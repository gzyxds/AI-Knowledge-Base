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

namespace app\adminapi\lists\setting;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\SensitiveWord;

/**
 * 敏感词列表
 */
class SensitiveWordLists extends BaseAdminDataLists
{
    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        return (new SensitiveWord())
            ->where($this->setSearch())
            ->withoutField('update_time,delete_time')
            ->append(['word_arr'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id'=>'desc'])
            ->select()->toArray();
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        return (new SensitiveWord())->where($this->setSearch())->count();
    }

    /**
     * @notes 条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        $where = [];
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['word','like','%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['status']) && $this->params['status'] != '') {
            $where[] = ['status','=',$this->params['status']];
        }

        return $where;
    }
}