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

namespace app\adminapi\controller\recharge;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\recharge\RechargePackageLists;
use app\adminapi\logic\recharge\RechargePackageLogic;
use app\adminapi\validate\recharge\RechargePackageValidate;
use think\response\Json;

/**
 * 充值套餐管理
 */
class PackageController extends BaseAdminController
{
    /**
     * @notes 套餐列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists(new RechargePackageLists());
    }

    /**
     * @notes 套餐详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $id = intval($this->request->get('id', 0));
        $detail = RechargePackageLogic::detail($id);
        return $this->data($detail);
    }

    /**
     * @notes 套餐新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new RechargePackageValidate())->post()->goCheck('add');
        $result = RechargePackageLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(RechargePackageLogic::getError());
    }

    /**
     * @notes 套餐编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new RechargePackageValidate())->post()->goCheck('edit');
        $result = RechargePackageLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(RechargePackageLogic::getError());
    }

    /**
     * @notes 套餐删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new RechargePackageValidate())->post()->goCheck('id');
        $result = RechargePackageLogic::del(intval($params['id']));
        if (true === $result) {
            return $this->success('删除成功', [], 1, 1);
        }
        return $this->fail(RechargePackageLogic::getError());
    }

    /**
     * @notes 套餐状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $post  = $this->request->post();
        $id    = intval($post['id']);
        $field = trim($post['field']??'');
        $value = intval($post['value']??0);

        if (!in_array($field, ['status', 'is_give', 'is_recommend'])) {
            return $this->fail('不支持的字段');
        }

        if (!in_array($value, [0, 1])) {
            return $this->fail('不支持的值');
        }

        $result = RechargePackageLogic::status($id, $field, $value);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(RechargePackageLogic::getError());
    }

    /**
     * @notes 套餐排序
     * @return Json
     * @author fzr
     */
    public function sort(): Json
    {
        $post  = $this->request->post();
        $id    = intval($post['id']);
        $value = intval($post['value']??0);
        if ($value < 0) {
            return $this->fail('排序值不能少于0');
        }

        $result = RechargePackageLogic::sort($id, $value);
        if (true === $result) {
            return $this->success('修改成功', [], 1, 1);
        }
        return $this->fail(RechargePackageLogic::getError());
    }

    /**
     * @notes 充值配置详情
     * @return Json
     * @author fzr
     */
    public function getConfig(): Json
    {
        $result = RechargePackageLogic::getConfig();
        return $this->success('', $result);
    }

    /**
     * @notes 充值配置保存
     * @return Json
     * @author fzr
     */
    public function setConfig(): Json
    {
        RechargePackageLogic::setConfig($this->request->post());
        return $this->success('修改成功', [], 1, 1);
    }
}