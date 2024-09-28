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

use app\common\enum\PayEnum;
use app\common\enum\user\UserEnum;
use app\common\model\BaseModel;
use app\common\model\distribution\DistributionOrder;
use app\common\model\member\MemberOrder;
use app\common\model\recharge\RechargeOrder;
use app\common\service\FileService;
use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

/**
 * 用户模型
 * Class User
 * @package app\common\model\user
 */
class User extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    /**
     * @notes 关联用户授权模型
     * @return HasOne
     * @author 段誉
     * @date 2022/9/22 16:03
     */
    public function userAuth(): HasOne
    {
        return $this->hasOne(UserAuth::class, 'user_id');
    }

    /**
     * @notes 订单数量
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2023/5/25 10:02 上午
     */
    public function getOrderNumAttr($value,$data)
    {
        $member_num = MemberOrder::where(['user_id'=>$data['id'],'pay_status'=>PayEnum::ISPAID])->count();
        $recharge_num = RechargeOrder::where(['user_id'=>$data['id'],'pay_status'=>PayEnum::ISPAID])->count();
        return $member_num + $recharge_num;
    }


    /**
     * @notes 搜索器-用户信息
     * @param $query
     * @param $value
     * @author 段誉
     * @date 2022/9/22 16:12
     */
    public function searchKeywordAttr($query, $value)
    {
        if ($value) {
            $query->where('sn|nickname|mobile|email', 'like', '%' . $value . '%');
        }
    }

    /**
     * @notes 搜索器-注册来源
     * @param $query
     * @param $value
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchChannelAttr($query, $value)
    {
        if ($value) {
            $query->where('channel', '=', $value);
        }
    }

    /**
     * @notes 搜索器-注册时间
     * @param $query
     * @param $value
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchCreateTimeStartAttr($query, $value)
    {
        if ($value) {
            $query->where('create_time', '>=', strtotime($value));
        }
    }

    /**
     * @notes 搜索器-注册时间
     * @param $query
     * @param $value
     * @author 段誉
     * @date 2022/9/22 16:13
     */
    public function searchCreateTimeEndAttr($query, $value)
    {
        if ($value) {
            $query->where('create_time', '<=', strtotime($value));
        }
    }

    /**
     * @notes 头像获取器 - 用于头像地址拼接域名
     * @param $value
     * @return string
     * @author Tab
     * @date 2021/7/17 14:28
     */
    public function getAvatarAttr($value): string
    {
        return trim($value) ? FileService::getFileUrl($value) : '';
    }

    /**
     * @notes 获取器-性别描述
     * @param $value
     * @return string|string[]
     * @author 段誉
     * @date 2022/9/7 15:15
     */
    public function getSexAttr($value): array|string
    {
        return UserEnum::getSexDesc($value);
    }

    /**
     * @notes 登录时间
     * @param $value
     * @return string
     * @author 段誉
     * @date 2022/9/23 18:15
     */
    public function getLoginTimeAttr($value): string
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    /**
     * @notes 生成用户编码
     * @param string $prefix
     * @param int $length
     * @return string
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 10:33
     */
    public static function createUserSn(string $prefix = '', int $length = 8): string
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(0, 9);
        }
        $sn = $prefix . $rand_str;
        if ((new User())->where(['sn' => $sn])->find()) {
            return self::createUserSn($prefix, $length);
        }
        return $sn;
    }


    /**
     * @notes 邀请人数
     * @param $value
     * @param $data
     * @return int
     * @author ljj
     * @date 2023/5/25 10:03 上午
     */
    public function getInviteNumAttr($value,$data)
    {
        return User::where(['inviter_id'=>$data['id']])->count();
    }

    /**
     * @notes 邀请人昵称
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/5/26 11:08 上午
     */
    public function getInviterNameAttr($value,$data)
    {
        if (empty($data['inviter_id'])) {
            $result = '系统';
        } else {
            $result = User::where(['id'=>$data['inviter_id']])->value('nickname');
        }
        return $result;
    }

    /**
     * @notes 分销订单数量
     * @param $value
     * @param $data
     * @return mixed
     * @author ljj
     * @date 2023/5/26 11:08 上午
     */
    public function getDistributionOrderNumAttr($value,$data){
        return DistributionOrder::where(['first_user_id|second_user_id'=>$data['id']])->count();
    }
}