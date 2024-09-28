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

namespace app\api\validate\kb;

use app\common\validate\BaseValidate;

/**
 * 知识库参数验证器
 */
class KbKnowValidate extends BaseValidate
{
    protected $rule = [
        'id'              => 'require|number',
        'fd_id'           => 'require|number',
        'kb_id'           => 'require|number',
//        'to_fd_id'        => 'require|number',
        'image'           => 'require|max:250',
        'name'            => 'require|max:100',
        'intro'           => 'max:500',
        'embedding_model' => 'require',
        'documents_model' => 'require'
    ];

    protected $message = [
        'kb_id.require'           => '请指定知识库',
        'fd_id.require'           => '请选择文件',
//        'to_fd_id.require'        => '请选择目标文件',
        'image.require'           => '请选择封面图',
        'image.max'               => '封面选择异常',
        'name.require'            => '请填写名称',
        'name.max'                => '名称不能大于100个字符',
        'intro.max'               => '知识库简介不能大于500个字符',
        'embedding_model_id.require'     => '请选择向量模型',
        'documents_model_id.require'     => '请选择处理模型',
        'documents_model_sub_id.require' => '请选择处理模型',
    ];

    /**
     * @notes ID场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneId(): KbKnowValidate
    {
        return $this->only(['id']);
    }

    /**
     * @notes 新增场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneAdd(): KbKnowValidate
    {
        return $this->only(['image', 'name', 'intro', 'embedding_model_id', 'documents_model_id', 'documents_model_sub_id']);
    }

    /**
     * @notes 编辑场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneEdit(): KbKnowValidate
    {
        return $this->only(['id', 'image', 'name', 'intro', 'documents_model_id', 'documents_model_sub_id']);
    }

    /**
     * @notes 重命名场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneRename(): KbKnowValidate
    {
        return $this->only(['fd_id', 'name']);
    }

//    /**
//     * @notes 文件迁移场景
//     * @return KbKnowValidate
//     * @author fzr
//     */
//    public function sceneMove(): KbKnowValidate
//    {
//        return $this->only(['kb_id', 'fd_id', 'to_fd_id']);
//    }

    /**
     * @notes 文件ID场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneFid(): KbKnowValidate
    {
        return $this->only(['fd_id']);
    }

    /**
     * @notes 知识库场景
     * @return KbKnowValidate
     * @author fzr
     */
    public function sceneKid(): KbKnowValidate
    {
        return $this->only(['kb_id']);
    }
}