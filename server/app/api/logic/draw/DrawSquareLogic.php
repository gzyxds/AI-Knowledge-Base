<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\logic\draw;


use app\common\enum\DrawSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\NoticeLogic;
use app\common\model\draw\DrawModelCategory;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawRecordsCollect;
use app\common\model\draw\DrawSquare;
use app\common\model\square\SquareCategory;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\WorksShareLog;
use app\common\service\ConfigService;
use app\common\service\FileService;
use app\common\service\storage\Driver as StorageDriver;
use Exception;
use think\facade\Db;

class DrawSquareLogic extends BaseLogic
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
    public function categoryLists($userId)
    {
        $lists = DrawModelCategory::where(['status'=>1])
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
     * @notes 分享至绘画广场
     * @param $params
     * @return bool|string
     * @author ljj
     * @date 2023/8/31 4:59 下午
     */
    public function add($params)
    {
        try {
            Db::startTrans();
            $isShare =  ConfigService::get('draw_award', 'is_open');

            if (!$isShare) {
                throw new Exception('绘画广场分享未开启，请联系管理员');
            }
            if($params['category_id']){
                $category = SquareCategory::where(['id'=>$params['category_id'],'type'=>1])->findOrEmpty();
                if($category->isEmpty()){
                    throw new Exception('分类不存在，请重新选择');
                }
            }
            $autoAudit = ConfigService::get('draw_award', 'auto_audit');
            $drawRecords = DrawRecords::where(['id'=>$params['records_id']])->findOrEmpty()->toArray();
            $image =  $drawRecords['image'] ?? '';
            $imageUrl = $drawRecords['image_url'] ?? '';
            $thumbnail =  $drawRecords['thumbnail'] ?? '';
            $prompts =  $drawRecords['prompt_desc'] ?? '';

            $isSlice = 0;
            $isBase64 = $params['is_base64'] ?? '';
            $base64 = $params['base64'] ?? '';
            //切片分享，base64转图片保存
            if ($isBase64) {
                if(empty($base64)) {
                    throw new Exception('参数缺失');
                }
                if (strstr($base64,",")){
                    $base64 = explode(',',$base64);
                    $base64 = $base64[1];
                }

                // 存储引擎
                $config = [
                    'default' => ConfigService::get('storage', 'default', 'local'),
                    'engine' => ConfigService::get('storage'),
                ];
                $StorageDriver = new StorageDriver($config);
                //保存图片
                $saveDir = 'uploads/draw_square/';
                $filename = md5($base64).'.png';
                $fileUrl = $saveDir.$filename;
                //获取文件，如果之前生成过，不用重新生成
                if (($config['default'] == 'local' && !file_exists($fileUrl)) || ($config['default'] != 'local' && !getRemoteFileExists(FileService::getFileUrl($fileUrl)))) {
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0775, true);
                    }
                    //保存到本地
                    file_put_contents($fileUrl, base64_decode($base64));
                    //上传到oss
                    if('local' != $config['default']){
                        $localFileUrl = request()->domain(true).'/'.$fileUrl;
                        if (!$StorageDriver->fetch($localFileUrl,$fileUrl)) {
                            throw new \Exception('保存失败:' . $StorageDriver->getError());
                        }
                        //删除本地文件
                        unlink($fileUrl);
                    }
                }

                $image =  $fileUrl;
                $thumbnail =  (new DrawSquare())->getThumbnail(FileService::getFileUrl($fileUrl));
                $isSlice = 1;
            }

            //绘画记录image字段为空时，拿image_url服务器返回的图片保存下来
            if (empty($image) && $imageUrl) {
                // 存储引擎
                $config = [
                    'default' => ConfigService::get('storage', 'default', 'local'),
                    'engine' => ConfigService::get('storage'),
                ];
                $StorageDriver = new StorageDriver($config);
                //保存图片
                $saveDir = 'uploads/draw_square/';
                $filename = md5($imageUrl).'.png';
                $fileUrl = $saveDir.$filename;
                //获取文件，如果之前生成过，不用重新生成
                if (($config['default'] == 'local' && !file_exists($fileUrl)) || ($config['default'] != 'local' && !getRemoteFileExists(FileService::getFileUrl($fileUrl)))) {
                    if (!file_exists($saveDir)) {
                        mkdir($saveDir, 0775, true);
                    }
                    //保存到本地
                    $downloadResult = download_file($imageUrl, $saveDir, $filename, false);
                    if (empty($downloadResult)) {
                        throw new \Exception('图片下载失败,图片地址-' . $imageUrl);
                    }
                    //上传到oss
                    if('local' != $config['default']){
                        $localFileUrl = request()->domain(true).'/'.$fileUrl;
                        if (!$StorageDriver->fetch($localFileUrl,$fileUrl)) {
                            throw new \Exception('保存失败:' . $StorageDriver->getError());
                        }
                        //删除本地文件
                        unlink($fileUrl);
                    }
                }

                $image =  $fileUrl;
                $thumbnail =  (new DrawSquare())->getThumbnail(FileService::getFileUrl($fileUrl));
            }
            $oneAward = 0;
            //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
            $shareNum = DrawSquare::where(['operate_id'=>$params['user_id'],'source'=>DrawSquareEnum::SOURCE_USER,'records_id'=>$params['records_id'],'verify_status'=>1])
                    ->count();
            if (0 == $shareNum && 1 == $autoAudit) {
                //奖励
                $oneAward = ConfigService::get('draw_award', 'one_award');

                //每天最多分享多少次
                $dayNum   = ConfigService::get('draw_award', 'day_num');
                $shareNum = DrawSquare::where(['operate_id'=>$params['user_id'],'source'=>DrawSquareEnum::SOURCE_USER,'verify_status'=>1])
                    ->whereDay('create_time')
                    ->group('records_id')
                    ->count();
                if ($dayNum >= $shareNum  && $oneAward) {
                    User::update(['balance'=>['inc',$oneAward]],['id'=>$params['user_id']]);
                    // 记录账户流水
                    UserAccountLog::add(
                        $params['user_id'],
                        AccountLogEnum::UM_INC_DRAW_SHARE,
                        AccountLogEnum::INC,
                        $oneAward
                    );
                    $unit = ConfigService::get('chat', 'price_unit', '电力值');
                    BaseLogic::$returnData = '分享成功,获取'.format_amount_zero($oneAward).$unit;
                }
            }


            $square = DrawSquare::create([
                'source' => DrawSquareEnum::SOURCE_USER,
                'operate_id' => $params['user_id'],
                'category_id' => $params['category_id'] ?? 0,
                'prompts' => $prompts,
                'image' => $image,
                'thumbnail' => $thumbnail,
                'verify_status' => $autoAudit,
                'is_show' => $autoAudit,
                'is_slice' => $isSlice,
                'records_id' => $params['records_id'],
            ]);

            WorksShareLog::create([
                'type'          => 1,
                'user_id'       => $params['user_id'],
                'work_id'       => $params['records_id'],
                'channel'       => $params['terminal'],
                'balance'       => $oneAward,
                'square_id'     => $square->id,
            ]);
            if(1 == $autoAudit){
                //添加信息通知
                NoticeLogic::addSquareNotice(
                    $params['user_id'],
                    4,
                    1,
                    [
                        'square_id'     => $square->id,
                        'records_id'    => $params['records_id'],
                        'verify_status' => 1,
                        'verify_result' => '',
                        'balance'       => $oneAward,
                        'model'         => $drawRecords['model']
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
     * @notes 点赞操作
     * @param $params
     * @return bool
     * @author ljj
     * @date 2024/1/25 6:14 下午
     */
    public function praise($params)
    {
        if ($params['praise'] == 1) {
            DrawSquarePraise::create([
                'square_id' => $params['id'],
                'user_id' => $params['user_id'],
            ]);
            return '收藏成功';
        } else {
            DrawSquarePraise::where(['square_id'=>$params['id'],'user_id'=>$params['user_id']])->delete();
            return '取消成功';
        }
    }

    /**
     * @notes 绘画详情
     * @param $id
     * @param $userId
     * @return DrawRecords|array|mixed|\think\Model
     * @author cjhao
     * @date 2024/8/30 11:16
     */
    public function detail($id,$userId)
    {
        $record = DrawSquare::where(['id'=>$id])->findOrEmpty();
        $draw = DrawRecords::withTrashed()->where(['id'=>$record->records_id])
            ->withoutField('notify_snap,status,censor_status,update_time,delete_time')
            ->findOrEmpty();
        if ($record->is_slice) {
            $draw->image = FileService::getFileUrl($record['image']);
            $draw->thumbnail = FileService::getFileUrl($record['thumbnail']);
        }
        $draw->nickname = User::where(['id'=>$draw['user_id']])->value('nickname');
        $collect = DrawRecordsCollect::where(['square_id'=>$id,'user_id'=>$userId])
            ->findOrEmpty();
        $draw->is_collect =  $collect->isEmpty()? 0 : 1;
        return $draw->toArray();
    }


}