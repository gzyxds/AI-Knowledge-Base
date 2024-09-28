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

namespace app\api\controller\chat;

use app\api\controller\BaseApiController;
use app\api\lists\chat\ChatRecordCollectLists;
use app\api\lists\chat\ChatRecordLogLists;
use app\api\logic\chat\ChatRecordLogic;
use app\api\validate\chat\ChatRecordValidate;
use think\response\Json;

/**
 * 对话记录管理
 */
class ChatRecordController extends BaseApiController
{
    public array $notNeedLogin = ['chatRecord'];

    /**
     * @notes 对话记录
     * @return Json
     * @author fzr
     */
    public function chatRecord(): Json
    {
        return $this->dataLists((new ChatRecordLogLists()));
    }

    /**
     * @notes 对话清除
     * @return Json
     * @author fzr
     */
    public function chatClean(): Json
    {
        $params = (new ChatRecordValidate())->post()->goCheck('chatClean', ['user_id' => $this->userId]);
        $result = ChatRecordLogic::chatClean($params);
        if ($result === false) {
            return $this->fail(ChatRecordLogic::getError());
        }
        if (isset($params['id']) && $params['id']) {
            return $this->success('操作成功');
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 收藏列表
     * @return Json
     * @author fzr
     */
    public function collectLists(): Json
    {
        return $this->dataLists((new ChatRecordCollectLists()));
    }

    /**
     * @notes 收藏加入
     * @return Json
     * @author fzr
     */
    public function collectCreate(): Json
    {
        $params = (new ChatRecordValidate())->post()->goCheck('collectCreate', ['user_id' => $this->userId]);
        $result = ChatRecordLogic::collectCreate($params);
        if ($result === false) {
            return $this->fail(ChatRecordLogic::getError());
        }
        return $this->success('加入收藏成功', [], 1, 1);
    }

    /**
     * @notes 收藏取消
     * @return Json
     * @author fzr
     */
    public function collectCancel(): Json
    {
        $params = (new ChatRecordValidate())->post()->goCheck('collectCancel', ['user_id' => $this->userId]);
        $result = ChatRecordLogic::collectCancel($params);
        if ($result === false) {
            return $this->fail(ChatRecordLogic::getError());
        }
        return $this->success('取消收藏成功', [], 1, 1);
    }

    /**
     * @notes 更新对话记录
     * @return Json
     * @author cjhao
     * @date 2024/6/14 09:44
     */
    public function update(): Json
    {
        $params = $this->request->post();
        $result = ChatRecordLogic::update($params,$this->userId);
        if(true === $result){
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(ChatRecordLogic::getError());

    }


    /**
     * @notes 删除对话记录
     * @return Json
     * @author cjhao
     * @date 2024/8/13 11:12
     */
    public function del():Json
    {
        $params = $this->request->post();
        $result = ChatRecordLogic::del($params,$this->userId);
        if ($result !== true) {
            return $this->fail($result);
        }
        return $this->success('删除成功', [], 1, 1);
    }
}