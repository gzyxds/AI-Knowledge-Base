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
namespace app\adminapi\logic\cardcode;
use app\common\enum\CardCodeEnum;
use app\common\enum\CardCodeRecordEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\model\cardcode\CardCode;
use app\common\model\cardcode\CardCodeRecord;
use app\common\model\member\MemberPackage;
use app\common\model\member\MemberPackagePrice;
use app\common\model\recharge\RechargePackage;
use app\common\service\ConfigService;
use think\facade\Db;

/**
 * 卡密逻辑类
 * Class CardCodeController
 * @package app\adminapi\logic\cardcode
 */
class CardCodeLogic
{

    /**
     * @notes 获取套餐
     * @return array
     * @author cjhao
     * @date 2023/7/10 15:50
     */
    public function getPackageList()
    {
        $memberPackge = MemberPackage::where(['status'=>1])
            ->field('name,id')
            ->append(['price_list'])
            ->select();
        foreach ($memberPackge as $key => $member){
            foreach ($member['price_list'] as $priceKey =>  $priceLists){
                $longTime = '';
                switch ($priceLists['duration_type']){
                    case MemberPackageEnum::DURATION_TYPE_DAY:
                        $longTime = $priceLists['duration'].'天';
                        break;
                    case MemberPackageEnum::DURATION_TYPE_MONTH:
                        $longTime = $priceLists['duration'].'个月';
                        break;
                    case MemberPackageEnum::DURATION_PERPEUTAL:
                        $longTime = '永久';
                        break;
                }
                $memberPackge[$key]['price_list'][$priceKey]['long_time'] = $longTime;
            }
        }
        $rechargePackge = RechargePackage::where(['status'=>1])
            ->field('name,id')->select();
        return [
            'member_pckge'      => $memberPackge,
            'recharge_pckge'    => $rechargePackge,
        ];

    }
    /**
     * @notes 添加卡密
     * @param array $post
     * @return bool
     * @author cjhao
     * @date 2023/7/10 15:47
     */
    public function add(array $post)
    {

        try{
            Db::startTrans();
            $post['sn'] = card_sn(CardCode::class,'sn');
            $cardCode   = new CardCode();
            $cardCode->save($post);
            $cardCodeRecord = [];
            for ($i = 0; $i < $post['card_num']; $i++) {
                $cardCodeRecord[] = [
                    'card_id'   => $cardCode->id,
                    'sn'        => card_sn(CardCodeRecord::class,'sn','K',10,$post['rule_type']),
                ];
            }
            (new CardCodeRecord())->saveAll($cardCodeRecord);

            Db::commit();
            return true;
        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }


    /**
     * @notes 卡密详情
     * @param int $id
     * @author cjhao
     * @date 2023/7/10 17:18
     */
    public function detail(int $id)
    {

        $cardCode = CardCode::where(['id' => $id])->field('id,sn,type,balance,card_num,relation_id,valid_start_time,valid_end_time,create_time,remark')->findOrEmpty();
        $cardCode->type_desc = CardCodeEnum::getTypeDesc($cardCode->type);
        $cardCode->content = '';
        $cardCode->package_id = '';
        switch ($cardCode->type){
            case CardCodeEnum::TYPE_MEMBER:
                $memberPackge = MemberPackagePrice::alias('MPP')
                    ->join('member_package MP','MPP.package_id = MP.id')
                    ->where(['MPP.id'=>$cardCode->relation_id])
                    ->field('name,duration,package_id,duration_type')
                    ->findOrEmpty();
                $duration = $memberPackge['duration'] ?? '';
                $durationType = $memberPackge['duration_type'] ?? 0;
                $cardCode->content = $memberPackge['name'] ?? '';
                $cardCode->package_id = $memberPackge['package_id'];
                switch ($durationType){
                    case MemberPackageEnum::DURATION_TYPE_DAY:
                        $cardCode->content.= '('.$duration.'天)';
                        break;
                    case MemberPackageEnum::DURATION_TYPE_MONTH:
                        $cardCode->content.= '('.$duration.'个月)';
                        break;
                    case MemberPackageEnum::DURATION_PERPEUTAL:
                        $cardCode->content.= '(永久)';
                        break;
                }
                break;
            case CardCodeEnum::TYPE_RECHARGE:
                $cardCode->content = RechargePackage::where(['id'=>$cardCode->relation_id])->value('name ');
                break;
            case CardCodeEnum::TYPE_BALANCE:
                $cardCode->content = $cardCode->balance.'条';
                break;
        }
        $cardCode->valid_time_desc = date('Y-m-d H:i:s',$cardCode->valid_start_time).'~'.date('Y-m-d H:i:s',$cardCode->valid_end_time);
        $useNum = CardCodeRecord::where(['card_id'=>$cardCode->id,'status'=>CardCodeRecordEnum::STATYS_YES])->count();
        $cardCode->use_num = $useNum;
        $cardCode->unused_num = $cardCode->card_num - $useNum;
        return $cardCode->toArray();
    }


    /**
     * @notes 删除卡密
     * @param int $id
     * @author cjhao
     * @date 2023/7/10 17:33
     */
    public function del(int $id)
    {
        CardCode::where(['id'=>$id])->delete();
    }


    /**
     * @notes 获取卡密配置
     * @return array|int|mixed|string
     * @author cjhao
     * @date 2023/7/11 11:53
     */
    public function getConfig()
    {
        return [
            'is_show' =>  ConfigService::get('card_code','is_show'),
            'buy_site' =>  ConfigService::get('card_code','buy_site'),
            'is_open' =>  ConfigService::get('card_code','is_open',0),
        ];
    }


    /**
     * @notes 设置卡密设置
     * @param array $post
     * @author cjhao
     * @date 2023/7/11 11:55
     */
    public function setConfig(array $post)
    {
         ConfigService::set('card_code','is_show',$post['is_show']);
         ConfigService::set('card_code','buy_site',$post['buy_site']);
         ConfigService::set('card_code','is_open',$post['is_open']);
    }


}