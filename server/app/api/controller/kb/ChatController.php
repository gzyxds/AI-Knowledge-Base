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

namespace app\api\controller\kb;

use app\api\controller\BaseApiController;
use app\api\logic\kb\KbChatLogic;
use app\api\service\KbChatService;
use app\api\validate\kb\KbDataValidate;
use app\common\model\kb\KbRobotPublish;
use app\common\model\kb\KbRobotRecord;
use app\common\service\ai\ChatService;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use think\db\exception\DbException;
use think\response\Json;

/**
 * 机器人对话管理
 */
class ChatController extends BaseApiController
{
     public array $notNeedLogin = ['chat', 'chatRecord', 'chatClean', 'test', 'feedback'];

    /**
     * @notes 机器人对话
     * @throws Exception
     * @author fzr
     */
    #[NoReturn]
    public function chat()
    {
        $apiKey   = $this->request->header('Authorization');
        $identity = $this->request->header('identity');
        $headers  = $this->request->header();
        $post     = $this->request->post();

        $stream = (bool) ($post['stream']??'');
        try {
            // 问题兼容处理
            $question = $post['question']??'';
            if (!empty($post['messages'])) {
                $question = $post['messages'][count($post['messages']) -1]['content'];
                $post['question'] = $question;
            }

            if ($apiKey) {
                // 对接微信时的密钥处理
                $keys   = explode(" ", $apiKey);
                $apiKey = count($keys) >= 2 ? $keys[1] : $keys[0];

                // 查发布渠道
                $modelKbRobotPublish = new KbRobotPublish();
                $publish = $modelKbRobotPublish->where(['apikey'=>$apiKey])->findOrEmpty()->toArray();
                if (!$publish) {
                    throw new Exception('apiKey校验不通过!');
                }

                // 验证密码
                if ($publish['secret']) {
                    if ($publish['secret'] !== ($headers['password']??'')) {
                        throw new Exception('访问密码错误!', 1200);
                    }
                }

                // 验证限制
                $this->checkShareChat($publish, $identity);

                // 对话参数
                $chatParams = [
                    'apiKey'      => $apiKey,
                    'identity'    => $identity,
                    'question'    => $question,
                    'share_id'    => intval($publish['id']),
                    'robot_id'    => intval($publish['robot_id']),
                    'context_num' => intval($publish['context_num'])
                ];

                $result = (new KbChatService($chatParams, $publish['user_id'], $stream))->chat();
                return json($result);
            } else {
                if (!$this->userId) {
                    throw new Exception('请先登录');
                }
                (new KbChatService($post, $this->userId))->chat();
            }
        } catch (Exception $e) {
            $result = ChatService::parseReturnError($stream, $e->getMessage(), $e->getCode());
            if (!$stream) {
                return json($result);
            }
        }

        exit;
    }

    /**
     * @notes 对话记录列表
     * @throws DbException
     * @author fzr
     */
    public function chatRecord(): Json
    {
        $get  = $this->request->get();
        $get['apikey']   = $this->request->header('Authorization', '');
        $get['identity'] = $this->request->header('identity', '');
        $get['password'] = $this->request->header('password', '');

        $list = KbChatLogic::chatRecord($get, $this->userId);
        if (!$list) {
            return $this->fail(KbChatLogic::getError());
        }

        return $this->data($list);
    }

