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

namespace app\common\enum;

/**
 * 对话记录枚举类
 */
class ChatRecordEnum
{
    // 对话类型
    const CHAT_QUESTION = 1; //问答
    const CHAT_CREATION = 2; //创作
    const CHAT_SKILL    = 3; //角色

    const CHAT_MINDMAP  = 4; //思维导图

    // 审核状态
    const CENSOR_STATUS_WAIT           = 0; //未审核
    const CENSOR_STATUS_COMPLIANCE     = 1; //合规
    const CENSOR_STATUS_NON_COMPLIANCE = 2; //不合规
    const CENSOR_STATUS_SUSPECTED      = 3; //疑似
    const CENSOR_STATUS_FAIL           = 4; //审核失败


    const REPLYTYPE_MODEL      = 1;        //模型对话
    const REPLYTYPE_SYSTEM     = 2;        //系统

    /**
     * @notes 获取语音播报的保存路径
     * @param $type
     * @return string
     * @author cjhao
     * @date 2023/10/10 11:13
     */
    public static function getChatVoiceDir($type): string
    {
        $desc =  [
            self::CHAT_QUESTION => 'question',
            self::CHAT_CREATION => 'creation',
        ];
        return $desc[$type] ?? 'know';
    }

    /**
     * @notes 审核状态
     * @param $type
     * @return string
     * @author ljj
     * @date 2023/6/19 8:06 下午
     */
    public static function getCensorStatusDesc($type): string
    {
        $desc =  [
            self::CENSOR_STATUS_WAIT           => '未审核',
            self::CENSOR_STATUS_COMPLIANCE     => '合规',
            self::CENSOR_STATUS_NON_COMPLIANCE => '不合规',
            self::CENSOR_STATUS_SUSPECTED      => '疑似',
            self::CENSOR_STATUS_FAIL           => '审核失败'
        ];
        return $desc[$type] ?? '';
    }
}