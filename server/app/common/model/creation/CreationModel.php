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

namespace app\common\model\creation;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use think\model\relation\HasOne;

/**
 * 创作模型
 */
class CreationModel extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    protected $json = ['form'];

    // 设置JSON数据返回数组
    protected $jsonAssoc = true;

    public function category(): HasOne
    {
        return $this->hasOne(CreationCategory::class,'id');
    }

    /**
     * @notes 词汇属性
     * @param $value
     * @return float
     * @author ljj
     * @date 2023/6/8 2:32 下午
     */
    public function getTemperatureAttr($value): float
    {
        return floatval($value);
    }

    /**
     * @notes 随机属性
     * @param $value
     * @return float
     * @author ljj
     * @date 2023/6/8 2:32 下午
     */
    public function getTopPAttr($value): float
    {
        return floatval($value);
    }

    /**
     * @notes 话题属性
     * @param $value
     * @return float
     * @author ljj
     */
    public function getPresencePenaltyAttr($value): float
    {
        return floatval($value);
    }

    /**
     * @notes 重复属性
     * @param $value
     * @return float
     * @author ljj
     */
    public function getFrequencyPenaltyAttr($value): float
    {
        return floatval($value);
    }
}