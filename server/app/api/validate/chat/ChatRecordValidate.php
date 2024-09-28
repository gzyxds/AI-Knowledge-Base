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

namespace app\api\validate\chat;

use app\common\enum\ChatRecordEnum;
use app\common\validate\BaseValidate;

class ChatRecordValidate extends BaseValidate
{
    protected $rule = [
        'user_id'       => 'require',
        'category_id'   => 'requireIf:type,'.ChatRecordEnum::CHAT_QUESTION.'|checkCategory',
        'type'          => 'require|in:'.ChatRecordEnum::CHAT_QUESTION.','.','.ChatRecordEnum::CHAT_CREATION.','.','.ChatRecordEnum::CHAT_SKILL.','.ChatRecordEnum::CHAT_MINDMAP,
        'records_id'    => 'require',
        'collect_id'    => 'require',
    ];

    protected $message = [
        'user_id.require'       => '参数缺失',
        'category_id.requireIf' => '请选择对话',
        'records_id.require'    => '参数缺失',
        'collect_id.require'    => '参数缺失'
    ];

    /**
     * @notes 对话清除场景
     * @return ChatRecordValidate
     */
    public function sceneChatClean(): ChatRecordValidate
    {
        return $this->only(['user_id', 'type']);
    }

    /**
     * @notes 收藏对话场景
     * @return ChatRecordValidate
     */
    public function sceneCollectCreate(): ChatRecordValidate
    {
        return $this->only(['user_id', 'records_id']);
    }

    /**
     * @notes 收藏取消场景
     * @return ChatRecordValidate
     */
    public function sceneCollectCancel(): ChatRecordValidate
    {
        return $this->only(['user_id','collect_id']);
    }

    /**
     * @notes 对话记录场景
     * @return ChatRecordValidate
     */
    public function sceneChatRecord(): ChatRecordValidate
    {
        return $this->only(['user_id','type']);
    }
}