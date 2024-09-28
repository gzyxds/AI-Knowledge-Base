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

namespace app\adminapi\logic\member;

use app\common\enum\draw\DrawEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\logic\BaseLogic;
use app\common\model\cardcode\CardCode;
use app\common\model\chat\Models;
use app\common\model\member\MemberBenefits;
use app\common\model\member\MemberOrder;
use app\common\model\member\MemberPackage;
use app\common\model\member\MemberPackageApply;
use app\common\model\member\MemberPackagePrice;
use app\common\model\member\UserMember;
use app\common\service\ConfigService;
use app\common\service\FileService;
use think\Exception;
use think\facade\Db;

/**
 * 会员套餐逻辑类
 * Class MemberPackageLogic
 * @package app\adminapi\logic\member
 */
class MemberPackageLogic extends BaseLogic
{

    /**
     * @notes 获取会员配置
     * @return array
     * @author cjhao
     * @date 2024/6/14 10:59
     */
    public function getConfig()
    {
        return [
            'is_open'   => ConfigService::get('member_package','is_open',1)
        ];

    }


    /**
     * @notes 设置会员配置
     * @return bool
     * @author cjhao
     * @date 2024/6/14 10:59
     */
    public function setConfig(array $params)
    {
        ConfigService::set('member_package','is_open',$params['is_open']);
        return true;

    }


