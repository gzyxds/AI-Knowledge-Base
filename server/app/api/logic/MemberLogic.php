<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\logic;


use app\common\enum\member\MemberPackageEnum;
use app\common\enum\PayEnum;
use app\common\logic\BaseLogic;
use app\common\model\member\MemberAdjustLog;
use app\common\model\member\MemberPackage;
use app\common\model\member\MemberOrder;
use app\common\model\member\MemberPackagePrice;
use app\common\service\ConfigService;

class MemberLogic extends BaseLogic
{
    /**
     * @notes 会员套餐列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/4/20 5:11 下午
     */
    public function lists($userId)
    {
        $status = ConfigService::get('member', 'status', 1);
        $lists = MemberPackage::field('id,name,duration,duration_type,is_perpetual,sell_price,lineation_price,is_retrieve,retrieve_amount,is_default,benefits_ids,give_chat_number,give_draw_number,is_quota,quota_value,quota_tips,tag')
            ->append(['member_benefits','duration_desc'])
            ->where(['status'=>1])
            ->order(['sort'=>'desc','id'=>'asc'])
            ->select()
            ->toArray();

        foreach ($lists as &$list) {
            $list['quota_tips'] = !empty($list['quota_tips']) ? $list['quota_tips'] : '您已超过当前会员套餐的购买限制，如有需要请联系管理员！';
            $list['quota_tips_show'] = 0;
            if ($list['is_quota'] == 1 && !empty($list['quota_value']) && $list['quota_value'] > 0) {
                $user_buy_num = MemberOrder::where(['user_id'=>$userId,'pay_status'=>PayEnum::ISPAID,'member_package_id'=>$list['id']])->count();
                if ($user_buy_num >= $list['quota_value']) {
                    $list['quota_tips_show'] = 1;
                }
            }
        }

        return ['status'=>$status,'lists'=>$lists];
    }

