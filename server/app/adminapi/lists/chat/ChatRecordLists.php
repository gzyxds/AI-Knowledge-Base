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

namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\ChatRecordEnum;
use app\common\enum\CreationEnum;
use app\common\model\chat\ChatRecord;
use app\common\service\FileService;

/**
 * 对话记录列表
 */
class ChatRecordLists extends BaseAdminDataLists
{
    /**
     * @notes 条件
     * @return array
     * @author fzr
     */
    public function where(): array
    {
        $where = [];
        $type = intval($this->params['type'] ?? 1);
        $where[] = ['cr.type','=',$type];
        if (isset($this->params['user_info']) && $this->params['user_info'] != '') {
            $where[] = ['u.sn|u.nickname','like','%'.$this->params['user_info'].'%'];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] !== '') {
            $where[] = ['cr.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['cr.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['cr.ask','like','%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['censor_status']) && $this->params['censor_status'] != '') {
            $where[] = ['cr.censor_status','=',$this->params['censor_status']];
        }
        return $where;
    }

    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        $lists = (new ChatRecord())
            ->alias('cr')
            ->join('user u', 'u.id = cr.user_id')
            ->field([
                'cr.id,cr.type,cr.creation_type,cr.other_id,u.avatar,u.nickname,cr.create_time,cr.ask,cr.reply',
                'cr.price,cr.tokens,cr.model,cr.censor_status,cr.ip,cr.censor_result,files_plugin'
            ])
            ->append(['other_desc','censor_status_desc','censor_result_desc'])
            ->where($this->where())
            ->order('cr.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['price'] = format_amount_zero($item['price']);
            $item['tokens'] = format_amount_zero($item['tokens']);
            $item['text_type'] = '-';
            $item['files_plugin'] = json_decode($item['files_plugin']??'[]', true);
            foreach ($item['files_plugin'] as &$f) {
                $f['url'] = FileService::getFileUrl($f['url']);
            }
            if(ChatRecordEnum::CHAT_CREATION == $item['type']){
                $item['text_type'] = CreationEnum::getCreationTypeDesc($item['creation_type']);
            }
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        return (new ChatRecord())
            ->alias('cr')
            ->join('user u', 'u.id = cr.user_id')
            ->where($this->where())
            ->count();
    }
}