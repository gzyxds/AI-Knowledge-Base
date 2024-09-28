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
use app\common\enum\ChatRecordEnum;
use app\common\lists\ListsExcelInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\chat\ChatRecord;
use app\common\model\creation\CreationModel;

/**
 * 创建模型列表类
 */
class CreationModelLists extends BaseAdminDataLists implements ListsSearchInterface, ListsExcelInterface
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
        $creationModelLists = (new CreationModel())
            ->alias('CM')
            ->where($this->searchWhere)
            ->join('creation_category CC','CC.id = CM.category_id')
            ->field('CM.*,CC.name as category_name')
            ->order(['CM.sort'=>'desc','CM.id'=>'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        $ids = array_column($creationModelLists,'id');
        $modelChatRecord = new ChatRecord();

        // 访问数据: 所有
        $creationAllUseCount = $modelChatRecord
            ->where(['type'=>ChatRecordEnum::CHAT_CREATION,'other_id'=>$ids])
            ->group('other_id')
            ->column('count(id) as count','other_id');

        // 访问数据: 当天
        $creationDayUseCount = $modelChatRecord
            ->where(['type'=>ChatRecordEnum::CHAT_CREATION,'other_id'=>$ids])
            ->whereDay('create_time')
            ->group('other_id')
            ->column('count(id) as count','other_id');

        // 使用人数: 所有
        $creationAllUserCount = $modelChatRecord
            ->where(['other_id'=>$ids,'type'=> ChatRecordEnum::CHAT_CREATION])
            ->group('other_id')
            ->column('COUNT(DISTINCT user_id) AS user_count','other_id');

        // 使用人数: 当天
        $creationDayUserCount = $modelChatRecord
            ->where(['other_id'=>$ids,'type'=> ChatRecordEnum::CHAT_CREATION])
            ->whereDay('create_time')
            ->group('other_id')
            ->column('COUNT(DISTINCT user_id) AS user_count','other_id');

        foreach ($creationModelLists as $key => $list) {
            $creationModelLists[$key]['status_desc'] = $list['status'] == 1 ? '开启' : '关闭';
            $creationModelLists[$key]['all_use_count']  = $creationAllUseCount[$list['id']] ?? 0;
            $creationModelLists[$key]['day_use_count']  = $creationDayUseCount[$list['id']] ?? 0;
            $creationModelLists[$key]['all_user_count'] = ($creationAllUserCount[$list['id']] ?? 0) + (empty($list['virtual_use_num']) ? 0 : $list['virtual_use_num']);
            $creationModelLists[$key]['day_user_count'] = $creationDayUserCount[$list['id']] ?? 0;
        }

        return $creationModelLists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        return (new CreationModel())
            ->alias('CM')
            ->where($this->searchWhere)
            ->join('creation_category CC','CC.id = CM.category_id')
            ->count();
    }

    /**
     * @notes 条件
     * @return array
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['CM.name'],
            '='      => ['CM.status','CM.category_id']
        ]??[];
    }

    /**
     * @notes 导出名称
     * @return string
     * @author fzr
     */
    public function setFileName(): string
    {
        return '创作模型列表';
    }

    /**
     * @notes 导出字段
     * @return array
     * @author fzr
     */
    public function setExcelFields(): array
    {
        return [
            'name'          => '模型名称',
            'content'       => '模型描述',
            'category_name' => '所属类目',
            'status_desc'   => '状态',
            'sort'          => '排序',
            'create_time'   => '创建时间'
        ]??[];
    }
}