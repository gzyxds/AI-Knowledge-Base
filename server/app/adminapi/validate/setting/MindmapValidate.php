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

namespace app\adminapi\validate\setting;


use app\common\model\chat\ModelsCost;
use app\common\validate\BaseValidate;

class MindmapValidate extends BaseValidate
{
    protected $rule = [
        'channel_id'=>'require',
        'model_id'=>'require|checkModel',
        'is_example' => 'require|in:0,1',
        'example_content' => 'requireIf:is_example,1',
        'cue_word' => 'require|checkWord',
    ];

    protected $message = [
        'channel.require'=>'请选择模型',
        'model.require'=>'请选择子模型',
        'is_example.require' => '示例开关值缺失',
        'is_example.in' => '示例开关值错误',
        'example_content.requireIf' => '请输入示例内容',
        'cue_word.require' => '请输入提示词',
    ];


    public function sceneSetConfig()
    {
        return $this->only(['channel','model','cue_word']);
    }


    /**
     * @notes 校验提示词
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/9/21 11:14 上午
     */
    public function checkWord($value,$rule,$data)
    {
        if(!strpos($value, '{prompt}')){
            return '提示词必须填写一个变量';
        }

        return true;
    }

    /**
     * @notes 模型数据验证
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/9/21 11:14 上午
     */
    public function checkModel($value,$rule,$data){
        $channel = $data['channel'] ?? '';
        $model = ModelsCost::where(['model_id'=>$channel,'id'=>$value])->findOrEmpty();
        if($model->isEmpty()){
            return '模型数据错误，请刷新页面重新选择';
        }
        return true;
    }
}