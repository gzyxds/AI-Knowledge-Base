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

namespace app\api\lists\chat;

use app\api\lists\BaseApiDataLists;
use app\common\enum\ChatRecordEnum;
use app\common\model\chat\ChatRecord;
use app\common\model\chat\ChatRecordCollect;
use app\common\service\FileService;

/**
 * 对话记录列表
 */
class ChatRecordLogLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/6/20 10:38 上午
     */
    public function where(): array
    {
        $where[] = ['is_show', '=', 1];
        $where[] = ['user_id', '=', $this->userId];
        $where[] = ['type', '=', $this->params['type']];
        $where[] = ['category_id', '=', $this->params['category_id'] ?? 0];
        $where[] = ['censor_status', 'in', [ChatRecordEnum::CENSOR_STATUS_WAIT,ChatRecordEnum::CENSOR_STATUS_COMPLIANCE,ChatRecordEnum::CENSOR_STATUS_FAIL]];
        if (isset($this->params['other_id']) && $this->params['other_id'] != '') {
            $where[] = ['other_id', '=', $this->params['other_id']];
        }
        if(ChatRecordEnum::CHAT_CREATION == $this->params['type']){
            $where[] = ['creation_type', '=', 1];
        }
        return $where;
    }

    /**
     * @notes 聊天记录列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/6/20 10:37 上午
     */
    public function lists(): array
    {
//        $order = 'asc';
//        $otherId = $this->params['other_id'] ?? '';
//        if (($otherId != '' && ChatRecordEnum::CHAT_CREATION == $this->params['type']) || $this->params['type'] == ChatRecordEnum::CHAT_MINDMAP) {
//            $order = 'desc';
//        }
//        if ($otherId != '' && ChatRecordEnum::CHAT_CREATION == $this->params['type']) {
//            $order = 'desc';
//        }
        $order = 'desc';
        $lists = (new ChatRecord())->field('id,ask,reply,model,type,extra,files_plugin,correlation,create_time')
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id'=>$order])
            ->select()
            ->toArray();

        $id = array_column($lists,'id');
        $collectId = (new ChatRecordCollect())->where(['records_id'=>$id])->column('id','records_id');
        $data = [];
        //如果是首页对话记录，需要将数组反转
        if($this->params['type'] == ChatRecordEnum::CHAT_QUESTION || $this->params['type'] == ChatRecordEnum::CHAT_SKILL){
            $lists = array_reverse($lists);
        }
        foreach ($lists as $list) {
            if ($list['type'] == ChatRecordEnum::CHAT_CREATION || ChatRecordEnum::CHAT_MINDMAP == $list['type']) {
                $data[] = [
                    'id'          => $list['id'],
                    'ask'         => $list['ask'],
                    'reply'       => $list['reply'],
                    'model'       => $list['model'],
                    'create_time' => $list['create_time'],
                    'extra'       => $list['extra'],
                    'is_collect'  => $collectId[$list['id']] ?? 0,
                ];
            } else {
                $files_plugin = json_decode($list['files_plugin']??'[]', true);
                $correlation  = json_decode($list['correlation']??'[]', true);

                if ($files_plugin) {
                    foreach ($files_plugin as &$item) {
                        $item['url'] = FileService::getFileUrl($item['url']);
                    }
                }

                $data[] = [
                    'id'           => $list['id'],
                    'type'         => 1,//用户
                    'content'      => (string)$list['ask'],
                    'files_plugin' => $files_plugin,
                    'is_collect'   => $collectId[$list['id']] ?? 0,
                    'create_time'  => $list['create_time']
                ];
                $data[] = [
                    'id'          => $list['id'],
                    'type'        => 2,//AI
                    'model'       => $list['model'],//AI
                    'content'     => $list['reply'],
                    'correlation' => $correlation,
                    'is_collect'  => $collectId[$list['id']] ?? 0,
                    'create_time' => $list['create_time']
                ];
            }
        }

        return $data;
    }

    /**
     * @notes 聊天记录数量
     * @return int
     * @throws #\think\db\exception\DbException
     * @author ljj
     * @date 2023/6/20 10:37 上午
     */
    public function count(): int
    {
        return (new ChatRecord())->where($this->where())->count();
    }
}