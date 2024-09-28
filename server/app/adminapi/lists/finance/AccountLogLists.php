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

namespace app\adminapi\lists\finance;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\user\AccountLogEnum;
use app\common\lists\ListsExtendInterface;
use app\common\model\user\UserAccountLog;
use app\common\service\FileService;
use JetBrains\PhpStorm\Pure;

/**
 * 余额明细列表
 */
class AccountLogLists extends BaseAdminDataLists implements ListsExtendInterface
{
    public function where(): array
    {
        $where = [];
        if (isset($this->params['change_type']) && $this->params['change_type']) {
            $where[] = ['ua.change_type', '=', intval($this->params['change_type'])];
        }

        if (isset($this->params['user_info']) && $this->params['user_info']) {
            $where[] = ['u.sn|u.nickname|u.mobile', 'like', '%'.$this->params['user_info'].'%'];
        }

        if (isset($this->params['start_time']) && $this->params['start_time']) {
            $where[] = ['ua.create_time', '>=', strtotime($this->params['start_time'])];
        }

        if (isset($this->params['end_time']) && $this->params['end_time']) {
            $where[] = ['ua.create_time', '<=', strtotime($this->params['end_time'])];
        }

        if (isset($this->params['type']) && $this->params['type']) {
            $where[] = ['ua.change_object', '=', $this->params['type']];
        }

        return $where;
    }

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
        $model = new UserAccountLog();
        $lists = $model
            ->alias('ua')
            ->field([
                'ua.user_id,u.sn,u.nickname,u.avatar,ua.admin_id',
                'ua.id,ua.action,ua.change_type,ua.change_amount,ua.left_amount,ua.create_time',
            ])
            ->where($this->where())
            ->join('user u', 'u.id=ua.user_id')
            ->order('id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->append(['admin'])
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['avatar']        = FileService::getFileUrl($item['avatar']);
            $item['change_type']   = AccountLogEnum::getChangeTypeDesc($item['change_type']);
            $item['change_amount'] = format_amount_zero($item['change_amount']);
            $item['left_amount']   = format_amount_zero($item['left_amount']);
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
        $model = new UserAccountLog();
        return $model
            ->alias('ua')
            ->field([
                'ua.user_id,u.sn,u.nickname,u.avatar,ua.admin_id',
                'ua.id,ua.action,ua.change_type,ua.change_amount,ua.left_amount,ua.create_time',
            ])
            ->where($this->where())
            ->join('user u', 'u.id=ua.user_id')
            ->count();
    }

    /**
     * @notes 扩展
     * @return array
     * @author fzr
     */
    public function extend(): array
    {
        $type = intval($this->params['type']??1);
        $typeCodes = AccountLogEnum::getChangeTypeCodes($type);
        $typeLists = AccountLogEnum::getChangeTypeDesc(0, true);

        $data = [];
        foreach ($typeLists as $key => $value) {
            if (in_array($key, $typeCodes)) {
                $data[$key] = $value;
            }
        }

        return ['types' => $data]??[];
    }
}