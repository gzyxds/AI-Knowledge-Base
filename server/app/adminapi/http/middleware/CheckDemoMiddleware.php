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
declare (strict_types=1);

namespace app\adminapi\http\middleware;

use app\common\service\JsonService;
use Closure;

/**
 * 校验演示环境
 * Class CheckDemoMiddleware
 * @package app\adminapi\http\middleware
 */
class CheckDemoMiddleware
{
    // 不允许post的接口
    protected array $disablePost = [
        'auth.admin/editself',
        'auth.menu/edit',
        'auth.menu/add',
        'auth.menu/delete',
        'auth.admin/edit',
        'auth.admin/delete',
        'auth.admin/add',
        'auth.role/add',
        'auth.role/edit',
        'auth.role/delete',

        'crontab.crontab/add',
        'crontab.crontab/edit',
        'crontab.crontab/delete',

        'setting.storage/setup',
        'setting.website/setwebsite',

        'setting.system.upgrade/upgrade',
        'setting.ai.models/save',
        'setting.ai.cost/save',

        'setting.keypool/add',
        'setting.keypool/edit',
        'setting.keypool/del',
        'setting.keypool/status',

        'notice.notice/set',
        'notice.smsconfig/setconfig',
        'notice.email/save',

        'adminapi/setting.customer/save',
        'setting.bulletin/save',
        'setting.ai.chat/save',
        'setting.voice/save',
        'setting.contentCensor/save',

        'setting.sensitiveword/add',
        'setting.sensitiveword/edit',
        'setting.sensitiveword/del',
        'setting.website/setcopyright',
        'setting.website/setagreement',

        'setting.user/setconfig',
        'setting.user/setregisterconfig',
        'setting.pay.payconfig/setconfig',
        'setting.pay.payway/setpayway'
    ];

    public function handle($request, Closure $next)
    {
        if ($request->method() != 'POST') {
            return $next($request);
        }

        if ($request->controllerObject->isNotNeedLogin()) {
            return $next($request);
        }

        if (1 === $request->adminInfo['root'])  {
            return $next($request);
        }

        $accessUri = strtolower($request->controller() . '/' . $request->action());
        if (in_array($accessUri, $this->disablePost) && env('project.demo_env')) {
            return JsonService::fail('演示环境不支持修改数据，请下载源码本地部署体验');
        }

        return $next($request);
    }
}