    /**
     * @notes 添加会员套餐
     * @param $params
     * @return bool
     * @author cjhao
     * @date 2024/5/27 14:21
     */
    public function add($params)
    {
        try {
            Db::startTrans();

            $memberPackage = new MemberPackage();
            $memberPackage->name = $params['name'];
            $memberPackage->describe = $params['describe'] ?? '';
            $memberPackage->status = $params['status'];
            $memberPackage->sort = $params['sort'];
            $memberPackage->save();
            //权益
            $benefits = $params['benefits_list'] ?? [];
            foreach ($benefits as $key => $benefit){
                $benefits[$key]['image'] = FileService::setFileUrl($benefit['image']);
                $benefits[$key]['package_id'] = $memberPackage->id;
            }
            (new MemberBenefits())->saveAll($benefits);
            //对话向量模型
            $chatModel      = $params['model_list']['chat_model'];
            $vectorModel    = $params['model_list']['vector_model'];
            $applyModel     = $params['apply_list'];
            $applyLists     = [];
            foreach ($chatModel as $model){
                $model['type'] = MemberPackageEnum::APPLY_CHAT;
                $model['package_id'] = $memberPackage->id;
                $applyLists[] = $model;
            }
            foreach ($vectorModel as $model){
                $model['type'] = MemberPackageEnum::APPLY_VECTOR;
                $model['package_id'] = $memberPackage->id;
                $applyLists[] = $model;
            }
            //应用
            foreach ($applyModel as $model){
                switch ($model['channel']){
                    case DrawEnum::API_SD:
                    case DrawEnum::API_DALLE3:
                    case DrawEnum::API_MJ_GOAPI:
                        $model['type'] = MemberPackageEnum::APPLY_DRAW;
                        break;
                    case 'music':
                        $model['type'] = MemberPackageEnum::APPLY_MUSIC;
                        break;
                    case 'mindmap':
                        $model['type'] = MemberPackageEnum::APPLY_MINDMAP;
                        break;
                    case 'video':
                        $model['type'] = MemberPackageEnum::APPLY_VIDEO;
                        break;
                }
                $model['package_id'] = $memberPackage->id;

                $applyLists[] = $model;
            }
            (new MemberPackageApply())->saveAll($applyLists);
            //会员价格
            $priceList = $params['price_list'] ?? [];
            foreach ($priceList as $key => $item) {
                $priceList[$key]['package_id'] = $memberPackage->id;
                $priceList[$key]['sort'] = $key;
            }
            (new MemberPackagePrice())->saveAll($priceList);

            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 编辑会员权益
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/6/27 11:01 上午
     */
    public function edit($params)
    {
        try {
            Db::startTrans();
            $memberPackage = MemberPackage::where(['id'=>$params['id']])->findOrEmpty();
            $memberPackage->name = $params['name'];
            $memberPackage->describe = $params['describe'] ?? '';
            $memberPackage->status = $params['status'];
            $memberPackage->sort = $params['sort'];
            $memberPackage->save();

            $memberPackagePriceLists = MemberPackagePrice::where(['package_id'=>$params['id']])
                ->append(['duration_text'])
                ->field('duration,duration_type,id')
                ->select()->toArray();
            $memberPackagePriceLists = array_column($memberPackagePriceLists,null,'id');

            //删除数据，在重新添加
            MemberBenefits::where(['package_id'=>$params['id']])->delete();
            MemberPackageApply::where(['package_id'=>$params['id']])->delete();
//            MemberPackagePrice::where(['package_id'=>$params['id']])->delete();

            //权益
            $benefits = $params['benefits_list'] ?? [];
            foreach ($benefits as $key => $benefit){
                unset($benefits[$key]['id']);
                unset($benefits[$key]['create_time']);
                $benefits[$key]['image'] = FileService::setFileUrl($benefit['image']);
                $benefits[$key]['package_id'] = $memberPackage->id;
            }
            (new MemberBenefits())->saveAll($benefits);
            //对话向量模型
            $chatModel      = $params['model_list']['chat_model'];
            $vectorModel    = $params['model_list']['vector_model'];
            $applyModel     = $params['apply_list'];
            $applyLists     = [];
            foreach ($chatModel as $model){
                unset($model['id']);
                $model['type'] = MemberPackageEnum::APPLY_CHAT;;
                $model['package_id'] = $memberPackage->id;
                unset($model['create_time']);
                $applyLists[] = $model;
            }
            foreach ($vectorModel as $model){
                unset($model['id']);
                $model['type'] = MemberPackageEnum::APPLY_VECTOR;;
                $model['package_id'] = $memberPackage->id;
                unset($model['create_time']);
                $applyLists[] = $model;
            }
            //应用
            foreach ($applyModel as $model){
                unset($model['id']);
                switch ($model['channel']){
                    case DrawEnum::API_SD:
                    case DrawEnum::API_DALLE3:
                    case DrawEnum::API_MJ_GOAPI:
                        $model['type'] = MemberPackageEnum::APPLY_DRAW;
                        break;
                    case 'music':
                        $model['type'] = MemberPackageEnum::APPLY_MUSIC;
                        break;
                    case 'mindmap':
                        $model['type'] = MemberPackageEnum::APPLY_MINDMAP;
                        break;
                    case 'video':
                        $model['type'] = MemberPackageEnum::APPLY_VIDEO;
                        break;
                    case 'aisearch':
                        $model['type'] = MemberPackageEnum::APPLY_AISEARCH;
                        break;

                }
                $model['package_id'] = $memberPackage->id;
                unset($model['create_time']);
                $applyLists[] = $model;
            }
            (new MemberPackageApply())->saveAll($applyLists);
            //会员价格
            $priceList = $params['price_list'] ?? [];
            $priceIds = array_column($priceList,'id');
            $priceDiffIds = array_diff(array_keys($memberPackagePriceLists),$priceIds);
            if($priceDiffIds){
                $cardCodeList = CardCode::alias('CC')
                    ->join('card_code_record CCR','CC.id = CCR.card_id')
                    ->where(['relation_id'=>$priceDiffIds,'status'=>0])
                    ->DISTINCT(true)
                    ->column('relation_id');
                    foreach ($cardCodeList as $relationId){
                        $memberPackageName = $memberPackagePriceLists[$relationId] ?? '';
                        throw new Exception($memberPackageName.'已关联了卡密，无法删除');
                    }
            }
            foreach ($priceList as $key => $item) {
                unset($item['buy_num']);
                unset($item['create_time']);
                $item['sort'] = $key;
                if(isset($item['id']) && $item['id']){
                    unset($memberPackagePriceLists[$item['id']]);
                    $item['package_id'] = $memberPackage->id;
                    MemberPackagePrice::update($item);
                    unset($priceList[$key]);
                }else{
                    unset($item['id']);
                    $item['package_id'] = $memberPackage->id;
                    $priceList[$key] = $item;
                }
            }
            if($priceList){
                (new MemberPackagePrice())->saveAll($priceList);
            }
            //删除已经清理的价格
            if($memberPackagePriceLists){
                MemberPackagePrice::where(['id'=>array_keys($memberPackagePriceLists)])->delete();
            }
            Db::commit();
            return true;
        }catch (Exception $e){
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 获取套餐详情
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author cjhao
     * @date 2024/5/27 16:37
     */
    public function detail($id)
    {
        $memberPackage = MemberPackage::where(['id'=> $id])->findOrEmpty();
        $priceLists = MemberPackagePrice::where(['package_id'=>$id])->order('sort asc')->select()->toArray();
        $benefitLists = MemberBenefits::where(['package_id'=>$id])->select()->toArray();
        $applyData = MemberPackageApply::where(['package_id'=>$id])->select()->toArray();
        $applyModel = [];
        $applyLists = [];
        $chatModel = [];
        $chatLists = [];
        $vectorModel = [];
        $vectorLists = [];
        $modelsLists = Models::column('name','id');
        foreach ($applyData as $apply){
            switch ($apply['type']){
                case MemberPackageEnum::APPLY_CHAT:
                    if(!isset($modelsLists[$apply['channel']])){
                        break;
                    }
                    $apply['name'] = $modelsLists[$apply['channel']];
                    $chatModel[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_VECTOR:
                    if(!isset($modelsLists[$apply['channel']])){
                        break;
                    }
                    $apply['name'] = $modelsLists[$apply['channel']];
                    $vectorModel[] = $apply;
                    break;
                case MemberPackageEnum::APPLY_DRAW:
                case MemberPackageEnum::APPLY_MUSIC:
                case MemberPackageEnum::APPLY_MINDMAP:
                case MemberPackageEnum::APPLY_VIDEO:
                case MemberPackageEnum::APPLY_AISEARCH:
                    $name = MemberPackageEnum::getApplyLissts($apply['channel']);
                    if(empty($name)){
                        break;
                    }
                    $apply['name'] = MemberPackageEnum::getApplyLissts($apply['channel']);
                    $applyModel[] = $apply;
                    break;
            }
        }
        //如果有新增的模型
        $defaultModels = $this->getModels();
        $chatModelLists = array_column($chatModel,null,'channel');
        foreach ($defaultModels['chat_list'] as $defaultModel){
            $chatLists[] = $chatModelLists[$defaultModel['channel']] ?? $defaultModel;
        }
        $vectorModelLists = array_column($vectorModel,null,'channel');
        foreach ($defaultModels['vector_list'] as $defaultModel){
            $vectorLists[] = $vectorModelLists[$defaultModel['channel']] ?? $defaultModel;
        }
        $applyModelLists = array_column($applyModel,null,'channel');
        foreach ($defaultModels['apply_list'] as $defaultModel){
            $applyLists[] = $applyModelLists[$defaultModel['channel']] ?? $defaultModel;
        }
//        empty($chatModel) && $chatModel = $defaultModels['chat_list'];
//        empty($vectorModel) && $vectorModel = $defaultModels['vector_list'];
//        empty($applyLists) && $applyLists = $defaultModels['apply_list'];

        $ids = array_column($priceLists,'id');
        $memberPriceNum = MemberOrder::where(['member_price_id'=>$ids])
            ->group('member_price_id')
            ->column('count(id)','member_price_id');
        foreach ($priceLists as $key => $price){
//            $priceLists[$key]['is_give'] = 0;
//            if($price['give_balance'] > 0 & $price['give_robot'] > 0){
//                $priceLists[$key]['is_give'] = 1;
//            }
            $priceLists[$key]['buy_num'] = $memberPriceNum[$price['id']] ?? 0;
            $priceLists[$key]['give_balance'] = format_amount_zero($price['give_balance']);
        }
        $memberPackage->price_list = $priceLists;

        $memberPackage->apply_list = $applyLists;
        $memberPackage->benefits_list = $benefitLists;
        $memberPackage->model_list = [
            'chat_model'    => $chatLists,
            'vector_model'   => $vectorLists,
        ];
        $whereOr = [ [
            ['package_id','=',$memberPackage->id],
            ['is_perpetual','=',0],
            ['member_end_time','>',time()]
        ],[
            ['is_perpetual','=',1],
            ['package_id','=',$memberPackage->id]
        ]];
        $count = UserMember::whereOr($whereOr)
            ->count();
        $memberPackage->is_use = $count > 0 ? true : false;
        return $memberPackage->toArray();
    }

    /**
     * @notes 删除会员权益
     * @param $params
     * @return bool
     * @author ljj
     * @date 2023/6/27 11:05 上午
     */
    public function del($params)
    {
        $whereOr = [ [
            ['package_id','=',$params['id']],
            ['is_perpetual','=',0],
            ['member_end_time','>',time()]
        ],[
            ['is_perpetual','=',1],
            ['package_id','=',$params['id']]
        ]];
        $count = UserMember::whereOr($whereOr)
            ->count();

        if($count > 0){
            return '已有会员关联了该套餐，不允许删除';
        }
        MemberPackage::destroy($params['id']);

        return true;
    }

    /**
     * @notes 调整状态
     * @param $params
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function status($params)
    {
        $result = MemberPackage::findOrEmpty($params['id']);
        $result->status = !$result->status;
        $result->save();
    }

    /**
     * @notes 调整推荐
     * @param $params
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function recommend($params)
    {
        MemberPackage::where(['is_recommend'=>1])->update(['is_recommend'=>0]);
        $result = MemberPackage::findOrEmpty($params['id']);
        $result->is_recommend = !$result->is_recommend;
        $result->save();
    }


    /**
     * @notes 调整排序
     * @param $params
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function sort($params)
    {
        $result = MemberPackage::findOrEmpty($params['id']);
        $result->sort = $params['sort'];
        $result->save();
    }

    /**
     * @notes 获取模型
     * @return array
     * @author cjhao
     * @date 2024/5/27 15:49
     */
    public function getModels()
    {
        $modelsLists = Models::column('name,type,id');
        $chatModel = [];
        $vectorModel = [];
        $applyModel = [];
        foreach ($modelsLists as $model){
            if(MemberPackageEnum::APPLY_CHAT == $model['type']){
                $chatModel[] = [
                    'channel'   => $model['id'],
                    'name'      => $model['name'],
                    'day_limit' => '',
                    'status'    => 1,
                ];
            }else{
                $vectorModel[] = [
                    'channel'   => $model['id'],
                    'name'      => $model['name'],
                    'day_limit' => '',
                    'status'    => 1,
                ];
            }
        }
        $applyLists = MemberPackageEnum::getApplyLissts();
        foreach ($applyLists as $key => $value){
            $applyModel[] = [
                'channel'   => $key,
                'name'      => $value,
                'day_limit' => '',
                'status'    => 1,
            ];
        }
        return [
            'chat_list'     => $chatModel,
            'vector_list'   => $vectorModel,
            'apply_list'    => $applyModel,
        ];

    }

    /**
     * @notes 公共列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/6/21 8:21 下午
     */
    public function commonLists()
    {
        $lists = MemberPackage::field('id,name')
            ->order(['sort'=>'desc','id'=>'asc'])
            ->select()
            ->toArray();

        return $lists;
    }
}