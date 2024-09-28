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

namespace app\adminapi\logic\kb;

use app\common\enum\kb\RobotEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\model\kb\KbDigital;
use app\common\model\kb\KbKnow;
use app\common\model\kb\KbRobot;
use app\common\model\kb\KbRobotInstruct;
use app\common\model\kb\KbRobotRecord;
use app\common\service\FileService;
use Exception;
use function Symfony\Component\Translation\t;

/**
 * 机器人逻辑类
 */
class KbRobotLogic extends BaseLogic
{
    /**
     * @notes 机器人详情
     * @param int $id
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $modelKbRobot = new KbRobot();
        $detail = $modelKbRobot
            ->withoutField('user_id,sort,limit_prompt,delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if ($detail) {
            $detail['icons']  = FileService::getFileUrl($detail['icons']);

            $modelKbRobotInstruct = new KbRobotInstruct();
            $detail['menus'] = $modelKbRobotInstruct
                ->where(['robot_id'=>$id])
                ->field(['keyword,content,images'])
                ->order('id asc')
                ->select()
                ->toArray();

            // 知识库
            $detail['knows'] = [];
            if ($detail['kb_ids']) {
                $kbIds = explode(',', $detail['kb_ids']);
                $modelKbKnow = new KbKnow();
                $detail['knows'] = $modelKbKnow->field(['id,name'])->whereIn('id', $kbIds)->select()->toArray();
            }

            // 模型
            $mainModel = (new Models())->where(['id'=>$detail['model_id']])->value('name');
            $subModel = (new ModelsCost())->where(['id'=>$detail['model_sub_id']])->value('name');
            $detail['models'] = $mainModel . '('.$subModel.')';

            // 关联数字人
            $detail['digital'] = [];
            if ($detail['digital_id'] && $detail['is_digital']) {
                $detail['digital'] = (new KbDigital())
                    ->withoutField('user_id,channel,dubbing,delete_time')
                    ->where(['id'=>$detail['digital_id']])
                    ->findOrEmpty()
                    ->toArray();
            }

            unset($detail['kb_ids']);
            foreach ($detail['menus'] as &$item) {
                $item['images'] = $item['images'] ? explode(',', $item['images']) : [];
            }
        }

        return $detail;
    }

    /**
     * @notes 机器人删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            $modelKbRobot = new KbRobot();
            $detail = $modelKbRobot
                ->field(['id'])
                ->where(['id'=>$id])
                ->findOrEmpty()
                ->toArray();

            if (!$detail) {
                throw new Exception('该机器人应用已不存在了!');
            }

            KbRobot::destroy($id);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改机器人状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function changeStatus(int $id): bool
    {
        try {
            $modelKbRobot = new KbRobot();
            $detail = $modelKbRobot
                ->field(['id,is_enable'])
                ->where(['id'=>$id])
                ->findOrEmpty()
                ->toArray();

            if (!$detail) {
                throw new Exception('该机器人应用已不存在了!');
            }

            KbRobot::update([
                'is_enable'   => !$detail['is_enable'],
                'update_time' => time()
            ], ['id'=>$id]);

            if ($detail['is_enable']) {
                self::setError('禁用成功');
            } else {
                self::setError('启用成功');
            }

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改广场的状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function changePublic(int $id): bool
    {
        try {
            $modelKbRobot = new KbRobot();
            $detail = $modelKbRobot
                ->field(['id,is_public'])
                ->where(['id'=>$id])
                ->findOrEmpty()
                ->toArray();

            if (!$detail) {
                throw new Exception('该机器人应用已不存在了!');
            }

            KbRobot::update([
                'is_public'   => !$detail['is_public'],
                'update_time' => time()
            ], ['id'=>$id]);

            if ($detail['is_public']) {
                self::setError('停用成功');
            } else {
                self::setError('公开成功');
            }

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 机器人问答记录
     * @param array $get
     * @return array
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public static function chatRecord(array $get): array
    {
        $pageNo   = intval($get['page_no']   ?? 1);
        $pageSize = intval($get['page_size'] ?? 25);

        $where = [];
        $where[] = ['is_show', '=', 1];
        if (isset($get['user']) && $get['user'] != '') {
            $where[] = ['u.sn|u.nickname|kr.share_identity', 'like', '%'.$get['user'].'%'];
        }

        if (isset($get['keyword']) && $get['keyword'] != '') {
            $where[] = ['kr.ask', 'like', '%'.$get['keyword'].'%'];
        }

        if (isset($get['start_time']) && $get['start_time'] != '') {
            $where[] = ['kr.create_time', '>=', strtotime($get['start_time'])];
        }

        if (isset($get['end_time']) && $get['end_time'] != '') {
            $where[] = ['kr.create_time', '<=', strtotime($get['end_time'])];
        }

        if (isset($get['censor_status']) && $get['censor_status'] != '') {
            $where[] = ['kr.censor_status', '=', $get['censor_status']];
        }

        if (isset($get['robot']) && $get['robot']) {
            $where[] = ['kb.name', 'like', '%'.$get['robot'].'%'];
        }

        $modelKbRobotRecord = new KbRobotRecord();
        $lists = $modelKbRobotRecord
            ->alias('kr')
            ->field([
                'kr.id,kr.ask,kr.reply,kr.tokens,kr.task_time,kr.create_time',
                'kr.model,kr.censor_status,kr.censor_num,kr.censor_result',
                'kr.user_id,u.sn,u.nickname,u.avatar',
                'kr.share_apikey,kr.share_identity,kb.name as robot_name'
            ])
            ->where($where)
            ->leftJoin('user u', 'u.id = kr.user_id')
            ->leftJoin('kb_robot kb', 'kb.id = kr.robot_id')
            ->append(['censor_status_desc', 'censor_result_desc'])
            ->order('kr.id desc')
            ->paginate([
                'page'      => $pageNo,
                'list_rows' => $pageSize,
                'var_page'  => 'page'
            ])->toArray();

        foreach ($lists['data'] as &$item) {
            $item['reply'] = is_array($item['reply']) ? $item['reply'][0]??'' :  $item['reply'];

            if (!$item['share_apikey']) {
                $item['channel'] = '前台';
            } else {
                $key = explode('-', $item['share_apikey'])[0]??'';
                $item['channel'] = RobotEnum::getSecretDesc($key);
            }


            $item['user'] = [
                'id'       => $item['user_id']  ?? 0,
                'sn'       => $item['sn']       ?? '',
                'nickname' => $item['nickname'] ?? $item['share_identity'],
                'avatar'   => FileService::getFileUrl($item['avatar']??''),
            ];

            $item['tokens'] = format_amount_zero($item['tokens']);
            unset($item['user_id']);
            unset($item['sn']);
            unset($item['nickname']);
            unset($item['mobile']);
            unset($item['avatar']);
            unset($item['know_id']);
            unset($item['share_apikey']);
            unset($item['share_identity']);
        }

        return [
            'page_no'   => $pageNo,
            'page_size' => $pageSize,
            'count'     => $lists['total'],
            'lists'     => $lists['data']
        ] ?? [];
    }

    /**
     * @notes 问答记录清空
     * @param array $ids
     * @return bool
     * @author fzr
     */
    public static function chatClean(array $ids): bool
    {
        try {
            if (!$ids) {
                throw new Exception('请选择要删除的数据');
            }

            KbRobotRecord::update([
                'is_show'    => 0,
                'update_time' => time()
            ], [['id', 'in', $ids]]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}