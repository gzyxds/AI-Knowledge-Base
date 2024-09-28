<?php
namespace app\api\logic;
use app\common\enum\MusicEnum;
use app\common\enum\MusicSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\NoticeLogic;
use app\common\model\music\MusicRecord;
use app\common\model\music\MusicRecordsCollect;
use app\common\model\music\MusicSquare;
use app\common\model\music\MusicStyle;
use app\common\model\square\SquareCategory;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\WorksShareLog;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;
use think\facade\Db;

class

MusicSquareLogic extends BaseLogic
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
        $lists = MusicStyle::where(['status'=>1])
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
     * @notes 分享到音乐广场
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
            $isShare =  ConfigService::get('music_award', 'is_open');
            if (!$isShare) {
                throw new Exception('音乐广场分享未开启，请联系管理员');
            }
            if($params['category_id']){
                $category = SquareCategory::where(['id'=>$params['category_id'],'type'=>2])->findOrEmpty();
                if($category->isEmpty()){
                    throw new Exception('分类不存在，请重新选择');
                }
            }
            $musicRecords = MusicRecord::where(['id'=>$params['records_id'],'user_id'=>$userId,'status'=>MusicEnum::STATUS_SUCCESS])
                ->field('style_id,title,audio_url,image_url,image_large_url,lyric,duration')
                ->findOrEmpty();
            $duration = $musicRecords->getData('duration');
            if($musicRecords->isEmpty()){
                throw new Exception('音乐记录不存在');
            }

            $musicSquare = MusicSquare::where(['operate_id'=>$userId,'source'=>MusicSquareEnum::SOURCE_USER,'records_id'=>$recordsIds])
                ->findOrEmpty();
            if(!$musicSquare->isEmpty()){
                if(MusicSquareEnum::VERIFY_STATUS_SUCCESS == $musicSquare->verify_status){
                    throw new Exception('该音乐已被分享至广场！');
                }
                if(MusicSquareEnum::VERIFY_STATUS_WAIT == $musicSquare->verify_status){
                    throw new Exception('审核中!');
                }
                if(MusicSquareEnum::VERIFY_STATUS_FAIL == $musicSquare->verify_status){
                    throw new Exception('审核失败，请重新分享其他作品!');
                }
            }
            //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
