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

namespace app\api\lists\chat;

use app\api\lists\BaseApiDataLists;
use app\common\model\chat\ChatRecordCollect;

class ChatRecordCollectLists extends BaseApiDataLists
{
    /**
     * @notes 收藏会话列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author ljj
     */
    public function lists(): array
    {
        $model = new ChatRecordCollect();
        return $model
            ->field('id,records_id,create_time')
            ->with(['chat_records'])
            ->where(['user_id'=>$this->userId])
            ->order('id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
    }

    /**
     * @notes 收藏会话数量
     * @return int
     * @throws @\think\db\exception\DbException
     * @author ljj
     */
    public function count(): int
    {
        $model = new ChatRecordCollect();
        return $model->where(['user_id'=>$this->userId])->count();
    }
}