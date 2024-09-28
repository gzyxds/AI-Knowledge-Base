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

namespace app\common\model\member;
use app\common\enum\member\MemberPackageEnum;
use app\common\model\BaseModel;
use think\model\relation\HasOne;

/**
 * 会员价格模型类
 * Class LevelApply
 * @package app\common\model\member
 */
class MemberPackagePrice extends BaseModel{


    /**
     * @notes 会员套餐描述
     * @param $value
     * @param $rule
     * @param $data
     * @return string|void
     * @author cjhao
     * @date 2024/7/11 10:59
     */
    public function getDurationTextAttr($value,$data)
    {
        switch ($data['duration_type']){
            case MemberPackageEnum::DURATION_TYPE_DAY:
                return $data['duration'].'天';
            case MemberPackageEnum::DURATION_TYPE_MONTH:
                return $data['duration'].'个月';
            case MemberPackageEnum::DURATION_PERPEUTAL:
                return '永久';
        }
    }
}