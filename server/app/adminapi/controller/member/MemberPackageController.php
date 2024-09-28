<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\adminapi\controller\member;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\member\MemberPackageLists;
use app\adminapi\logic\member\MemberPackageLogic;
use app\adminapi\validate\member\MemberPackageValidate;


/**
 * 会员套餐
 * Class MemberPackageController
 * @package app\adminapi\controller\member
 */
class MemberPackageController extends BaseAdminController
{
    /**
     * @notes 获取配置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/6/14 11:01
     */
    public function getConfig()
    {
        $result = (new MemberPackageLogic())->getConfig();
        return $this->success('',$result);
    }


    /**
     * @notes 设置配置
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/6/14 11:01
     */
    public function setConfig()
    {
        $params = $this->request->post();
        (new MemberPackageLogic())->setConfig($params);
        return $this->success('设置成功',[],1,1);
    }
    
    /**
     * @notes 会员套餐列表
     * @return mixed
     * @author ljj
     * @date 2023/6/27 10:44 上午
     */
    public function lists()
    {
        return $this->dataLists(new MemberPackageLists());
    }

    /**
     * @notes 会员模型
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/5/27 15:47
     */
    public function getModels()
    {
        $result = (new MemberPackageLogic())->getModels();
        return $this->success('',$result);
    }
    /**
     * @notes 添加会员套餐
     * @return mixed
     * @author caijianhao
     * @date 2023/6/27 10:50 上午
     */
    public function add()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('add');
        $result = (new MemberPackageLogic())->add($params);
        if(true === $result){
            return $this->success('操作成功',[],1,1);
        }
        return $this->fail(MemberPackageLogic::getError());
    }

    /**
     * @notes 获取套餐详情
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/5/27 16:25
     */
    public function detail()
    {
        $params = (new MemberPackageValidate())->goCheck('id');
        $result = (new MemberPackageLogic())->detail($params['id']);
        return $this->success('',$result);
    }

    /**
     * @notes 编辑会员权益
     * @return mixed
     * @author ljj
     * @date 2023/6/27 11:02 上午
     */
    public function edit()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('edit');
        $result = (new MemberPackageLogic())->edit($params);
        if(true === $result){
            return $this->success('操作成功',[],1,1);
        }
        return $this->fail(MemberPackageLogic::getError());
    }


    /**
     * @notes 删除会员权益
     * @return mixed
     * @author ljj
     * @date 2023/6/27 11:05 上午
     */
    public function del()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('id');
        $result = (new MemberPackageLogic())->del($params);
        if(true === $result){
            return $this->success('操作成功',[],1,1);
        }
        return $this->fail($result);
    }

    /**
     * @notes 调整状态
     * @return mixed
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function status()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('id');
        (new MemberPackageLogic())->status($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 调整推荐
     * @return mixed
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function recommend()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('id');
        (new MemberPackageLogic())->recommend($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 调整排序
     * @return mixed
     * @author ljj
     * @date 2023/6/27 11:07 上午
     */
    public function sort()
    {
        $params = (new MemberPackageValidate())->post()->goCheck('id');
        (new MemberPackageLogic())->sort($params);
        return $this->success('操作成功',[],1,1);
    }

    /**
     * @notes 公共列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/6/21 8:21 下午
     */
    public function commonLists()
    {
        $result = (new MemberPackageLogic())->commonLists();
        return $this->success('',$result);
    }
}