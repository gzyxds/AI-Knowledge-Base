<?php
namespace app\api\controller;
use app\api\logic\ShareLogic;

class TaskController extends BaseApiController
{

    /**
     * @notes 分享接口
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/18 4:59 下午
     */
    public function share()
    {
        $type = $this->request->get('type');
        $result = (new ShareLogic())->share($type,$this->userInfo);
        if (false === $result) {
            return $this->fail(ShareLogic::getError());
        }
        return $this->success('',$result);
    }
}