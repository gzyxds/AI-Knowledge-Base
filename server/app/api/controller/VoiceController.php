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

use app\api\logic\VoiceLogic;
use app\common\model\kb\KbRobotPublish;
use think\response\Json;

class VoiceController extends BaseApiController
{
    public array $notNeedLogin = ['generate', 'transfer'];

    /**
     * @notes 语音合成 (文字转语音)
     * @return Json
     * @author fzr
     */
    public function generate(): Json
    {
        $apiKey   = $this->request->header('Authorization');
        $type     = intval($this->request->post('type', 2));
        $recordId = intval($this->request->post('record_id', 0));
        $headers  = $this->request->header();

        $userId = $this->userId;
        if ($apiKey) {
            $keys = explode(" ", $apiKey);
            $apiKey = count($keys) >= 2 ? $keys[1] : $keys[0];

             // 查发布渠道
            $modelKbRobotPublish = new KbRobotPublish();
            $publish = $modelKbRobotPublish->where(['apikey'=>$apiKey])->findOrEmpty()->toArray();
            if (!$publish) {
                return $this->fail('apiKey校验不通过!');
            }

            // 验证密码
            if ($publish['secret']) {
                if ($publish['secret'] !== ($headers['password']??'')) {
                    return $this->fail('apiKey校验不通过!', [], 1200);
                }
            }
            $userId = 0;
        }

        $result = VoiceLogic::generate($type, $recordId, $userId);
        if ($result === false) {
            return $this->fail(VoiceLogic::getError());
        }
        return $this->success('OK', $result);
    }

    /**
     * @notes 语音识别 (语音转文字)
     * @return Json
     * @author fzr
     */
    public function transfer(): Json
    {
        $file = $this->request->file('file');
        if (!$file) {
            return $this->fail('请上传音频文件');
        }

        $result = VoiceLogic::transfer($this->userId, $file);
        if(false === $result){
            return $this->fail(VoiceLogic::getError());
        }
        return $this->success('OK', $result);
    }
}