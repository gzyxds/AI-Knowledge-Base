<?php
namespace app\api\logic;
use app\common\enum\MusicSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\enum\VideoEnum;
use app\common\enum\VideoSquareEnum;
use app\common\logic\BaseLogic;
use app\common\logic\NoticeLogic;
use app\common\model\square\SquareCategory;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoRecordsCollect;
use app\common\model\video\VideoSquare;
use app\common\model\video\VideoStyle;
use app\common\model\WorksShareLog;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;
use think\facade\Db;

class VideoSquareLogic extends BaseLogic
{
    /**
     * @notes 分类列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:22 下午
     */
    public function styleLists($userId)
    {
        $lists = VideoStyle::where(['status'=>1])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->withoutField('create_time,update_time,delete_time,status,sort')
            ->select()
            ->toArray();
        if ($userId) {
            array_unshift($lists,['id'=>0,'name'=>'收藏','image'=>FileService::getFileUrl('resource/image/adminapi/default/nav02.png')]);
        }

        return $lists;
    }

    /**
     * @notes 分享到视频广场
     * @param $params
     * @return string|true
     * @author cjhao
     * @date 2024/7/22 18:27
     */
    public function add($params,$userId,$terminal)
    {
        try {
            Db::startTrans();
            $recordsIds = $params['records_id']??'';
            if(empty($recordsIds)){
                throw new Exception('请选择音乐');
            }
            $isShare =  ConfigService::get('video_award', 'is_open');
            if (!$isShare) {
                throw new Exception('视频广场分享未开启，请联系管理员');
            }
            if($params['category_id']){
                $category = SquareCategory::where(['id'=>$params['category_id'],'type'=>3])->findOrEmpty();
                if($category->isEmpty()){
                    throw new Exception('分类不存在，请重新选择');
                }
            }
            $autoAudit = ConfigService::get('video_award', 'auto_audit');
            $oneAward = 0;
            $videoRecords = VideoRecord::where(['id'=>$params['records_id'],'user_id'=>$userId,'status'=>VideoEnum::STATUS_SUCCESS])
                ->field('style_id,prompt,image,video_url')
                ->findOrEmpty()->toArray();
            if(empty($videoRecords)){
                throw new Exception('视频记录不存在');
            }
            //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
            $videoSquare = VideoSquare::where(['operate_id'=>$userId,'source'=>VideoEnum::SOURCE_USER,'records_id'=>$recordsIds])
                ->findOrEmpty();
            if(!$videoSquare->isEmpty()){
                if(VideoSquareEnum::VERIFY_STATUS_SUCCESS == $videoSquare->verify_status){
                    throw new Exception('该视频已被分享至广场！');
                }
                if(VideoSquareEnum::VERIFY_STATUS_WAIT == $videoSquare->verify_status){
                    throw new Exception('审核中!');
                }
                if(VideoSquareEnum::VERIFY_STATUS_FAIL == $videoSquare->verify_status){
                    throw new Exception('审核失败，请重新分享其他作品!');
                }
            }
            if (1 == $autoAudit) {
                //奖励
                $oneAward = ConfigService::get('video_award', 'one_award');
                //每天最多分享多少次
                $dayNum   = ConfigService::get('video_award', 'day_num');
                $shareNum = VideoSquare::where(['operate_id'=>$userId,'source'=>VideoEnum::SOURCE_USER,'verify_status'=>1])
                    ->whereDay('create_time')
                    ->group('records_id')
                    ->count();
                if ($dayNum >= $shareNum  && $oneAward) {
                    User::update(['balance'=>['inc',$oneAward]],['id'=>$userId]);
                    // 记录账户流水
                    UserAccountLog::add(
                        $userId,
                        AccountLogEnum::UM_INC_VIDEO_SHARE,
                        AccountLogEnum::INC,
                        $oneAward
                    );
                    $unit = ConfigService::get('chat', 'price_unit', '电力值');
                    BaseLogic::$returnData = '分享成功,获取'.format_amount_zero($oneAward).$unit;

                }
            }

            $videoRecords['source'] = MusicSquareEnum::SOURCE_USER;
            $videoRecords['operate_id'] = $userId;
            $videoRecords['category_id'] =  $params['category_id'] ?? 0;
            $videoRecords['verify_status'] = $autoAudit;
            $videoRecords['is_show'] = $autoAudit;
            $videoRecords['records_id'] = $params['records_id'];

            $square = VideoSquare::create($videoRecords);

            WorksShareLog::create([
                'type'          => 3,
                'user_id'       => $userId,
                'work_id'       => $params['records_id'],
                'channel'       => $terminal,
                'balance'       => $oneAward,
                'square_id'     => $square->id,
            ]);
            if(1 == $autoAudit){
                //添加信息通知
                NoticeLogic::addSquareNotice(
                    $userId,
                    6,
                    1,
                    [
                        'square_id'     => $square->id,
                        'records_id'    => $params['records_id'],
                        'verify_status' => 1,
                        'verify_result' => '',
                        'balance'       => $oneAward
                    ]
                );


            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 视频详情
     * @param $id
     * @param $userId
     * @return array
     * @author cjhao
     * @date 2024/8/30 11:07
     */
    public function detail($id,$userId)
    {
        $recordsId = VideoSquare::where(['id'=>$id])
            ->value('records_id');
        $detail = VideoRecord::withTrashed()->where(['id'=>$recordsId])
            ->field(['id','prompt', 'type','user_id', 'tags', 'image','channel',
            'video_url', 'style_id','status', 'create_time'])
            ->order('id desc')
            ->append(['status_desc', 'style_desc', 'type_desc'])
            ->findOrEmpty()
            ->toArray();
        $detail['nickname'] = User::where(['id'=>$detail['user_id']])->value('nickname');
        $collect = VideoRecordsCollect::where(['square_id'=>$id,'user_id'=>$userId])
            ->findOrEmpty();
        $detail['is_collect'] =  $collect->isEmpty()? 0 : 1;
        return $detail;
    }


}