    /**
     * @notes 购买
     * @param $params
     * @return array|false
     * @author ljj
     * @date 2023/4/20 5:50 下午
     */
    public function buy($params)
    {
        try {
            $memberPrice = MemberPackagePrice::where(['id'=>$params['member_price_id']])
                ->findOrEmpty()
                ->toArray();
            $memberPackage = MemberPackage::where(['id'=>$memberPrice['package_id']])->findOrEmpty()->toArray();
            //优惠金额
//            $discount_amount = (!isset($params['discount_amount']) || $params['discount_amount'] == '') ? 0 : $params['discount_amount'];
//            if ($member_package['is_retrieve'] == 0) {
//                $discount_amount = 0;
//            }
//            if ($member_package['is_retrieve'] == 1 && $discount_amount > $member_package['retrieve_amount']) {
//                $discount_amount = $member_package['retrieve_amount'];
//            }
            $discountAmount = 0;
            $orderAmount = $memberPrice['sell_price'] - $discountAmount;
            $orderAmount = $orderAmount > 0 ? $orderAmount : 0;
            $memberPackage['price_list'] = $memberPrice;
            $order = MemberOrder::create([
                'user_id'               => $params['user_id'],
                'order_sn'              => generate_sn(MemberOrder::class, 'order_sn'),
                'terminal'              => $params['terminal'],
                'order_amount'          => round($orderAmount,2),
                'discount_amount'       => $discountAmount,
                'total_amount'          => $memberPrice['sell_price'],
                'member_price_id'       => $params['member_price_id'],
                'member_package_id'     => $memberPrice['package_id'],
                'member_package_info'   => $memberPackage,
            ]);

            return [
                'order_id' => $order->id,
                'from' => 'member'
            ];
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 最近三十条购买记录
     * @return array
     * @author ljj
     * @date 2023/4/25 7:32 下午
     */
    public function buyLog(int $userId)
    {

        //后台开通的
        $memberLists = MemberAdjustLog::where(['user_id'=>$userId])
                    ->column('id,type,package_id,member_end_time,is_perpetual,package_snap,create_time','create_time');
        $sortLists = [];
        $packageList = MemberPackage::column('name','id');
        $now = time();
        foreach ($memberLists as $key => $memberList){
            $sortLists[] = $key;
            //原套餐
            $packageSnap = $memberList['package_snap'];
            if($memberList['package_id']){
                if($memberList['is_perpetual']){
                    $memberLists[$key]['package_long_time'] = "永久";
                }
                if(empty($packageSnap)){
                    $memberLists[$key]['original_package_long_time'] = intval(ceil(($memberList['member_end_time'] - $key) / (60 * 60 * 24))).'天';
                }else{
                    if(0 == $memberList['is_perpetual'] && $memberList['member_end_time']){

                        $memberEndTime = $packageSnap['member_end_time'] ?? 0;
                        $memberLists[$key]['original_package_long_time'] = intval(ceil(($memberList['member_end_time'] - $memberEndTime) / (60 * 60 * 24))).'天';
                    }else{
                        $memberLists[$key]['original_package_long_time'] = '永久';
                    }
                }
            }else{
                $memberLists[$key]['package_long_time'] = "-";
                $memberLists[$key]['original_package_long_time'] = "-";
            }
//            if($memberList['id'] == 166){
//                dd($memberLists[$key]);
//            }

            if(1 == $memberList['type']){
                $memberLists[$key]['channel_text'] = '系统调整';
                $memberLists[$key]['refund_status_text'] = '未退款';

            }else{
                $memberLists[$key]['channel_text'] = '卡密兑换';
                $memberLists[$key]['refund_status_text'] = '未退款';

                $memberPrice = $packageSnap['price_list'] ?? [];
                if($memberPrice){
                    switch ($memberPrice['duration_type']){
                        case MemberPackageEnum::DURATION_TYPE_DAY:
                            $memberLists[$key]['original_package_long_time'] = $memberPrice['duration'].'天';
                            break;
                        case MemberPackageEnum::DURATION_TYPE_MONTH:
                            $memberLists[$key]['original_package_long_time'] = $memberPrice['duration'].'个月';
                            break;
                        case MemberPackageEnum::DURATION_PERPEUTAL:
                            $memberLists[$key]['original_package_long_time'] = '永久';
                            break;
                    }
                }

            }
            $memberLists[$key]['package_name'] = $packageList[$memberList['package_id']] ?? '无';
            $memberLists[$key]['pay_way_text'] = '';
            $memberLists[$key]['create_time'] = date('Y-m-d H:i:s',$key);
            $memberLists[$key]['pay_time_text'] = '';
            if($memberList['package_id']){
                $memberLists[$key]['package_long_time'] = date('Y-m-d H:i:s',$memberList['member_end_time']);
            }

            $memberLists[$key]['package_info'] = null;
            $memberLists[$key]['channel'] = 2;

        }
        //订单记录
        $orderLists = MemberOrder::where(['pay_status'=>PayEnum::ISPAID,'user_id'=>$userId])
            ->append(['member_package','refund_status_text','pay_way_text','pay_time_text','pay_status_text','original_package_long_time','package_long_time','package_end_time'])
            ->hidden(['member_package_info'])
            ->order(['id'=>'desc'])
            ->field('id,order_sn,refund_status,member_package_id,pay_status,pay_time,order_amount,create_time,pay_way,member_package_info,package_end_time,user_id')
            ->select()->toArray();

        $orderLists = array_column($orderLists,null,'create_time');
        $orderSortLists = [];
        foreach ($orderLists as $key => $list){
            $sortLists[] = strtotime($list['create_time']);
            $list['package_name'] = $packageList[$list['member_package_id']] ?? '';
            $list['channel'] = 1;
            $list['channel_text'] = '自行购买';
//            $list['original_package_long_time'] = '-';
            $orderSortLists[strtotime($list['create_time'])] = $list;
        }
        //按订单时间大到小排序
        arsort($sortLists);
        $lists = [];
        foreach ($sortLists as $sort){
            $lists[] = $memberLists[$sort] ?? $orderSortLists[$sort] ?? [];
        }
        return $lists;
    }
}