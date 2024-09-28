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

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\WebsiteLogic;
use app\adminapi\validate\setting\WebsiteValidate;
use think\response\Json;

/**
 * 网站设置
 */
class WebsiteController extends BaseAdminController
{
    /**
     * @notes 获取网站信息
     * @return Json
     * @author 段誉
     * @date 2021/12/28 15:44
     */
    public function getWebsite(): Json
    {
        $result = WebsiteLogic::getWebsiteInfo();
        return $this->data($result);
    }

    /**
     * @notes 设置网站信息
     * @return Json
     * @author 段誉
     * @date 2021/12/28 15:45
     */
    public function setWebsite(): Json
    {
        $params = (new WebsiteValidate())->post()->goCheck('website');
        WebsiteLogic::setWebsiteInfo($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 获取备案信息
     * @return Json
     * @author 段誉
     * @date 2021/12/28 16:10
     */
    public function getCopyright(): Json
    {
        $result = WebsiteLogic::getCopyright();
        return $this->data($result);
    }

    /**
     * @notes 设置备案信息
     * @return Json
     * @author 段誉
     * @date 2021/12/28 16:10
     */
    public function setCopyright(): Json
    {
        $params = $this->request->post();
        $result = WebsiteLogic::setCopyright($params);
        if (false === $result) {
            return $this->fail(WebsiteLogic::getError() ?: '操作失败');
        }
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 设置政策协议
     * @return Json
     * @author ljj
     * @date 2022/2/15 11:00 上午
     */
    public function setAgreement(): Json
    {
        $params = $this->request->post();
        WebsiteLogic::setAgreement($params);
        return $this->success('设置成功', [], 1, 1);
    }

    /**
     * @notes 获取政策协议
     * @return Json
     * @author ljj
     * @date 2022/2/15 11:16 上午
     */
    public function getAgreement(): Json
    {
        $result = WebsiteLogic::getAgreement();
        return $this->data($result);
    }
}