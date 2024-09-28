<?php

namespace app\api\controller;

use app\api\logic\SearchLogic;
use app\api\validate\SearchValidate;
use JetBrains\PhpStorm\NoReturn;
use think\response\Json;

/**
 * AI搜索控制器
 */
class SearchController extends BaseApiController
{
    public array $notNeedLogin = ['config', 'example'];

    /**
     * @notes AI搜索配置
     * @return Json
     * @author fzr
     */
    public function config(): Json
    {
        $result = SearchLogic::config($this->userId);
        return $this->success('OK', $result);
    }

    /**
     * @notes AI查询
     * @return void
     * @author fzr
     */
    #[NoReturn]
    public function query(): void
    {
        (new SearchValidate())->post()->goCheck();
        $probe = intval($this->request->post('probe', '0'));
        $model = $this->request->post('model', 'search');
        $type  = $this->request->post('type', 'all');
        $ask   = $this->request->post('ask', '');
        SearchLogic::query($this->userId, $probe, $model, $type, $ask);
        exit();
    }

    /**
     * @notes 搜索示例
     * @return Json
     * @author fzr
     */
    public function example(): Json
    {
        $lists = SearchLogic::example();
        return $this->data($lists);
    }
}