<?php

namespace app\api\controller;

use app\api\logic\NoticeLogic;
use think\response\Json;

class NoticeController extends BaseApiController
{
    /**
     * @notes 通知列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        $lists = NoticeLogic::lists($this->request->get(), $this->userId);
        return $this->data($lists);
    }

    /**
     * @notes 标记已读
     * @return Json
     * @author fzr
     */
    public function read(): Json
    {
        $id = intval($this->request->post('id', 0));
        NoticeLogic::read($id, $this->userId);
        return $this->success('操作成功');
    }

    /**
     * @notes 全部标记已读
     * @return Json
     * @author fzr
     */
    public function allRead(): Json
    {
        NoticeLogic::allRead($this->userId);
        return $this->success('操作成功');
    }

    /**
     * @notes 清空所有已读
     * @return Json
     * @author fzr
     */
    public function clear(): Json
    {
        NoticeLogic::clear($this->userId);
        return $this->success('清空成功');
    }
}