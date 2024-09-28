<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\api\logic;
use app\common\model\member\MemberPackage;

/**
 * 会员套餐逻辑类
 * Class MemberPackageLogic
 * @package app\api\logic
 */
class MemberPackageLogic
{

    /**
     * @notes 获取会员套餐
     * @param int $userId
     * @return array
     * @author cjhao
     * @date 2024/5/27 18:33
     */
    public function lists(int $userId){

        $memberLists = MemberPackage::where(['status'=>1])
            ->field('id,name,describe,is_recommend')
            ->order('sort asc,id desc')
            ->append(['benefits_list','price_list'])
            ->select()
            ->toArray();
        foreach ($memberLists as $memberKey => $memberList){
            foreach ($memberList['price_list'] as $key => $price)
            {
                $memberLists[$memberKey]['price_list'][$key]['give_balance'] = format_amount_zero($price['give_balance']);
            }
        }
        return $memberLists;
    }
}