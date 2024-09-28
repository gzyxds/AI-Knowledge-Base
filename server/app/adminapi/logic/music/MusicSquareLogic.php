<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\music;

use app\common\enum\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\logic\NoticeLogic;
use app\common\model\draw\DrawSquare;
use app\common\model\music\MusicSquare;
use app\common\model\square\SquareCategory;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;
use app\common\model\WorksShareLog;
use app\common\service\ConfigService;
use Exception;
use think\facade\Db;

class MusicSquareLogic extends BaseLogic
{
    /**
     * @notes 添加
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:14 下午
     */
    public static function add($params)
    {
        $thumbnail = (new DrawSquare())->getThumbnail($params['image']);
        MusicSquare::create([
            'source' => DrawSquareEnum::SOURCE_ADMIN,
            'operate_id' => $params['admin_id'],
            'category_id' => $params['category_id'] ?? 0,
            'prompts' => $params['prompts'],
            'prompts_cn' => $params['prompts_cn'] ?? null,
            'image' => $params['image'],
            'thumbnail' => $thumbnail,
            'is_show' => $params['is_show'],
            'verify_status' => DrawSquareEnum::VERIFY_STATUS_SUCCESS,
            'avatar' => $params['avatar'] ?? null,
            'nickname' => $params['nickname'] ?? null,
        ]);

        return true;
    }

    /**
     * @notes 编辑
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public static function edit($params)
    {
        $musicSquare = MusicSquare::where('id',$params['id'])->findOrEmpty()->toArray();
        if ($musicSquare['source'] == MusicSquareEnum::SOURCE_ADMIN) {
            DrawSquare::update([
                'category_id' => $params['category_id'] ?? 0,
                'title' => $params['title'],
                'audio_url' => $params['audio_url'] ?? null,
                'image_url' => $params['image_url'],
                'lyric' => $params['lyric'],
                'duration' => $params['duration'],
                'is_show' => $params['is_show'],
            ],['id'=>$params['id']]);
        }
        if ($musicSquare['source'] == MusicSquareEnum::SOURCE_USER) {
            MusicSquare::update([
                'category_id' => $params['category_id'] ?? 0,
                'is_show' => $params['is_show'],
            ],['id'=>$params['id']]);
        }

        return true;
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public static function detail($params)
    {
        $result = MusicSquare::field('*')
            ->append(['category_name','verify_status_desc','user_info','source_desc'])
            ->findOrEmpty($params['id'])->toArray();
        if ($result['category_id'] == 0) {
            $result['category_id'] = '';
        }
        return $result;
    }

    /**
     * @notes 删除
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:17 下午
     */
    public static function del($params)
    {
        return MusicSquare::destroy($params['id']);
    }

    /**
     * @notes 显示状态
     * @param $id
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:18 下午
     */
    public static function isShow($id)
    {
        $result = MusicSquare::where(['id' => $id])->findOrEmpty();
        if ($result->isEmpty()) {
            return true;
        }
        $result->is_show = $result->is_show ? 0 : 1;
        $result->save();
        return true;
    }

