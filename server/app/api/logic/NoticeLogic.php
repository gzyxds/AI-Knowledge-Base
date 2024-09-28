<?php

namespace app\api\logic;

use app\common\enum\notice\NoticeEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawRecords;
use app\common\model\kb\KbRobot;
use app\common\model\music\MusicRecord;
use app\common\model\music\MusicSquare;
use app\common\model\notice\NoticeRecord;
use app\common\model\user\User;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoSquare;
use app\common\service\FileService;

class NoticeLogic extends BaseLogic
{

    /**
     * @notes 通知列表
     * @param array $get
     * @param int $userId
     * @return array
     * @author fzr
     */
    public static function lists(array $get, int $userId): array
    {
        $type     = $get['type'] ?? '';
        $pageNo   = intval($get['page_no']   ?? 1);
        $pageSize = intval($get['page_size'] ?? 25);
        $where[] = ['notice_type','in',[3,4,5,6,7]];
        if(1 == $type){
            $where = [];
            $where[] = ['notice_type','=',3];
        }
        if(2 == $type){
            $where = [];
            $where[] = ['notice_type','in',[4,5,6,7]];
        }
        $lists = (new NoticeRecord())
            ->field('id,user_id,robot_id,title,content,read,notice_type,send_uid,extra,create_time')
            ->where(['user_id'=>$userId])
            ->where(['scene_id'=>[NoticeEnum::CHAT_FEEDBACK,NoticeEnum::SQUARE_NOTICE]])
            ->where(['recipient'=>1])
            ->where(['send_type'=>1])
            ->where($where)
            ->order('read asc, id desc')
            ->paginate([
                'page'      => $pageNo,
                'list_rows' => $pageSize,
                'var_page'  => 'page'
            ])
            ->toArray();
        $robotIds = array_column($lists['data'],'robot_id');
        $robotLists = KbRobot::where(['id'=>$robotIds])->column('name','id');
        $userIds = array_column($lists['data'],'send_uid');
        $userLists = User::where(['id'=>$userIds])->column('avatar,nickname','id');
        foreach ($lists['data'] as $key =>  &$item) {
            $item['robot'] = $robotLists[$item['robot_id']] ?? '-';
            $item['avatar'] = $userLists[$item['send_uid']]['avatar'] ?? '';
            $item['nickname'] = $userLists[$item['send_uid']]['nickname'] ?? '';
            if($item['avatar']){
                $item['avatar'] = FileService::getFileUrl($item['avatar']);
            }
            $extra = json_decode($item['extra'],true);
            $recordsName = '';
            $verifyStatus = '';
            $verifyResult = '';
            $balance = '';
            $model = '';
            $recordsIds = '';
            if($extra){
                $recordsIds = $extra['records_id'] ?? $extra['robot_id'];
                $verifyResult = $extra['verify_result'] ?? '';
                $verifyStatus = $extra['verify_status'] ?? '';
                $balance = $extra['balance'] ?? 0;
            }
            if($recordsIds){
                switch ($item['notice_type']){
                    case 4:
                        $recordsName = DrawRecords::where(['id'=>$recordsIds])->value('prompt');
                        $model = $extra['model'] ?? 'sd';
                        break;
                    case 5:
                        $recordsName = MusicRecord::where(['id'=>$recordsIds])->value('title');
                        break;
                    case 6:
                        $recordsName = VideoRecord::where(['id'=>$recordsIds])->value('prompt');
                        break;
                    case 7:
                        $recordsName = KbRobot::where(['id'=>$recordsIds])->value('name');
                        break;
                }
            }
            $item['records_name'] = $recordsName;
            $item['verify_result'] = $verifyResult;
            $item['verify_status'] = $verifyStatus ? (int)$verifyStatus : '';
            $item['balance'] = $balance;
            $item['model'] = $model;
            unset($lists['data'][$key]['extra']);
        }

        $allUnread = (new NoticeRecord())
            ->where(['read'=>0])
            ->where(['scene_id'=>[NoticeEnum::CHAT_FEEDBACK,NoticeEnum::SQUARE_NOTICE]])
            ->where(['user_id'=>$userId])
            ->where(['recipient'=>1])
            ->where(['send_type'=>1])
            ->count();

        $systemUnread = (new NoticeRecord())
            ->where(['read'=>0])
            ->where(['scene_id'=>[NoticeEnum::CHAT_FEEDBACK]])
            ->where(['user_id'=>$userId])
            ->where(['recipient'=>1])
            ->where(['send_type'=>1])
            ->count();

        $auditUnread = (new NoticeRecord())
            ->where(['read'=>0])
            ->where(['scene_id'=>[NoticeEnum::SQUARE_NOTICE]])
            ->where(['user_id'=>$userId])
            ->where(['recipient'=>1])
            ->where(['send_type'=>1])
            ->count();

        return [
            'page_no'           => $pageNo,
            'page_size'         => $pageSize,
            'system_unread'     => $systemUnread,
            'audit_unread'      => $auditUnread,
            'all_unread'        => $allUnread,
            'count'             => $lists['total'],
            'lists'             => $lists['data']
        ] ?? [];
    }

    /**
     * @notes 标记已读
     * @param int $id
     * @param int $userId
     * @return void
     * @author fzr
     */
    public static function read(int $id, int $userId): void
    {
        NoticeRecord::update([
            'read' => 1,
            'update_time' => time()
        ], [
            'id'          => $id,
            'user_id'     => $userId,
            'read'        => 0,
//            'scene_id'    => NoticeEnum::CHAT_FEEDBACK,
            'recipient'   => 1,
            'send_type'   => 1,
//            'notice_type' => 3
        ]);
    }

    /**
     * @notes 全部标记已读
     * @param int $userId
     * @return void
     * @author fzr
     */
    public static function allRead(int $userId): void
    {
        NoticeRecord::update([
            'read' => 1,
            'update_time' => time()
        ], [
            'user_id'     => $userId,
            'read'        => 0,
//            'scene_id'    => NoticeEnum::CHAT_FEEDBACK,
            'recipient'   => 1,
            'send_type'   => 1,
//            'notice_type' => 3
        ]);
    }

    /**
     * @notes 清空所有已读
     * @param int $userId
     * @return void
     * @author fzr
     */
    public static function clear(int $userId): void
    {
        NoticeRecord::update([
            'delete_time' => time()
        ], [
            'user_id'     => $userId,
            'read'        => 1,
            'scene_id'    => NoticeEnum::CHAT_FEEDBACK,
            'recipient'   => 1,
            'send_type'   => 1,
            'notice_type' => 3
        ]);
    }
}