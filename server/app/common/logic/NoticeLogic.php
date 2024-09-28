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

namespace app\common\logic;

use app\common\enum\notice\NoticeEnum;
use app\common\enum\YesNoEnum;
use app\common\model\notice\NoticeRecord;
use app\common\model\notice\NoticeSetting;
use app\common\model\user\User;
use app\common\service\sms\SmsMessageService;
use Exception;
use think\Model;

/**
 * 通知逻辑层
 * Class NoticeLogic
 * @package app\common\logic
 */
class NoticeLogic extends BaseLogic
{
    /**
     * @notes 根据场景发送短信
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/15 15:28
     */
    public static function noticeByScene($params): bool
    {
        try {
            $noticeSetting = (new NoticeSetting())->where('scene_id', $params['scene_id'])->findOrEmpty()->toArray();
            if (empty($noticeSetting)) {
                throw new Exception('找不到对应场景的配置');
            }
            // 合并额外参数
            $params = self::mergeParams($params);
            $res = false;
            self::setError('发送通知失败');

            // 短信通知
            if (isset($noticeSetting['sms_notice']['status']) && $noticeSetting['sms_notice']['status'] == YesNoEnum::YES) {
                $res = (new SmsMessageService())->send($params);
            }

            return $res;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 整理参数
     * @param $params
     * @return array
     * @author 段誉
     * @date 2022/9/15 15:28
     */
    public static function mergeParams($params): array
    {
        // 用户相关
        if (!empty($params['params']['user_id'])) {
            $user = (new User())->findOrEmpty($params['params']['user_id'])->toArray();
            $params['params']['nickname']  = $user['nickname'];
            $params['params']['user_name'] = $user['nickname'];
            $params['params']['user_sn']   = $user['sn'];
            $params['params']['mobile']    = $params['params']['mobile'] ?? $user['mobile'];
        }

        // 跳转路径
        $jumpPath = self::getPathByScene($params['scene_id'], $params['params']['order_id'] ?? 0);
        $params['url'] = $jumpPath['url'];
        $params['page'] = $jumpPath['page'];
        return $params;
    }

    /**
     * @notes 根据场景获取跳转链接
     * @param $sceneId
     * @param $extraId
     * @return string[]
     * @author 段誉
     * @date 2022/9/15 15:29
     */
    public static function getPathByScene($sceneId, $extraId): array
    {
        // 小程序主页路径
        $page = '/pages/index/index';
        // 公众号主页路径
        $url = '/mobile/pages/index/index';
        return [
            'url'  => $url,
            'page' => $page,
        ]??[];
    }

    /**
     * @notes 替换消息内容中的变量占位符
     * @param $content
     * @param $params
     * @return mixed
     * @author 段誉
     * @date 2022/9/15 15:29
     */
    public static function contentFormat($content, $params): mixed
    {
        foreach ($params['params'] as $k => $v) {
            $search = '{' . $k . '}';
            $content = str_replace($search, $v, $content);
        }
        return $content;
    }

    /**
     * @notes 添加通知记录
     * @param $params
     * @param $noticeSetting
     * @param $sendType
     * @param $content
     * @param string $extra
     * @return NoticeRecord|Model
     * @author 段誉
     * @date 2022/9/15 15:29
     */
    public static function addNotice($params, $noticeSetting, $sendType, $content, string $extra = ''): NoticeRecord|Model
    {
        return NoticeRecord::create([
            'user_id'     => $params['params']['user_id'] ?? 0,
            'title'       => self::getTitleByScene($sendType, $noticeSetting),
            'content'     => $content,
            'scene_id'    => $noticeSetting['scene_id'],
            'read'        => YesNoEnum::NO,
            'recipient'   => $noticeSetting['recipient'],
            'send_type'   => $sendType,
            'notice_type' => $noticeSetting['type'],
            'extra'       => $extra
        ]);
    }

    /**
     * @notes 通知记录标题
     * @param $sendType
     * @param $noticeSetting
     * @return string
     * @author 段誉
     * @date 2022/9/15 15:30
     */
    public static function getTitleByScene($sendType, $noticeSetting): string
    {
        return match ($sendType) {
            NoticeEnum::OA  => $noticeSetting['oa_notice']['name'] ?? '',
            NoticeEnum::MNP => $noticeSetting['mnp_notice']['name'] ?? '',
            default => '',
        };
    }


    /**
     * @notes 添加广场审核通知
     * @param int $userId
     * @param int $noticeType
     * @param int $subType
     * @param array $extra
     * @param string $content
     * @return NoticeRecord|Model
     * @author cjhao
     * @date 2024/8/6 15:06
     */
    public static function addSquareNotice(int $userId,int $noticeType,int $subType,array $extra,string $content = '')
    {
        $title = '';
        $sceneId = NoticeEnum::SQUARE_NOTICE;
        switch ($noticeType){
            case 4:
                $title = '绘画分享审核通知';
                break;
            case 5:
                $title = '音乐分享审核通知';
                break;
            case 6:
                $title = '视频分享审核通知';
                break;
            case 7:
                $title = '机器人分享审核通知';
                break;

        }
        return NoticeRecord::create([
            'user_id'           => $userId,
            'title'             => $title,
            'content'           => $content,
            'read'              => YesNoEnum::NO,
            'recipient'         => 1,
            'scene_id'          => $sceneId,
            'send_type'         => 1,
            'notice_type'       => $noticeType,
            'notice_sub_type'   => $subType,
            'extra'             => $extra ? json_encode($extra,JSON_UNESCAPED_UNICODE):'',
        ]);

    }
}