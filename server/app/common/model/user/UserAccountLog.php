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

namespace app\common\model\user;

use app\common\enum\user\AccountLogEnum;
use app\common\model\auth\Admin;
use app\common\model\BaseModel;
use think\Model;
use think\model\concern\SoftDelete;

/**
 * 账户流水记录模型
 */
class UserAccountLog extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * @notes 获取管理员
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     */
    public function getAdminAttr($value, $data): mixed
    {
        unset($value);
        return (new Admin())->where('id', $data['admin_id'])->value('name') ?? '-';
    }

    /**
     * @param $userId (用户ID
     * @param $changeType (变动类型
     * @param $action (操作动作: [1=新增, 2=扣减])
     * @param $changeAmount (变动的值
     * @param string $sourceSn (来源编号)
     * @param string $remark (备注信息)
     * @param array $extra (扩展信息)
     * @param int $adminId (管理员ID)
     * @param array $flowUsage (token信息组)
     * @return UserAccountLog|false|Model
     */
    public static function add($userId, $changeType, $action, $changeAmount, string $sourceSn = '', string $remark = '', array $extra = [], int $adminId = 0, array $flowUsage = []): bool|Model|UserAccountLog
    {
        $flows = [];
        $robotId = 0;
        $robotName = '';
        if (!empty($flowUsage)) {
            $robotId = $flowUsage['robotId'] ?? 0;
            $robotName = $flowUsage['robotName'] ?? '';
            $flows = $flowUsage['flows'] ?? [];
        }

        // 取用户信息
        $user = (new User())->findOrEmpty($userId);
        if ($user->isEmpty()) {
            return false;
        }
        // 取变动对象
        $changeObject = AccountLogEnum::getChangeObject($changeType);
        if (!$changeObject) {
            return false;
        }

        // 变动后余额
        $leftAmount = match ($changeObject) {
            AccountLogEnum::UM => $user->balance,
            AccountLogEnum::ROBOT => $user->robot_num,
            AccountLogEnum::VIDEO => $user->video_num,
            AccountLogEnum::MONEY => $user->user_money
        };
        $data = [
            'sn'            => generate_sn(UserAccountLog::class, 'sn', 20),
            'user_id'       => $userId,
            'change_object' => $changeObject,
            'change_type'   => $changeType,
            'action'        => $action,
            'left_amount'   => $leftAmount,
            'change_amount' => $changeAmount,
            'robot_id'      => $robotId,
            'robot_name'    => $robotName,
            'source_sn'     => $sourceSn,
            'remark'        => $remark,
            'extra'         => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '',
            'flows'         => $flows ? json_encode($flows, JSON_UNESCAPED_UNICODE) : '',
            'admin_id'      => $adminId
        ];

        return UserAccountLog::create($data);
    }
}