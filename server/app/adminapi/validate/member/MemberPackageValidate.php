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

namespace app\adminapi\validate\member;


use app\common\enum\member\MemberPackageEnum;
use app\common\model\chat\Models;
use app\common\model\member\MemberPackage;
use app\common\validate\BaseValidate;

class MemberPackageValidate extends BaseValidate
{
    protected $rule = [
        'id'            => 'require',
        'name'          => 'require|max:64|unique:'.MemberPackage::class.',name',
        'status'        => 'require|in:0,1',
        'is_recommend'  => 'require|in:0,1',
        'sort'          => 'number',
        'price_list'    => 'array|checkPriceList',
        'model_list'    => 'array|checkModelList',
        'apply_list'    => 'array|checkApplyList',
        'benefits_list' => 'checkBenefitsList',
    ];
    protected $message = [
        'id.require' => '参数缺失',
        'name.require' => '请输入套餐名称',
        'name.max'  => '套餐名称不能超过64个字符',
        'name.unique'  => '套餐名称重复',
        'status.require' => '请选择状态',
        'status.in' => '状态值错误',
        'sort.number' => '排序值错误',
        'price_list.require'    => '请输入价格',
        'price_list.array'    => '价格数据错误',
        'model_list.require'    => '请选择模型限制',
        'model_list.array'    => '模型限制错误',
        'apply_list.require'    => '请输入应用限制',
        'apply_list.array'    => '应用数据错误',
    ];

    public function sceneAdd()
    {
        return $this->only(['name','status','is_recommend','price_list','model_list','apply_list','benefits_list']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','name','status','is_recommend','price_list','model_list','apply_list','benefits_list']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneDel()
    {
        return $this->only(['id']);
    }

    public function sceneId()
    {
        return $this->only(['id']);
    }

    /**
     * @notes 验证价格
     * @param $value
     * @param $rule
     * @param $data
     * @return true|string
     * @author cjhao
     * @date 2024/5/27 11:48
     */
    public function checkPriceList($value,$rule,$data){
        foreach ($value as $item){
            $status = $item['status'] ?? '';
            $duration = $item['duration'] ?? '';
            $durationType = $item['duration_type'] ?? '';
            $sellPrice = $item['sell_price'] ?? '';
            $lineationPrice = $item['lineation_price'] ?? '';
            $isGive = $item['is_give'] ?? '';
            $giveBalance = $item['give_balance'] ?? '';
            $giveRobot = $item['give_robot'] ?? '';
            if('' == $status && !in_array($status,[1,2])){
                return '请选择是否上架';
            }
            if(3 != $durationType && '' == $duration){
                return '请输入时长';
            }
            if('' == $durationType && !in_array($durationType,[MemberPackageEnum::DURATION_TYPE_DAY,MemberPackageEnum::DURATION_TYPE_MONTH,MemberPackageEnum::DURATION_PERPEUTAL])){
                return '请现在时长类型';
            }
            if('' == $sellPrice && $sellPrice < 0){
                return '售价不能小于零';
            }
//            if('' === $lineationPrice){
//                return '划线价不能是空';
//            }
            if($lineationPrice && $lineationPrice < 0){
                return '划线价不能小于0';
            }
            if($isGive && $giveBalance < 0){
                return '赠送电力值不能小于0';
            }
            if($isGive && $giveRobot < 0){
                return '赠送机器人不能小于0';
            }
        }

        return true;

    }

    /**
     * @notes 验证模型
     * @param $value
     * @param $rule
     * @param $data
     * @return true|string
     * @author cjhao
     * @date 2024/5/27 11:48
     */
    public function checkModelList($value,$rule,$data){
        $modelLists = Models::column('name','id');
        $chatModel = $value['chat_model'] ?? [];
        $vectorModel = $value['vector_model'] ?? [];
        foreach ($chatModel as $item){
            $channel = $item['channel'] ?? '';
            $dayLimit = $item['day_limit'] ?: 0;
            $status = $item['status'] ?? 0;
            if(!isset($modelLists[$channel])){
                return '模型应用发生改变，请刷新页面';
            }
            if('' == $status && !in_array($status,[1,2])){
                return '请选择状态';
            }
            if($status && $dayLimit < 0){
                $modelName = $modelLists[$item['channel']] ??'';
                return '模型'.$modelName.'使用量不能小于零';
            }


        }
        foreach ($vectorModel as $item){
            $channel = $item['channel'] ?? '';
            $dayLimit = $item['day_limit'] ?: 0;
            $status = $item['status'] ?? 0;
            if(!isset($modelLists[$channel])){
                return '模型应用发生改变，请刷新页面';
            }
            if('' == $status && !in_array($status,[1,2])){
                return '请选择状态';
            }
            if($status && $dayLimit < 0){
                $modelName = $modelLists[$channel] ??'';
                return '模型'.$modelName.'使用量不能小于零';
            }

        }

        return true;

    }

    /**
     * @notes 验证应用
     * @param $value
     * @param $rule
     * @param $data
     * @return true|string
     * @author cjhao
     * @date 2024/5/27 11:48
     */
    public function checkApplyList($value,$rule,$data){
        foreach ($value as $item){
            $channel = $item['channel'] ?? '';
            $dayLimit = $item['day_limit'] ?: 0;
            $status = $item['status'] ?? 0;
            $channelLists = MemberPackageEnum::getApplyLissts();
            if(empty($channel) && !in_array($channel,$channelLists)){
                return '应用数据错误';
            }
            $channelName = $channelLists[$channel] ??'';
            if($dayLimit < 0){
                return '应用'.$channelName.'使用量不能小于零';
            }
            if('' == $status && !in_array($status,[1,2])){
                return '应用'.$channelName.'请选择状态';
            }
        }
        return true;

    }

    /**
     * @notes 验证会员权益
     * @param $value
     * @param $rule
     * @param $data
     * @return string|true
     * @author cjhao
     * @date 2024/5/27 11:46
     */
    public function checkBenefitsList($value,$rule,$data)
    {
        if(!is_array($value)){
            return '会员权益数据格式错误';
        }
        foreach ($value as $item){
            $image = $item['image'] ?? '';
            $name = $item['name'] ?? '';
            $describe = $item['describe'] ?? '';
            $status = $item['status'] ?? 0;
//            if(empty($image)){
//                return '请上传权益图标';
//            }
            if(empty($name)){
                return '请输入迁移名称';
            }
            if(empty($describe)){
                return '请输入用量';
            }
            if('' == $status && !in_array($status,[1,2])){
                return '请选择状态';
            }
        }
        return true;
    }
}