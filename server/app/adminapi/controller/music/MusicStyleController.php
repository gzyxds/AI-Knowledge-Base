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

namespace app\adminapi\controller\music;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\music\MusicStyleLists;
use app\adminapi\logic\music\MusicStyleLogic;
use app\adminapi\validate\music\MusicStyleValidate;
use think\response\Json;

/**
 * Class StyleController
 * @package app\adminapi\controller\music
 */
class MusicStyleController extends BaseAdminController
{
    /**
     * @notes 曲风列表
     * @return Json
     * @author mjf
     * @date 2024/5/27 11:55
     */
    public function lists(): Json
    {
        return $this->dataLists((new MusicStyleLists()));
    }

    /**
     * @notes 详情
     * @return Json
     * @author mjf
     * @date 2024/5/27 12:02
     */
    public function detail(): Json
    {
        $params = (new MusicStyleValidate())->get()->goCheck('id');
        $result = MusicStyleLogic::detail(intval($params['id']));
        return $this->data($result);
    }

    /**
     * @notes 添加
     * @return Json
     * @author mjf
     * @date 2024/5/27 12:15
     */
    public function add(): Json
    {
        $params = (new MusicStyleValidate())->post()->goCheck('add');
        $result = MusicStyleLogic::add($params);
        if ($result === false) {
            return $this->fail(MusicStyleLogic::getError());
        }
        return $this->success('添加成功', [], 1, 1);
    }

    /**
     * @notes 编辑
     * @return Json
     * @author mjf
     * @date 2024/5/27 12:15
     */
    public function edit(): Json
    {
        $params = (new MusicStyleValidate())->post()->goCheck();
        $result = MusicStyleLogic::edit($params);
        if ($result === false) {
            return $this->fail(MusicStyleLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }

    /**
     * @notes 删除
     * @return Json
     * @author mjf
     * @date 2024/5/27 12:15
     */
    public function del(): Json
    {
        $params = (new MusicStyleValidate())->post()->goCheck('id');
        $result = MusicStyleLogic::del($params['id']);
        if ($result === false) {
            return $this->fail(MusicStyleLogic::getError() ?: '删除失败');
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 状态
     * @return Json
     * @author mjf
     * @date 2024/5/27 12:15
     */
    public function status(): Json
    {
        $params = (new MusicStyleValidate())->post()->goCheck('id');
        $result = MusicStyleLogic::status(intval($params['id']));
        if ($result === false) {
            return $this->fail(MusicStyleLogic::getError());
        }
        return $this->success('修改成功', [], 1, 1);
    }
}