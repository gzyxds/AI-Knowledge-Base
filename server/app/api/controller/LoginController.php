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

namespace app\api\controller;

use app\api\validate\{LoginAccountValidate, RegisterValidate, WebScanLoginValidate, WechatLoginValidate};
use app\api\logic\LoginLogic;
use think\response\Json;


/**
 * 登录注册
 */
class LoginController extends BaseApiController
{
    public array $notNeedLogin = ['register', 'oaAuthBind','account', 'logout', 'codeUrl', 'oaLogin',  'mnpLogin','qrcode', 'getScanCode', 'scanLogin','ticket'];

    /**
     * @notes 注册账号
     * @return Json
     * @author 段誉
     * @date 2022/9/7 15:38
     */
    public function register(): Json
    {
        $params = (new RegisterValidate())->post()->goCheck('register');
        $result = LoginLogic::register($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('注册成功', $result, 1, 1);

    }

    /**
     * @notes 账号密码/手机号密码/手机号验证码登录
     * @return Json
     * @author 段誉
     * @date 2022/9/16 10:42
     */
    public function account(): Json
    {
        $params = (new LoginAccountValidate())->post()->goCheck();
        $result = LoginLogic::login($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes 退出登录
     * @return Json
     * @author 段誉
     * @date 2022/9/16 10:42
     */
    public function logout(): Json
    {
        LoginLogic::logout($this->userInfo);
        return $this->success();
    }

    /**
     * @notes 获取微信请求code的链接
     * @return Json
     * @author 段誉
     * @date 2022/9/15 18:27
     */
    public function codeUrl(): Json
    {
        $url = $this->request->get('url');
        $result = LoginLogic::codeUrl($url);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        $result = ['url' => LoginLogic::codeUrl($url)];
        return $this->success('获取成功', $result);
    }

    /**
     * @notes 公众号登录
     * @return Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function oaLogin(): Json
    {
        $params = (new WechatLoginValidate())->post()->goCheck('oa');
        $res = LoginLogic::oaLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }

    /**
     * @notes 小程序-登录接口
     * @return Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function mnpLogin(): Json
    {
        $params = (new WechatLoginValidate())->post()->goCheck('mnpLogin');
        $res = LoginLogic::mnpLogin($params);
        if (false === $res) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $res);
    }

    /**
     * @notes 小程序绑定微信
     * @return Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function mnpAuthBind(): Json
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::mnpAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功', [], 1, 1);
    }

    /**
     * @notes 公众号绑定微信
     * @return Json
     * @author 段誉
     * @date 2022/9/20 19:48
     */
    public function oaAuthBind(): Json
    {
        $params = (new WechatLoginValidate())->post()->goCheck("wechatAuth");
        $params['user_id'] = $this->userId;
        $result = LoginLogic::oaAuthLogin($params);
        if ($result === false) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('绑定成功',$result, 1, 1);
    }

    /**
     * @notes 获取扫码地址
     * @return Json
     * @author 段誉
     * @date 2022/10/20 18:25
     */
    public function getScanCode(): Json
    {
        $redirectUri = $this->request->get('url/s');
        $result = LoginLogic::getScanCode($redirectUri);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '未知错误');
        }
        return $this->success('', $result);
    }

    /**
     * @notes 网站扫码登录
     * @return Json
     * @author 段誉
     * @date 2022/10/21 10:28
     */
    public function scanLogin(): Json
    {
        $params = (new WebScanLoginValidate())->post()->goCheck();
        $result = LoginLogic::scanLogin($params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError() ?? '登录失败');
        }
        return $this->success('', $result);
    }

    /**
     * @notes 更新用户头像昵称
     * @return Json
     * @author 段誉
     * @date 2023/2/22 11:15
     */
    public function updateUser(): Json
    {
        $params = (new WechatLoginValidate())->post()->goCheck("updateUser");
        LoginLogic::updateUser($params, $this->userId);
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 二维码登录
     * @return Json
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @author cjhao
     * @date 2024/5/30 14:40
     */
    public function qrcode():Json
    {
        $requestCheck = LoginLogic::requestLimit(1, 15);
        if ($requestCheck === false) {
            return $this->fail('请求过于频繁，5分钟后重试。');
        }
        $params = $this->request->get();
        $result = LoginLogic::getLoginCode($this->userId,$params);
        if (false === $result) {
            return $this->fail(LoginLogic::getError());
        }
        return $this->success('', $result);
    }


    /**
     * @notes 扫码验证
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/7/25 15:37
     */
    public function ticket()
    {
        $key = $this->request->post('key');
        $channel = $this->request->post('channel','login');
        if ($channel == 'bind') {
            $result = LoginLogic::checkBindTicket($key);
        } else {
            $result = LoginLogic::checkTicket($key);
        }
        return $this->success($result['msg'], $result['data'], $result['code'], $result['show']);
    }

}