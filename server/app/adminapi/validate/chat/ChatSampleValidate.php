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

namespace app\adminapi\validate\chat;

use app\common\model\chat\ChatCategory;
use app\common\validate\BaseValidate;

/**
 * 问题示例验证类
 */
class ChatSampleValidate extends BaseValidate
{
    protected $rule = [
        'id'          => 'require',
        'category_id' => 'require|checkCategory',
        'sort'        => 'require|number',
        'content'     => 'require',
        'status'      => 'require'
    ];

    protected $message = [
        'id.require'          => '请选择问题示例',
        'category_id.require' => '请选择分类',
        'content.require'     => '请输入内容',
        'sort.require'        => '请输入排序',
        'sort.number'         => '排序值错误',
        'status.require'      => '请选择状态'
    ];

    protected function sceneAdd(): ChatSampleValidate
    {
        return $this->remove('id', true);
    }

    protected function sceneId(): ChatSampleValidate
    {
        return $this->only(['id']);
    }

    protected function checkCategory($value): bool|string
    {
        $category = (new ChatCategory())->where(['id'=>$value])->findOrEmpty();
        if($category->isEmpty()){
            return '分类不存在';
        }
        return true;
    }
}