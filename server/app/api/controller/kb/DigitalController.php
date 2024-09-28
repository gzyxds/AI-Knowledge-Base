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

use app\adminapi\validate\IDMustValidate;
use app\api\controller\BaseApiController;
use app\api\lists\kb\KbDigitalLists;
use app\api\logic\kb\KbDigitalLogic;
use app\api\validate\kb\KbDigitalValidate;
use think\response\Json;

/**
 * AI数字人管理
 */
class DigitalController extends BaseApiController
{
    public array $notNeedLogin = ['lists'];

    /**
     * @notes 语音角色
     * @return Json
     * @author fzr
     */
    public function dubbing(): Json
    {
        $lists = KbDigitalLogic::dubbing();
        return $this->data($lists);
    }

    /**
     * @notes 数字人列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists(new KbDigitalLists());
    }

    /**
     * @notes 数字人详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        (new IDMustValidate())->goCheck();
        $id = intval($this->request->get('id'));

        $detail = KbDigitalLogic::detail($id);
        if (!$detail) {
            return $this->fail('数据丢失');
        }
        return $this->data($detail);
    }

    /**
     * @notes 数字人新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new KbDigitalValidate())->post()->goCheck('add');
        $result = KbDigitalLogic::add($params, $this->userId);
        if (!$result) {
            return $this->fail(KbDigitalLogic::getError());
        }
        return $this->success('添加成功', $result, 1, 1);
    }

    /**
     * @notes 数字人编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new KbDigitalValidate())->post()->goCheck();
        $result = KbDigitalLogic::edit($params, $this->userId);
        if (!$result) {
            return $this->fail(KbDigitalLogic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 删除成功
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new KbDigitalValidate())->post()->goCheck('id');
        $result = KbDigitalLogic::del(intval($params['id']), $this->userId);
        if (!$result) {
            return $this->fail(KbDigitalLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }
}