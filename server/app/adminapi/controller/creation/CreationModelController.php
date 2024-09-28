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

namespace app\adminapi\controller\creation;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\creation\CreationModelLists;
use app\adminapi\logic\creation\CreationModelLogic;
use app\adminapi\validate\creation\CreationModelValidate;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use think\response\File;
use think\response\Json;

/**
 * 创作模型控制器类
 */
class CreationModelController extends BaseAdminController
{
    public array $notNeedLogin = ['downExcelTemplate'];

    /**
     * @notes 创作模型列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new CreationModelLists()));
    }

    /**
     * @notes 创作模型详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $params = (new CreationModelValidate())->get()->goCheck('id');
        $result = CreationModelLogic::detail(intval($params['id']));
        return $this->data($result);
    }

    /**
     * @notes 创作模型新增
     * @return Json
     * @author fzr
     */
    public function add(): Json
    {
        $params = (new CreationModelValidate())->post()->goCheck('add');
        $result = CreationModelLogic::add($params);
        if ($result === false) {
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 创作模型编辑
     * @return Json
     * @author fzr
     */
    public function edit(): Json
    {
        $params = (new CreationModelValidate())->post()->goCheck();
        $result = CreationModelLogic::edit($params);
        if ($result === false) {
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success('编辑成功', [], 1, 1);
    }

    /**
     * @notes 创作模型删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $params = (new CreationModelValidate())->post()->goCheck('id');
        $result = CreationModelLogic::del(intval($params['id']));
        if ($result === false) {
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 批量删除
     * @return Json
     * @author cjhao
     * @date 2024/7/17 10:27
     */
    public function batchDel():Json
    {
        $ids = $this->request->post('ids');
        $result = CreationModelLogic::batchDel($ids);
        if ($result === false) {
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 创作模型状态
     * @return Json
     * @author fzr
     */
    public function status(): Json
    {
        $params = (new CreationModelValidate())->post()->goCheck('id');
        $result = CreationModelLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 导入数据
     * @return Json
     * @throws Exception
     * @author cjhao
     * @date 2024/4/11 17:10
     */
    public function import(): Json
    {
        $file = $this->request->file('file');
        $result = (new CreationModelLogic())->import($file);
        if(false === $result){
            return $this->fail(CreationModelLogic::getError());
        }
        return $this->success($result,[],1,1);
    }

    /**
     * @notes 下载模板
     * @return File
     * @author cjhao
     * @date 2024/4/15 11:53
     */
    public function downExcelTemplate(): File
    {
        return (new CreationModelLogic())->downExcelTemplate();
    }

    /**
     * @notes 创作导出
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/4/12 11:49
     */
    public function export()
    {
        $params = $this->request->get();
        $result = (new CreationModelLogic())->export($params);
        if(false === $result){
            return $this->fail($result); 
        }
        return $this->success('',$result);

    }
}