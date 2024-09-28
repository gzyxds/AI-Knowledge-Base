<?php

namespace app\adminapi\controller\search;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\search\SearchRecordLists;
use app\adminapi\logic\search\RecordLogic;
use think\response\Json;

/**
 * AI搜索记录控制器
 */
class RecordController extends BaseAdminController
{
    /***
     * @notes Ai搜索记录列表
     * @return Json
     * @author fzr
     */
    public function lists(): Json
    {
        return $this->dataLists((new SearchRecordLists()));
    }

    /**
     * @notes AI搜索记录详情
     * @return Json
     * @author fzr
     */
    public function detail(): Json
    {
        $id = intval($this->request->get('id'));
        $result = RecordLogic::detail($id);
        if (!$result) {
            return $this->fail('找不到数据');
        }
        return $this->data($result);
    }

    /**
     * @notes AI搜索记录删除
     * @return Json
     * @author fzr
     */
    public function del(): Json
    {
        $ids = $this->request->get('ids', []);
        $result = RecordLogic::del($ids);
        if (!$result) {
            return $this->fail(RecordLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }
}