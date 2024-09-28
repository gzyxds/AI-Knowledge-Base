<?php

namespace app\index\controller;

use app\BaseController;
use app\common\service\JsonService;
use think\facade\Request;
use think\response\Json;
use think\response\View;

class IndexController extends BaseController
{
    /**
     * @notes 主页
     * @param string $name
     * @return Json|View
     * @author 段誉
     * @date 2022/10/27 18:12
     */
    public function index(string $name = '你好'): View|Json
    {
        $template = app()->getRootPath() . 'public/index.html';
        if (Request::isMobile()) {
            $template = app()->getRootPath() . 'public/index.html';
        }
        if (file_exists($template)) {
            return view($template);
        }
        return JsonService::success($name);
    }
}
