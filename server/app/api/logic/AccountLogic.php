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

namespace app\api\logic;

use app\common\enum\user\AccountLogEnum;
use app\common\logic\BaseLogic;
use app\common\model\user\User;
use app\common\model\user\UserAccountLog;

/**
 * 账户明细逻辑类
 */
class AccountLogic extends BaseLogic
{
    /**
     * @notes 余额明细详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $model = new UserAccountLog();
        $detail = $model
            ->field([
                'id,sn,user_id,admin_id,change_type,robot_name,create_time',
                'action,change_amount,left_amount,flows'
            ])
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if (!$detail['robot_name'] && $detail['change_type'] == AccountLogEnum::UM_DEC_CHAT) {
            $detail['robot_name']  = '普通对话';
        }

        $detail['change_type'] = AccountLogEnum::getChangeTypeDesc($detail['change_type']);

        // 格式化金额
        $detail['change_amount'] = format_amount_zero($detail['change_amount']);
        $detail['left_amount']   = format_amount_zero($detail['left_amount']);

        // 用户信息
        $detail['user'] = ['avatar'=>'', 'nickname'=>$detail['admin_id']?'后台管理员':'-'] ;
        if ($detail['user_id']) {
            $detail['user'] = (new User())
                ->field(['avatar,nickname'])
                ->where(['id' => $detail['user_id']])
                ->findOrEmpty()
                ->toArray();
        }

        // tokens明细
        if ($detail['flows']) {
            $types = ['chat'=>'AI对话', 'emb'=>'文本检索', 'qa'=>'问答拆分'];
            $flows = json_decode($detail['flows'], true);
            foreach ($flows as &$item) {
                $item['name'] =  $types[$item['name']]??'未知';
            }
            $detail['flows'] = $flows;
        } else {
            $detail['flows'] = [];
        }

        return $detail;
    }

    /**
     * @notes 账户流水记录
     * @param $userId
     * @param $changeType
     * @param $action
     * @param $changeAmount
     * @param string $sourceSn
     * @param string $remark
     * @param array $extra
     * @return UserAccountLog|false|\think\Model
     * @author 段誉
     * @date 2023/2/23 12:03
     */
    public static function add($userId, $changeType, $action, $changeAmount, string $sourceSn = '', string $remark = '',  array $extra = [])
    {
        $user = User::findOrEmpty($userId);
        if($user->isEmpty()) {
            return false;
        }

        $changeObject = AccountLogEnum::getChangeObject($changeType);
        if(!$changeObject) {
            return false;
        }

        switch ($changeObject) {
            // 对话余额
            case AccountLogEnum::UM:
                $left_amount = $user->balance;
                break;
            // 可提现佣金
            case AccountLogEnum::MONEY:
                $left_amount = $user->user_money;
                break;
            // 绘画余额
            case AccountLogEnum::DRAW:
                $left_amount = $user->balance_draw;
                break;
        }

        $data = [
            'sn' => generate_sn(UserAccountLog::class, 'sn', 20),
            'user_id' => $userId,
            'change_object' => $changeObject,
            'change_type' => $changeType,
            'action' => $action,
            'left_amount' => $left_amount,
            'change_amount' => $changeAmount,
            'source_sn' => $sourceSn,
            'remark' => $remark,
            'extra' => $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE) : '',
        ];
        return UserAccountLog::create($data);
    }
}