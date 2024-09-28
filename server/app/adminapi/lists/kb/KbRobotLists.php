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
use app\common\model\kb\KbRobot;
use app\common\service\FileService;

/**
 * 机器人列表
 */
class KbRobotLists extends BaseAdminDataLists implements ListsSearchInterface
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
        $model = new KbRobot();
        $lists = $model
            ->alias('kr')
            ->field([
                'kr.id,kr.kb_ids,kr.image,kr.name,kr.sort,kr.is_enable,kr.is_public',
                'kr.create_time,kr.user_id,u.sn,u.nickname,u.avatar,u.mobile'
            ])
            ->leftJoin('user u', 'u.id = kr.user_id')
            ->where($this->where())
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id desc')
            ->select()
            ->toArray();

        $modelKbKnow = new KbKnow();
        foreach ($lists as &$item) {
            $item['user'] = [
                'id'       => $item['user_id'],
                'sn'       => $item['sn'],
                'nickname' => $item['nickname'],
                'mobile'   => $item['mobile'],
                'avatar'   => FileService::getFileUrl($item['avatar'])
            ];

            $item['knows'] = [];
            if ($item['kb_ids']) {
                $kbIds = explode(',', $item['kb_ids']);
                $item['knows'] = $modelKbKnow->field(['id,name'])->whereIn('id', $kbIds)->select()->toArray();
            }

            unset($item['user_id']);
            unset($item['sn']);
            unset($item['nickname']);
            unset($item['mobile']);
            unset($item['avatar']);
            unset($item['kb_ids']);
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
        $model = new KbRobot();
        return $model
            ->alias('kr')
            ->field([
                'kr.id,kr.kb_ids,kr.image,kr.name,kr.sort,kr.is_enable,kr.is_public',
                'kr.create_time,kr.user_id,u.sn,u.nickname,u.avatar,u.mobile'
            ])
            ->leftJoin('user u', 'u.id = kr.user_id')
            ->where($this->where())
            ->where($this->searchWhere)
            ->count();
    }

    public function setSearch(): array
    {
        return [
            '='      => ['kr.is_enable', 'kr.is_public'],
            '%like%' => ['kr.name']
        ]??[];
    }

    public function where(): array
    {
        $where = [];
        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['u.nickname|u.sn|u.mobile', 'like', '%'.$this->params['keyword'].'%'];
        }

        return $where;
    }
}