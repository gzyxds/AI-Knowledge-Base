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

namespace app\adminapi\lists\video;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoStyle;

/**
 * 音乐风格
 * Class StyleLists
 * @package app\adminapi\lists\music
 */
class VideoStyleLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
{
    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/27 11:52
     */
    public function lists(): array
    {
        $lists = (new VideoStyle())
            ->where($this->searchWhere)
            ->withoutField('update_time,delete_time')
            ->order(['sort'=>'desc','id'=>'desc'])
            ->append(['status_desc'])
            ->select()
            ->toArray();

        foreach ($lists as $key => $item) {
            $styleId = $item['id'];
            $lists[$key]['relation_count'] = VideoRecord::where("find_in_set('$styleId', style_id)")->count();
        }

        return $lists;
    }

    /**
     * @notes 数量
     * @return int
     * @throws \think\db\exception\DbException
     * @author mjf
     * @date 2024/5/27 11:50
     */
    public function count(): int
    {
        return (new VideoStyle())
            ->where($this->searchWhere)
            ->count();
    }

    /**
     * @notes 搜素条件
     * @return array
     * @author mjf
     * @date 2024/5/27 11:44
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '='      => ['status']
        ];
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author mjf
     * @date 2024/5/27 11:45
     */
    public function setFileName(): string
    {
        return '风格列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author mjf
     * @date 2024/5/27 15:43
     */
    public function setExcelFields(): array
    {
        return [
            'name'              => '风格名称',
            'relation_count'    => '已被关联',
            'sort'              => '排序',
            'status_desc'       => '状态',
            'create_time'       => '创建时间'
        ];
    }
}