//            $shareNum = MusicSquare::where(['operate_id'=>$userId,'source'=>MusicSquareEnum::SOURCE_USER,'records_id'=>$recordsIds])
//                ->count();
            $autoAudit = ConfigService::get('music_award', 'auto_audit');
            $oneAward = 0;
            if (1 == $autoAudit) {
                //奖励
                $oneAward = ConfigService::get('music_award', 'one_award');
                //每天最多分享多少次
                $dayNum   = ConfigService::get('music_award', 'day_num');
                $shareNum = MusicSquare::where(['operate_id'=>$userId,'source'=>MusicSquareEnum::SOURCE_USER,'verify_status'=>1])
                    ->whereDay('create_time')
                    ->group('records_id')
                    ->count();
                if ($dayNum >= $shareNum  && $oneAward) {
                    User::update(['balance'=>['inc',$oneAward]],['id'=>$userId]);
                    // 记录账户流水
                    UserAccountLog::add(
                        $userId,
                        AccountLogEnum::UM_INC_MUSIC_SHARE,
                        AccountLogEnum::INC,
                        $oneAward
                    );
                    $unit = ConfigService::get('chat', 'price_unit', '电力值');
                    BaseLogic::$returnData = '分享成功,获取'.format_amount_zero($oneAward).$unit;
                }
            }
            $musicRecords = $musicRecords->toArray();
            $musicRecords['duration'] = $duration;
            $musicRecords['source'] = MusicSquareEnum::SOURCE_USER;
            $musicRecords['category_id'] =  $params['category_id'] ?? 0;
            $musicRecords['operate_id'] =  $userId;
            $musicRecords['verify_status'] = $autoAudit;
            $musicRecords['is_show'] = $autoAudit;
            $musicRecords['records_id'] = $params['records_id'];

            $square = MusicSquare::create($musicRecords);
            WorksShareLog::create([
                'type'          => 2,
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
                    5,
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
     * @notes 推荐列表（按收藏人数从多到少排序，如果没有收藏的，按广场列表从最新的开始排序，拿10条数据）
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/8/14 16:55
     */
    public  function recommendLits($userId)
    {
        $recordsLists = MusicRecordsCollect::alias('MRC')
            ->join('music_square MS','MS.id = MRC.square_id')
            ->where(['MS.verify_status'=>1,'MS.is_show'=>1])
            ->field('count(records_id) as num,records_id')
            ->group('MS.records_id')
            ->orderRaw('num desc')
            ->select()->toArray();

        $fieldSetList = [];
        $recordsIds = array_column($recordsLists,'records_id');
        if($recordsIds){
            $fieldSetList = MusicRecord::alias('MR')
                ->where(['MR.id'=>$recordsIds])
                ->field(['MR.id','MR.user_id','MR.title','MR.prompt','MR.tags','MR.image_url','MR.image_large_url',
                    'MR.video_url', 'MR.audio_url', 'MR.lyric', 'MR.custom_mode', 'MR.make_instrumental','MR.duration',
                    'MR.style_id','MR.status', 'MR.create_time','MS.id as square_id'])
                ->join('music_square MS','MS.records_id = MR.id')
                ->append(['style_desc'])
                ->orderRaw('FIELD(MR.id,' . implode(',', $recordsIds) . ')')
                ->limit(10)
                ->select()
                ->toArray();
        }
        $surplusNum = 10 - count($fieldSetList);
        $sortList = [];
        if($surplusNum > 0){
            $sortList = MusicRecord::alias('MR')
                ->where(['MS.verify_status'=>1,'MS.is_show'=>1])
                ->whereNotIn('MR.id',$recordsIds)
                ->field(['MR.id','MR.user_id','MR.title','MR.prompt','MR.tags','MR.image_url','MR.image_large_url',
                    'MR.video_url', 'MR.audio_url', 'MR.lyric', 'MR.custom_mode', 'MR.make_instrumental','MR.duration',
                    'MR.style_id','MR.status', 'MR.create_time','MS.id as square_id'])
                ->join('music_square MS','MS.records_id = MR.id')
                ->order('MS.id desc')
                ->append(['style_desc'])
                ->limit($surplusNum)
                ->select()
                ->toArray();

        }
        $commentLists = array_merge($fieldSetList,$sortList);
        $userIds = array_column($commentLists,'user_id');
        $userLists = [];
        if($userIds){
            $userLists = User::where(['id'=>$userIds])
                ->field('id,avatar,nickname')
                ->select()->toArray();
            $userLists = array_column($userLists,null,'id');
        }
        $recordsId = MusicRecordsCollect::alias('MR')
            ->where(['MR.user_id'=>$userId])
            ->join('music_square MS','MR.square_id = MS.id')
            ->column('MS.records_id') ?: [];
        foreach ($commentLists as $key => $value){
            $commentLists[$key]['avatar'] = $userLists[$value['user_id']]['avatar'] ?? '';
            $commentLists[$key]['nickname'] = $userLists[$value['user_id']]['nickname'] ?? '';
            $commentLists[$key]['is_collect'] = 0;
            if(in_array($value['id'],$recordsId)){
                $commentLists[$key]['is_collect'] = 1;
            }
        }
        return $commentLists;
    }

    /**
     * @notes 获取详情
     * @param $id
     * @return MusicSquare|array|mixed|\think\Model
     * @author cjhao
     * @date 2024/8/29 17:46
     */
    public function detail($id,$userId)
    {
        $recordsId = MusicSquare::where(['id'=>$id])->value('records_id');
        $detail = MusicRecord::withTrashed()->where(['id'=>$recordsId])
            ->field('title,audio_url,image_url,lyric,duration,style_id,tags')
            ->append(['style_desc'])
            ->findOrEmpty()
            ->toArray();
        $collect = MusicRecordsCollect::where(['square_id'=>$id,'user_id'=>$userId])
            ->findOrEmpty();

        $detail['id'] =  $id;
        $detail['is_collect'] =  $collect->isEmpty()? 0 : 1;
        return $detail;
    }


}