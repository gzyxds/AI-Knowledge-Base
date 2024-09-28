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

namespace app\adminapi\validate\creation;

use app\common\model\creation\CreationCategory;
use app\common\validate\BaseValidate;

/**
 * 创作模型验证器类
 */
class CreationModelValidate extends BaseValidate
{
    protected $rule = [
        'id'          => 'require',
        'category_id' => 'require|checkCategory',
        'image'       => 'require',
        'sort'        => 'require|number',
        'tips'        => 'require',
        'content'     => 'require',
        'status'      => 'require',
        'temperature' => 'require|between:0,1',
        'top_p'       => 'require|between:0,1',
        'form'        => 'require|checkForm'
    ];

    protected $message = [
        'id.require'          => '请选择模型',
        'category_id.require' => '请选择分类',
        'image.require'       => '请上传图标',
        'content.require'     => '请输入主题内容',
        'tips.require'        => '请输入提示内容',
        'sort.require'        => '请输入排序',
        'sort.number'         => '排序值错误',
        'status.require'      => '请选择状态',

        'temperature.require'       => '请选择词汇属性',
        'temperature.between'       => '词汇属性值在0~1之间',
        'top_p.require'             => '请选择随机属性',
        'top_p.between'             => '随机属性值在0~1之间',
        'presence_penalty.require'  => '请选择话题属性',
        'presence_penalty.between'  => '题属性值在0~1之间',
        'frequency_penalty.require' => '请选择重复属性',
        'frequency_penalty.between' => '重复属性值在0~1之间',
        'n.require'                 => '请选择最大回复',
        'n.in'                      => '最大回复值错误',
        'form.require'              => '模型参数缺少',
    ];

    protected function sceneAdd(): CreationModelValidate
    {
        return $this->remove('id', true);
    }

    protected function sceneId(): CreationModelValidate
    {
        return $this->only(['id']);
    }

    protected function checkCategory($value): bool|string
    {
        $category = (new CreationCategory())->where(['id'=>$value])->findOrEmpty();
        if($category->isEmpty()){
            return '分类不存在';
        }
        return true;
    }

    protected function checkForm($value, $rule, $data): bool|string
    {
        unset($rule);
        if(empty($value)){
            return true;
        }
        $formList = json_decode($value,true);
        $props = array_column($formList,'props');
        $required = array_sum(array_column($props,'isRequired'));
        if(0 == $required){
            return '表单内容至少一个是必填的';
        }
        $fieldList = [];
        foreach ($formList as $form) {
            $isRequired = $form['props']['isRequired'] ?? false;
            $field =  $form['props']['field'] ?? '';
            $title =  $form['props']['title'] ?? '';
            if(empty($field)){
                return '请设置'.$title.'的字段值';
            }
            if(in_array($field,$fieldList)){
                return $field . '字段值重复';
            }
            $fieldList[] = $field;
            $title = $form['props']['title'] ?? '';
            if ($isRequired && !strpos($data['content'], $field)) {
                return $title . '字段为必填,请在内容中输入';
            }
        }
        return true;
    }
}