    /**
     * @notes 审核状态
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 12:19 下午
     */
    public static function verifyStatus($params)
    {
        try {
            Db::startTrans();
            foreach ($params['id'] as $id) {
                $musicSquare = MusicSquare::where(['id'=>$id])->findOrEmpty()->toArray();
                if(empty($musicSquare)){
                    continue;
                }
                //分享奖励，同一条绘画记录已分享过的不再奖励   通过审核在发放奖励
                $shareNum = MusicSquare::where([
                    'operate_id'=>$musicSquare['operate_id'],
                    'source'=>MusicSquareEnum::SOURCE_USER,
                    'records_id'=>$musicSquare['records_id'],
                    'verify_status'=>1])
                    ->count();
                if( 0 == $shareNum  && 1 == $params['verify_status']){
                    $rewardsConfig = [
                        'rewards' => ConfigService::get('music_award', 'one_award'),
                        'day_num' => ConfigService::get('music_award','day_num'),
                    ];

                    $shareNum = MusicSquare::where([
                        'operate_id'=>$musicSquare['operate_id'],
                        'source'=>MusicSquareEnum::SOURCE_USER,'verify_status'=>1])
                        ->whereDay('create_time')
                        ->group('records_id')
                        ->count();
                    if ($shareNum < $rewardsConfig['day_num'] && $rewardsConfig['rewards'] > 0) {
                        User::update(['balance'=>['inc',$rewardsConfig['rewards']]],['id'=>$musicSquare['operate_id']]);
                        // 记录账户流水
                        UserAccountLog::add(
                            $musicSquare['operate_id'],
                            AccountLogEnum::UM_INC_MUSIC_SHARE,
                            AccountLogEnum::INC,
                            $rewardsConfig['rewards']);
                        (new WorksShareLog())
                            ->where(['square_id'=>$musicSquare['id']])
                            ->update(['balance'=>$rewardsConfig['rewards']]);

                    }
                }
                MusicSquare::update([
                    'verify_status' => $params['verify_status'],
                    'verify_result' => $params['verify_result'],
                    'is_show' => $params['verify_status'] == 1 ? 1 : 0,
                ],['id'=>$id]);

                //添加信息通知
                NoticeLogic::addSquareNotice(
                    $musicSquare['operate_id'],
                    5,
                    $params['verify_status'],
                    [
                        'square_id'     => $musicSquare['id'],
                        'records_id'    => $musicSquare['records_id'],
                        'verify_status' => $params['verify_status'],
                        'verify_result' => $params['verify_result'],
                        'balance'       => $rewardsConfig['rewards'] ?? 0,
                    ]
                );
            }

            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @notes 获取绘画广场配置
     * @return array
     * @author ljj
     * @date 2023/8/31 2:49 下午
     */
    public static function getConfig()
    {
        return [
//            // 允许用户分享：1-开启；0-关闭；
//            'is_allow_share' => ConfigService::get('draw_square_config', 'is_allow_share', config('project.draw_square_config.is_allow_share')),
//            // 自动通过审核：1-开启；0-关闭；
//            'is_auto_pass' => ConfigService::get('draw_square_config', 'is_auto_pass', config('project.draw_square_config.is_auto_pass')),
            // 显示用户信息：1-开启；0-关闭；
            'is_show_user' => ConfigService::get('music_award', 'is_show_user'),
//            //分享奖励对话次数
//            'chat_rewards' => ConfigService::get('draw_square_config','chat_rewards', config('project.draw_square_config.chat_rewards')),
//            //分享奖励绘画次数
//            'draw_rewards' => ConfigService::get('draw_square_config','draw_rewards', config('project.draw_square_config.draw_rewards')),
//            //每天最多分享次数
//            'max_share' => ConfigService::get('draw_square_config','max_share', config('project.draw_square_config.max_share')),
        ];
    }

    /**
     * @notes 设置绘画广场配置
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/8/31 2:50 下午
     */
    public static function setConfig($params)
    {
//        ConfigService::set('draw_square_config', 'is_allow_share', $params['is_allow_share']);
//        ConfigService::set('draw_square_config', 'is_auto_pass', $params['is_auto_pass']);
        ConfigService::set('music_award', 'is_show_user', $params['is_show_user']);
//        ConfigService::set('draw_square_config', 'chat_rewards', $params['chat_rewards']);
//        ConfigService::set('draw_square_config', 'draw_rewards', $params['draw_rewards']);
//        ConfigService::set('draw_square_config', 'max_share', $params['max_share']);
        return true;
    }

    /**
     * @notes 移动分类
     * @param $params
     * @return string|true
     * @author cjhao
     * @date 2024/8/8 17:50
     */
    public static function removeCategory($params){
        $ids = $params['ids'] ?? [];
        $categoryId = $params['category_id'] ?? '';
        if(empty($ids)) {
            return '请选择记录';
        }
        if(empty($categoryId)){
            return '请选择分类';
        }
        $category = SquareCategory::where(['id'=>$categoryId,'type'=>2])->findOrEmpty();
        if($category->isEmpty()){
            return '分类不存在,请重新选择';
        }
        MusicSquare::where(['id'=>$ids])->update(['category_id'=>$categoryId]);
        return true;
    }
}
