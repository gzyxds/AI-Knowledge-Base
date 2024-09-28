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

namespace app\adminapi\logic\user;

use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\model\user\UserGroup;
use Exception;

/**
 * 用户分组逻辑类
 */
class UserGroupLogic extends BaseLogic
{
    /**
     * @notes 分组列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public static function lists(): array
    {
        $modelUser = new User();
        $modelUserGroup = new UserGroup();

        $lists = $modelUserGroup
            ->withoutField('delete_time')
            ->order('sort desc, id desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $id = $item['id'];
            $item['user_sum'] = $modelUser->where("find_in_set('$id', group_ids)")->count();
        }

        return $lists;
    }

    /**
     * @notes 分组详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        return (new UserGroup())
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();
    }

    /**
     * @notes 分组新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            if (empty($post['name'])) {
                throw new Exception('分组名称不能为空!');
            }

            $group = (new UserGroup())
                ->where(['name'=>$post['name']])
                ->findOrEmpty()
                ->toArray();

            if ($group) {
                throw new Exception('分组名称已存在,换一个吧!');
            }

            UserGroup::create([
                'name' => $post['name'],
                'sort' => $post['sort']??0
            ]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 分组编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            if (empty($post['name'])) {
                throw new Exception('分组名称不能为空!');
            }

            $group = (new UserGroup())
                ->where(['id'=>intval($post['id']??0)])
                ->findOrEmpty()
                ->toArray();

            if (!$group) {
                throw new Exception('分组不存在了!');
            }

            $groupE = (new UserGroup())
                ->where(['id'=>intval($post['id'])])
                ->where(['name'=>$post['name']])
                ->findOrEmpty()
                ->toArray();

            if ($groupE) {
                throw new Exception('分组名称已存在,换一个吧!');
            }

            UserGroup::update([
                'name' => $post['name'],
                'sort' => $post['sort']??0
            ], ['id'=>intval($post['id'])]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 分组删除
     * @param array $ids
     * @return bool
     * @author fzr
     */
    public static function del(array $ids): bool
    {
        try {
            if (empty($ids)) {
                throw new Exception('请选择要删除的数据!');
            }

            $modelUser = new User();
            foreach ($ids as $id) {
                $g = $modelUser->where("FIND_IN_SET('$id',group_ids)")->count();
                if ($g) {
                    throw new Exception('分组已被用户使用,不允许删除!');
                }
            }

            (new UserGroup())->whereIn('id', $ids)->update(['delete_time'=>time()]);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}