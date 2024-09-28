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

namespace app\adminapi\logic\draw;

use app\common\enum\draw\DrawEnum;
use app\common\model\draw\DrawPromptCategory;
use app\common\logic\BaseLogic;


/**
 * DrawPromptCategory逻辑
 * Class DrawPromptCategoryLogic
 * @package app\adminapi\logic\draw
 */
class DrawPromptCategoryLogic extends BaseLogic
{
    /**
     * @notes 列表
     * @param $params
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/14 17:01
     */
    public static function lists($params)
    {
        $where = [];
        if (!empty($params['name'])) {
            $where[] = ['name', 'like', '%' . $params['name'] . '%'];
        }
        if (isset($params['status']) && $params['status'] != '') {
            $where[] = ['status', '=', $params['status']];
        }

        $drawModel = !empty($params['model']) ? $params['model'] : DrawEnum::API_SD;
        $where[] = ['model', '=', $drawModel];

        $lists = DrawPromptCategory::where($where)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();

        $pid = 0;
        if (!empty($lists)) {
            $pid = min(array_column($lists, 'pid'));
        }
        return linear_to_tree($lists, "children", "id", "pid", $pid);
    }


    /**
     * @notes
     * @param array $params
     * @return DrawPromptCategory|\think\Model
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function add(array $params)
    {
        $level = 0;
        if ($params['pid']) {
            $level = DrawPromptCategory::where(['id' => $params['pid']])->value('level');
        }

        return DrawPromptCategory::create([
            'pid' => $params['pid'],
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'level' => $level + 1,
            'status' => $params['status'],
            'model' => !empty($params['model']) ? $params['model'] : DrawEnum::API_SD
        ]);
    }

    /**
     * @notes 编辑
     * @param array $params
     * @return DrawPromptCategory
     * @author 段誉
     * @date 2023/6/14 16:20
     */
    public static function edit(array $params)
    {
        $level = 0;
        if ($params['pid']) {
            $level = DrawPromptCategory::where(['id' => $params['pid']])->value('level');
        }

        return DrawPromptCategory::where('id', $params['id'])->update([
            'pid' => $params['pid'],
            'name' => $params['name'],
            'sort' => $params['sort'] ?? 0,
            'level' => $level + 1,
            'status' => $params['status']
        ]);
    }

    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function delete(array $params): bool
    {
        return DrawPromptCategory::destroy($params['id']);
    }

    /**
     * @notes 详情
     * @param $params
     * @return array
     * @author 段誉
     * @date 2023/6/14 16:21
     */
    public static function detail($params): array
    {
        return DrawPromptCategory::findOrEmpty($params['id'])->toArray();
    }

    /**
     * @notes 状态切换
     * @param int $id
     * @return bool
     * @author 段誉
     * @date 2023/6/15 10:56
     */
    public static function status(int $id)
    {
        $questionCategory = DrawPromptCategory::where(['id' => $id])->findOrEmpty();
        if ($questionCategory->isEmpty()) {
            return true;
        }
        $questionCategory->status = $questionCategory->status ? 0 : 1;
        $questionCategory->save();
        return true;
    }


    /**
     * @notes 全部列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:15
     */
    public static function all($params)
    {
        // 绘画模型
        $drawModel = !empty($params['model']) ? $params['model'] : DrawEnum::API_SD;

        return DrawPromptCategory::where(['status' => 1])
            ->where(['model' => $drawModel])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()
            ->toArray();
    }

}