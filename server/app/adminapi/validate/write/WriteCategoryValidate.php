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

namespace app\adminapi\validate\write;

use app\common\validate\BaseValidate;

/**
 * 写作分类参数验证器
 */
class WriteCategoryValidate extends BaseValidate
{
    protected $rule = [
        'ids'    => 'require|array',
        'id'     => 'require|number',
        'title'  => 'require|max:200',
        'image'  => 'max:200',
        'intro'  => 'require|max:500',
        'ai'     => 'require|max:100',
        'model'  => 'require|array',
        'forms'  => 'require|array|checkForm',
        'sort'   => 'number',
        'status' => 'require|in:0,1',
        'prompt_outline' => 'require',
        'prompt_archive' => 'require'
    ];

    protected $message = [
        'id.require'    => 'id不能为空',
        'id.number'     => 'id必须为数字',
        'title.require' => '写作类型不能为空',
        'title.max'     => '写作类型不能超出200个字符',
        'intro.require' => '类型描述不能为空',
        'intro.max'     => '类型描述不能超出500个字符',
        'ai.require'    => '请选择AI接口类型',
        'model.require' => '模型参数格式异常',
        'model.array'   => '模型参数格式异常',
        'forms.array'   => '表单参数格式异常',
        'sort.number'   => '排序编号必须为数字',
        'status.number' => '请设置模型的状态',
        'status.in'     => '模型状态选择异常',
        'prompt_outline.require' => '请填写生成大纲的调教文案',
        'prompt_archive.require' => '请填写生成全文的调教文案'
    ];

    public function sceneIds(): WriteCategoryValidate
    {
        return $this->only(['ids']);
    }

    public function sceneId(): WriteCategoryValidate
    {
        return $this->only(['id']);
    }

    public function sceneAdd(): WriteCategoryValidate
    {
        return $this
            ->remove('id', 'require')
            ->remove('ids', 'require');
    }

    public function sceneEdit(): WriteCategoryValidate
    {
        return $this
            ->remove('ids', 'require');
    }

    protected function checkForm($value, $rule, $data): bool|string
    {
        unset($rule);
        if (empty($value)) {
            return true;
        }

        $props    = array_column($value,'props');
        $required = array_sum(array_column($props,'isEnable'));
        if (0 == $required) {
            return '表单内容至少一个是启用的';
        }

        $priceCount = 0;
        foreach ($value as $form) {
            $name      = $form['name'];
            $field     = $form['props']['field']    ?? '';
            $title     = $form['props']['title']    ?? '';
            $price     = $form['props']['price']    ?? '';
            $isPrice   = $form['props']['isPrice']  ?? false;
            $isRequired = $form['props']['isRequired'] ?? false;

            if ($isRequired && !strpos($data['prompt_outline'], $field)) {
                return $title . ': 字段为"必填",请在大纲调教文案中输入';
            }

            if ($isPrice) {
                $priceCount += 1;
                if ($name !== 'WidgetRadio') {
                    if (!is_numeric($price) && !$price) {
                        return $title . '字段启用价格必填';
                    }
                } else {
                    $propsPrice = $form['props']['price']??[];
                    if (empty($propsPrice)) {
                        return $title . '字段启用价格必填';
                    }
                    foreach ($propsPrice as $v) {
                        if (!is_numeric($v) && !$v) {
                            return $title . '字段启用价格必填';
                        }
                    }
                }
            }
        }

        if ($priceCount > 1) {
            return '抱歉价格只能启用1个';
        }
        return true;
    }
}