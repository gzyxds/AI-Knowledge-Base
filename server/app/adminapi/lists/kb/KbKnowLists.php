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

namespace app\adminapi\lists\kb;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\kb\KbKnow;
use app\common\service\FileService;

/**
 * 知识库列表
 */
class KbKnowLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     */
    public function lists(): array
    {
        $model = new KbKnow();

        // 临时代码,同步创建者
        if (empty($this->params['page_no']) || $this->params['page_no'] == 1) {
            $model->where(['create_uid'=>0])->update([
                'create_uid' => app('db')->raw('user_id')
            ]);
        }

        $lists = $model
            ->alias('kk')
            ->field([
                'kk.id,kk.image,kk.name,kk.is_enable,kk.update_time,kk.create_time',
                'kk.user_id,u.sn,u.nickname,u.avatar,u.mobile,uc.nickname as create_user',
                'uc.avatar as create_avatar'
            ])
            ->leftJoin('user u', 'u.id = kk.user_id')
            ->leftJoin('user uc', 'uc.id = kk.create_uid')
            ->where($this->where())
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['user'] = [
                'id'       => $item['user_id'],
                'sn'       => $item['sn'],
                'nickname' => $item['nickname'],
                'mobile'   => $item['mobile'],
                'avatar'   => FileService::getFileUrl($item['avatar'])
            ];

            $item['create_avatar'] = FileService::getFileUrl($item['create_avatar']);
            unset($item['user_id']);
            unset($item['sn']);
            unset($item['nickname']);
            unset($item['mobile']);
            unset($item['avatar']);
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        $model = new KbKnow();
        return $model
            ->alias('kk')
            ->leftJoin('user u', 'u.id = kk.user_id')
            ->where($this->where())
            ->where($this->searchWhere)
            ->count();
    }

    public function setSearch(): array
    {
        return [
             '='     => ['kk.is_enable'],
            '%like%' => ['kk.name']
        ]??[];
    }

    public function where(): array
    {
        $where = [];
        if (isset($this->params['user']) && $this->params['user']) {
            $where[] = ['u.nickname|u.sn|u.mobile', 'like', '%'.$this->params['user'].'%'];
        }
        return $where;
    }
}