    /**
     * @notes 对话记录清空
     * @return Json
     * @author fzr
     */
    public function chatClean(): Json
    {
        $post = $this->request->post();
        $post['apikey']   = $this->request->header('Authorization', '');
        $post['identity'] = $this->request->header('identity', '');
        $post['password'] = $this->request->header('password', '');

        $result = KbChatLogic::chatClean($post, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success();
    }

    /**
     * @notes 对话数据修正
     * @return Json
     * @author fzr
     */
    public function chatCorrect(): Json
    {
        $post = $this->request->post();
        $result = KbChatLogic::chatCorrect($post, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('修正成功', [], 1, 1);
    }

    /**
     * @notes 对话分类列表
     * @return Json
     * @throws DbException
     * @author fzr
     */
    public function cateLists(): Json
    {
        $robotId = intval($this->request->get('robot_id', 0));
        $lists = KbChatLogic::cateLists($robotId, $this->userId);
        return $this->data($lists);
    }

    /**
     * @notes 对话分类新增
     * @return Json
     * @author fzr
     */
    public function cateAdd(): Json
    {
        $robotId = intval($this->request->post('robot_id', 0));
        $result = KbChatLogic::cateAdd($robotId, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('创建成功', []);
    }

    /**
     * @notes 对话分类编辑
     * @return Json
     * @author fzr
     */
    public function cateEdit(): Json
    {
        $id      = $this->request->post('id', 0);
        $robotId = $this->request->post('robot_id', 0);
        $name    = $this->request->post('name', '');
        $result = KbChatLogic::cateEdit($id, $robotId, $name, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('修改成功', []);
    }

    /**
     * @notes 对话分类删除
     * @return Json
     * @author fzr
     */
    public function cateDel(): Json
    {
        $id      = intval($this->request->post('id', 0));
        $robotId = intval($this->request->post('robot_id', 0));
        $result = KbChatLogic::cateDel($id, $robotId, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('删除成功', []);
    }

    /**
     * @notes 对话分类清空
     * @return Json
     * @author fzr
     */
    public function cateClear(): Json
    {
        $robotId = intval($this->request->post('robot_id', 0));
        $result = KbChatLogic::cateClear($robotId, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('清除成功', []);
    }

    /**
     * @notes 对话数据统计
     * @return Json
     * @throws DbException
     * @author fzr
     */
    public function dataCount(): Json
    {
        (new KbDataValidate())->get()->goCheck('robotId');
        $robotId = intval($this->request->get('robot_id'));

        $detail = KbChatLogic::dataCount($robotId, $this->userId);
        return $this->data($detail);
    }

    /**
     * @notes 对话数据记录
     * @return Json
     * @throws DbException
     * @author fzr
     */
    public function dataRecord(): Json
    {
        (new KbDataValidate())->get()->goCheck('robotId');
        $lists = KbChatLogic::dataRecord($this->request->get());
        return $this->data($lists);
    }

    /**
     * @notes 对话数据修正
     * @return Json
     * @author fzr
     */
    public function dataRevise(): Json
    {
        $params = (new KbDataValidate())->post()->goCheck('revise');
        $result = KbChatLogic::dataRevise($params, $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('操作成功', [], 1, 1);
    }

    /**
     * @notes 对话数据删除
     * @return Json
     * @author fzr
     */
    public function dataDelete(): Json
    {
        $params = (new KbDataValidate())->post()->goCheck('delete');
        $result = KbChatLogic::dataDelete(intval($params['robot_id']), $params['ids'], $this->userId);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('删除成功', [], 1, 1);
    }

    /**
     * @notes 对话记录反馈
     * @return Json
     * @author fzr
     */
    public function feedback(): Json
    {
        $params = (new KbDataValidate())->post()->goCheck('feedback');
        $result = KbChatLogic::feedback(intval($params['robot_id']), $params['record_id'], $this->userId, $params['content']);
        if ($result === false) {
            return $this->fail(KbChatLogic::getError());
        }
        return $this->success('反馈成功', [], 1, 1);
    }

    /**
     * @notes 验证分享对话是否超出
     * @param array $publish
     * @param $identity
     * @throws DbException
     * @throws Exception
     * @author fzr
     */
    private function checkShareChat(array $publish, $identity): void
    {
        $modelKbRobotRecord = new KbRobotRecord();
        $limitTotalChat = $publish['limit_total_chat'];
        $limitTodayChat = $publish['limit_today_chat'];
        $limitExceedErr = $publish['limit_exceed'];

        // 验证: 限制每个用户累计总对话数
        if ($limitTotalChat) {
            $totalChatCount = $modelKbRobotRecord
                ->where(['robot_id'=>$publish['robot_id']])
                ->where(['share_id'=>$publish['id']])
                ->where(['share_identity'=>$identity])
                ->count();

            if ($totalChatCount >= $limitTotalChat) {
                throw new Exception($limitExceedErr?:'超出累计限制对话数');
            }
        }

        // 验证: 限制每个用户累计总对话数
        if ($limitTodayChat) {
            $todayChatCount = $modelKbRobotRecord
                ->where(['robot_id'=>$publish['robot_id']])
                ->where(['share_id'=>$publish['id']])
                ->where(['share_identity'=>$identity])
                ->whereDay('create_time')
                ->count();

            if ($todayChatCount >= $limitTotalChat) {
                throw new Exception($limitExceedErr?:'超出每天限制对话数');
            }
        }
    }
}