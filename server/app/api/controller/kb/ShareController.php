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

namespace app\api\controller\kb;

use app\api\controller\BaseApiController;
use app\api\logic\kb\KbShareLogic;
use app\api\validate\kb\KbShareValidate;
use think\response\Json;

/**
 * 机器人发布分享管理
 */
class ShareController extends BaseApiController
{
    public array $notNeedLogin = ['detail'];

    /**
     * @notes 发布列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        (new KbShareValidate())->get()->goCheck('lists');
        $lists = KbShareLogic::lists($this->request->get(), $this->userId);
        return $this->data($lists);
    }

    /**
     * @notes 发布详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $apiKey = $this->request->get('apikey', '');
        $detail = KbShareLogic::detail($apiKey, $this->terminal);
        if (empty($detail)) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->data($detail);
    }

    /**
     * @notes 发布创建
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new KbShareValidate())->post()->goCheck('add');
        $result = KbShareLogic::add($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->success('发布成功', [], 1, 1);
    }

    /**
     * @notes 发布编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new KbShareValidate())->post()->goCheck('edit');
        $result = KbShareLogic::edit($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 发布更新
     * @return Json
     * @author fzr
     */
    public function update(): Json
    {
        $params = (new KbShareValidate())->post()->goCheck('update');
        $result = KbShareLogic::update($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->success('更新成功', [], 1, 1);
    }

    /**
     * @notes 发布删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new KbShareValidate())->post()->goCheck('del');
        $result = KbShareLogic::del(intval($params['id']), $this->userId);
        if ($result === false) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 设置分享背景图
     * @return Json
     * @author fzr
     */
    public function setBg(): Json
    {
        $params = (new KbShareValidate())->post()->goCheck('setBg');
        $result = KbShareLogic::setBg(intval($params['id']), $this->userId, $params['url']);
        if ($result === false) {
            return $this->fail(KbShareLogic::getError());
        }
        return $this->success('设置成功', [], 1, 1);
    }
}