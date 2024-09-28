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

namespace app\common\command;

use aip\AipContentCensor;
use app\common\enum\ChatRecordEnum;
use app\common\model\chat\ChatRecord;
use app\common\model\kb\KbRobotRecord;
use app\common\service\ConfigService;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class ContentCensor extends Command
{
    protected function configure(): void
    {
        $this->setName('content_censor')
            ->setDescription('百度内容审核');
    }

    /**
     * @notes 执行百度审核
     * @param Input $input
     * @param Output $output
     * @return bool
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @throws Exception
     */
    protected function execute(Input $input, Output $output): bool
    {
        // 百度内容审核配置
        $is_open = ConfigService::get('content_censor','is_open',0);
        if ($is_open != 1) {
            return true;
        }

        $APP_ID     = ConfigService::get('content_censor','app_id');
        $API_KEY    = ConfigService::get('content_censor','api_key');
        $SECRET_KEY = ConfigService::get('content_censor','secret_key');
        if (!$APP_ID || !$API_KEY || !$SECRET_KEY) {
            throw new Exception('内容审核配置缺失', 10006);
        }

        $client = new AipContentCensor($APP_ID, $API_KEY, $SECRET_KEY);

        $chat_lists = (new ChatRecord())
            ->field('id,ask,reply')
            ->where(['censor_status'=>[ChatRecordEnum::CENSOR_STATUS_WAIT,ChatRecordEnum::CENSOR_STATUS_FAIL]])
            ->where('censor_num','<',5)
            ->order('id','desc')
            ->limit(200)
            ->select()
            ->toArray();

        $know_lists = (new KbRobotRecord())
            ->field('id,robot_id,ask,reply')
            ->where(['censor_status'=>[ChatRecordEnum::CENSOR_STATUS_WAIT,ChatRecordEnum::CENSOR_STATUS_FAIL]])
            ->where('censor_num','<',5)
            ->order(['censor_status'=>'asc','id'=>'desc'])
            ->limit(200)
            ->select()
            ->toArray();

        $lists = array_merge_recursive($chat_lists,$know_lists);

        $chat_update_data = [];
        $know_update_data = [];
        foreach ($lists as $list) {
            // 拼接提问字符串
            $ask = $list['ask'];
            if (is_array($ask)) {
                $ask = json_encode($ask,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            }

            // 拼接回复字符串
            $reply = '';
            if (is_array($list['reply'])) {
                foreach ($list['reply'] as $reply_val) {
                    if (is_array($reply_val)) {
                        $reply .= implode($reply_val);
                    } else {
                        $reply .= $reply_val;
                    }
                }
            } else {
                $reply = $list['ask'];
            }

            // 审核字符串
            $str = $this->splitString($ask.$reply,6000);
            $result_data = [];
            $censor_status = ChatRecordEnum::CENSOR_STATUS_WAIT;
            foreach ($str as $str_val) {
                $result = $client->textCensorUserDefined($str_val);
                $result_data[] = $result;
                if (isset($result['error_code'])) {
                    $censor_status = ChatRecordEnum::CENSOR_STATUS_FAIL;
                    break;
                }
                if (isset($result['conclusionType']) && !in_array($censor_status, [ChatRecordEnum::CENSOR_STATUS_NON_COMPLIANCE, ChatRecordEnum::CENSOR_STATUS_SUSPECTED, ChatRecordEnum::CENSOR_STATUS_FAIL])) {
                    $censor_status = $result['conclusionType'];
                }
            }

            if (!empty($result_data)) {
                if (isset($list['apply_id'])) {
                    $know_update_data[] = [
                        'id'            => $list['id'],
                        'censor_status' => $censor_status,
                        'censor_result' => json_encode($result_data),
                        'censor_num'    => ['inc', 1]
                    ];
                } else {
                    $chat_update_data[] = [
                        'id'            => $list['id'],
                        'censor_status' => $censor_status,
                        'censor_result' => json_encode($result_data),
                        'censor_num'    => ['inc', 1]
                    ];
                }
            }
        }

        if (!empty($chat_update_data)) {
            (new ChatRecord())->saveAll($chat_update_data);
        }

        if (!empty($know_update_data)) {
            (new KbRobotRecord())->saveAll($know_update_data);
        }

        return true;
    }

    /**
     * @notes 以字符数分割字符串
     * @param $str
     * @param int $length
     * @return array
     * @author ljj
     * @date 2023/6/20 10:16 上午
     */
    private function splitString($str, int $length=6100): array
    {
        $data = [];
        $segments = ceil(mb_strlen($str) / $length);
        for ($i = 0; $i < $segments; $i++) {
            $data[] = mb_substr($str, $i * $length, $length);
        }

        return $data;
    }
}