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
use app\api\lists\kb\KbTeachLists;
use app\api\logic\kb\KbTeachLogic;
use app\api\validate\kb\KbTeachValidate;
use think\response\Json;

/**
 * 训练数据管理
 */
class TeachController extends BaseApiController
{
    /**
     * @notes 训练数据列表
     * @return Json
     * @author fzr
     */
    public function datas(): Json
    {
        return $this->dataLists((new KbTeachLists()));
    }

    /**
     * @notes 检测数据状态
     * @return Json
     * @author fzr
     */
    public function detection(): Json
    {
        //$params = (new KbTeachValidate())->post()->goCheck('check');
        $params = $this->request->post();
        $detail = KbTeachLogic::detection($this->userId, intval($params['kb_id']), intval($params['fd_id']), $params['uuids']);
        return $this->data($detail);
    }

    /**
     * @notes 训练数据详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new KbTeachValidate())->get()->goCheck('uuid');
        $detail = KbTeachLogic::detail($params['uuid']);
        return $this->data($detail);
    }

    /**
     * @notes 训练数据删除
     * @return Json
     * @author fzr
     */
    public function delete(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('delete');
        $result = KbTeachLogic::delete($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 训练数据修正
     * @return Json
     * @author fzr
     */
    public function update(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('update');
        $result = KbTeachLogic::update($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 训练失败重试
     * @return Json
     * @author fzr
     */
    public function reset(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('reset');
        $result = KbTeachLogic::reset($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('发起成功', [], 1, 1);
    }

    /**
     * @notes 训练数据录入 (手动录入)
     * @return Json
     * @author fzr
     */
    public function insert(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('insert');
        $result = KbTeachLogic::insert($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('录入成功', [], 1, 1);
    }

    /**
     * @notes 导入训练数据 (文件导入)
     * @return Json
     * @author fzr
     */
    public function import(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('import', ['user_id'=>$this->userId]);
        $result = KbTeachLogic::import($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('导入成功', [], 1, 1);
    }

    /**
     * @notes 搜索测试
     * @return Json
     * @author fzr
     */
    public function tests(): Json
    {
        $params = (new KbTeachValidate())->post()->goCheck('tests');
        $result = KbTeachLogic::tests($params['kb_id'], $params['question']);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes 预估价格计算
     * @return Json
     * @author fzr
     */
    public function charging(): Json
    {
        $kbId = $this->request->post('kb_id', 0);
        $text = $this->request->post('text', '');
        $result = KbTeachLogic::charging($kbId, $text);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes QA检测
     * @return Json
     * @author fzr
     */
    public function qaCheck(): Json
    {
        $fdIds = $this->request->post('fd_ids', []);
        $result = KbTeachLogic::qaCheck($fdIds, $this->userId);
        return $this->data($result);
    }

    /**
     * @notes QA拆分重试
     * @return Json
     * @author fzr
     */
    public function qaRetry(): Json
    {
        $kbId = intval($this->request->post('kb_id', 0));
        $fdId = intval($this->request->post('fd_id', 0));
        $result = KbTeachLogic::qaRetry($kbId, $fdId, $this->userId);
        if ($result === false) {
            return $this->fail(KbTeachLogic::getError());
        }
        return $this->success('已发起重试', [], 1, 1);
    }

    public function capture(): Json
    {
        $url  = $this->request->post('url',[]);
        $result = KbTeachLogic::capture($url);
        if (is_array($result)) {
            return $this->success('抓取成功', $result, 1, 1);
        }
        return $this->fail(KbTeachLogic::getError());
    }
}