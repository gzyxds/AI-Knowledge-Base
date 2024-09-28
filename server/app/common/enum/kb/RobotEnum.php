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

namespace app\common\enum\kb;

class RobotEnum
{
    // 分享类型
    const SHARE_TYPE_WEB = 1; // 网页
    const SHARE_TYPE_JS  = 2; // JS
    const SHARE_TYPE_OA  = 3; // 公众号
    const SHARE_TYPE_API = 4; // API
    const SHARE_TYPE_QWX = 5; // 企业微信
    const SHARE_TYPE_WX  = 6; // 个人微信

    // 空回复类型
    const EMPTY_ANSWER_AI   = 1; // 检索为空: AI回复
    const EMPTY_ANSWER_NULL = 2; // 检索为空: 指定文本

    /**
     * @notes 获取密钥前缀
     * @param bool|string $from
     * @return array|string
     */
    public static function getSecretPrefix(bool|string $from = true): array|string
    {
        $desc = [
            self::SHARE_TYPE_WEB => 'web',
            self::SHARE_TYPE_JS  => 'js',
            self::SHARE_TYPE_OA  => 'oa',
            self::SHARE_TYPE_API => 'api',
            self::SHARE_TYPE_QWX => 'qwx',
            self::SHARE_TYPE_WX  => 'wx'
        ];
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 获取密钥前缀
     * @param bool|string $from
     * @return array|string
     */
    public static function getSecretDesc(bool|string|int $from = true): array|string
    {
        if (is_numeric($from)) {
            $desc = [
                self::SHARE_TYPE_WEB => '网页',
                self::SHARE_TYPE_JS  => 'JS',
                self::SHARE_TYPE_API => 'API',
                self::SHARE_TYPE_OA  => '公众号',
                self::SHARE_TYPE_QWX => '企业微信',
                self::SHARE_TYPE_WX  => '个人微信'
            ];
        } else {
            $desc = [
                'web' => '网页',
                'js'  => 'JS',
                'api' => 'API',
                'oa'  => '公众号',
                'qwx' => '企业微信',
                'wx'  => '个人微信'
            ];
        }
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}