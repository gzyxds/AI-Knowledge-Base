<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\adminapi\controller\music;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\music\MusicSquareLists;
use app\adminapi\validate\music\MusicSquareValidate;
use app\adminapi\logic\music\MusicSquareLogic;


class MusicSquareController extends BaseAdminController
{
    /**
     * @notes 音乐广场列表
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:09 下午
     */
    public function lists()
    {
        return $this->dataLists(new MusicSquareLists());
    }

//    /**
//     * @notes 添加
//     * @return mixed
//     * @author ljj
//     * @date 2023/8/31 12:15 下午
//     */
//    public function add()
//    {
//        $params = (new DrawSquareValidate())->post()->goCheck('add',['admin_id'=>$this->adminId]);
//        DrawSquareLogic::add($params);
//        return $this->success('操作成功', [], 1, 1);
//    }

    /**
     * @notes 编辑
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public function edit()
    {
        $params = (new MusicSquareValidate())->post()->goCheck('edit');
        MusicSquareLogic::edit($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 详情
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:16 下午
     */
    public function detail()
    {
        $params = (new MusicSquareValidate())->goCheck('detail');
        $result = MusicSquareLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 删除
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:17 下午
     */
    public function del()
    {
        $params = (new MusicSquareValidate())->post()->goCheck('del');
        MusicSquareLogic::del($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 显示状态
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:18 下午
     */
    public function isShow()
    {
        $params = (new MusicSquareValidate())->post()->goCheck('isShow');
        MusicSquareLogic::isShow($params['id']);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 审核状态
     * @return mixed
     * @author ljj
     * @date 2023/8/31 12:19 下午
     */
    public function verifyStatus()
    {
        $params = (new MusicSquareValidate())->post()->goCheck('verifyStatus');
        $result = MusicSquareLogic::verifyStatus($params);
        if(true === $result){
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->success($result);
    }

    /**
     * @notes 获取音乐广场配置
     * @return mixed
     * @author ljj
     * @date 2023/8/31 2:49 下午
     */
    public function getConfig()
    {
        $result = MusicSquareLogic::getConfig();
        return $this->success('',$result);
    }

    /**
     * @notes 设置音乐广场配置
     * @return mixed
     * @author ljj
     * @date 2023/8/31 2:50 下午
     */
    public function setConfig()
    {
        $params = (new MusicSquareValidate())->post()->goCheck('setConfig');
        MusicSquareLogic::setConfig($params);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 批量移动分类
     * @return mixed
     * @author cjhao
     * @date 2024/8/8 17:43
     */
    public function removeCategory()
    {
        $params = $this->request->post();
        $result = (new MusicSquareLogic())->removeCategory($params);
        if(true === $result){
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail($result);
    }
}