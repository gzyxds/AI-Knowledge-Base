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

namespace app\adminapi\lists\creation;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\creation\CreationCategory;

/**
 * 创作类别类列表
 */
class CreationCategoryLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
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
        $creationCategoryLists = (new CreationCategory())
            ->where($this->searchWhere)
            ->withCount('model')
            ->withoutField('update_time,delete_time')
            ->order(['sort'=>'desc','id'=>'asc'])
            ->select()
            ->toArray();

        foreach ($creationCategoryLists as $key =>  $list) {
            $creationCategoryLists[$key]['status_desc'] = $list['status'] == 1 ? '开启' : '关闭';
        }

        return $creationCategoryLists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     */
    public function count(): int
    {
        return (new CreationCategory())->where($this->searchWhere)->count();
    }

    /**
     * @notes 设置搜索
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '='      => ['status']
        ]??[];
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author fzr
     */
    public function setFileName(): string
    {
        return '创作分类列表';
    }

    /**
     * @notes 导出字段
     * @return array
     * @author fzr
     */
    public function setExcelFields(): array
    {
        return [
            'name'        => '类别名称',
            'model_count' => '模型关联',
            'status_desc' => '状态',
            'sort'        => '排序',
            'create_time' => '创建时间'
        ] ?? [];
    }
}