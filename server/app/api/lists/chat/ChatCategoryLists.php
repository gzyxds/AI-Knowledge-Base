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
use app\common\model\chat\ChatRecordCategory;
use app\common\model\chat\ChatRecordCollect;

/**
 * 对话分类列表
 */
class ChatCategoryLists extends BaseApiDataLists
{
    /**
     * @notes 分类列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function lists(): array
    {
        $this->initCategory();
        $lists = (new ChatRecordCategory())
            ->where($this->setSearchWhere())
            ->field('id,name')
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select();

        return $lists->toArray();
    }

    /**
     * @notes 分类统计
     * @return int
     * @throws @\think\db\exception\DbException
     */
    public function count(): int
    {
        return  (new ChatRecordCategory())->where($this->setSearchWhere())->count();
    }

    /**
     * @notes 搜索条件
     * @return array
     */
    public function setSearchWhere(): array
    {
        $where = [];
        $where[] = ['user_id','=',$this->userId];
        return $where;

    }

    /**
     * @notes 初始化一个会话
     * @throws @\think\db\exception\DbException
     */
    public function initCategory()
    {
        $count = (new ChatRecordCategory())->where($this->setSearchWhere())->count();
        if (0 == $count) {
            //创建一个默认会话
            $chatCategory = new ChatRecordCategory();
            $chatCategory->save(['name'=>'新的会话', 'user_id'=>$this->userId]);
            (new ChatRecordCollect())->where(['user_id'=>$this->userId])->update(['category_id'=>$chatCategory->id??0]);
        }
    }
}