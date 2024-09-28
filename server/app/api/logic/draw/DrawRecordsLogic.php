<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------

namespace app\api\logic\draw;

use app\common\enum\draw\DrawTaskEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawRecords;
use app\common\service\FileService;


/**
 * 绘画记录逻辑
 * Class DrawRecordsLogic
 * @package app\api\logic
 */
class DrawRecordsLogic extends BaseLogic
{
    /**
     * @notes 绘图记录
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author JXDN
     * @date 2024/05/30 17:37
     */
    public static function records($userId): array
    {
        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'prompt_desc', 'prompt_other', 'status', 'image',
            'image_base', 'thumbnail', 'model', 'image_url', 'image_id', 'scale', 'able_actions',
            'fail_reason', 'negative_prompt','complex_params', 'version', 'style', 'engine', 'quality', 'create_time'
        ];
        $records = DrawRecords::field($field)
            ->where(['user_id' => $userId])
            ->order('id desc')
            ->select()
            ->toArray();


        foreach ($records as &$item) {
            if (empty($item['thumbnail'])) {
                $item['thumbnail'] = $item['image_url'];
            }
            $item['thumbnail'] = !empty($item['thumbnail']) ? FileService::getFileUrl($item['thumbnail']) : "";
            $item['image'] = !empty($item['image']) ? FileService::getFileUrl($item['image']) : "";
            $item['actions'] = json_decode($item['able_actions'], true);
            $item['able_edit'] = 0;
            if (strtotime($item['create_time']) + 3600 > time() && !empty($item['actions'])) {
                $item['able_edit'] = 1;
            }

            $ableStatus = self::getRecordAbleStatus($item['able_actions'], $item['create_time']);
            $item['able_cut']     = $ableStatus['able_cut'];
            $item['able_actions'] = $ableStatus['able_action'];

            unset($item['create_time']);
        }
        return $records;
    }

    /**
     * @notes 删除绘画记录
     * @param $userId
     * @param $ids
     * @return void
     * @author JXDN
     * @date 2024/05/30 17:37
     */
    public static function delete($userId, $ids): void
    {
        DrawRecords::destroy(function ($query) use ($userId, $ids) {
            $query->where('user_id', $userId)
                ->whereIn('id', $ids);
        });
    }

    /**
     * @notes 绘画任务详情
     * @param $params
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author JXDN
     * @date 2024/05/30 17:38
     */
    public static function getDrawDetail($params, $userId): array
    {
        if (empty($params['records_id'])) {
            return [];
        }
        if (!is_array($params['records_id'])) {
            $params['records_id'] = [$params['records_id']];
        }

        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'status', 'image', 'image_base', 'thumbnail',
            'image_url', 'image_id', 'scale', 'able_actions', 'fail_reason', 'create_time', 'complex_params'
        ];

        $lists = DrawRecords::field($field)->where('id', 'in', $params['records_id'])
            ->where('user_id', $userId)
            ->select()->toArray();

        foreach ($lists as &$item) {
            if (!empty($item['thumbnail'])) {
                $item['thumbnail'] = FileService::getFileUrl($item['thumbnail']);
            } else {
                $item['thumbnail'] = $item['image_url'];
            }

            $ableStatus = self::getRecordAbleStatus($item['able_actions'], $item['create_time']);
            $item['able_cut']     = $ableStatus['able_cut'];
            $item['able_actions'] = $ableStatus['able_action'];
        }

        return $lists;
    }

    /**
     * @notes 绘画记录状态
     * @param $action
     * @param $createTime
     * @return array
     * @author mjf
     * @date 2024/8/21 9:40
     */
    public static function getRecordAbleStatus($action, $createTime): array
    {
        $ableCut = 0;
        $ableAction = json_decode($action, true);

        if (is_array($ableAction) && !in_array(DrawTaskEnum::ACTION_LOW_VARIATION, $ableAction)) {
            $ableCut = 1;
        }

        if (time() > strtotime($createTime) + 3 * 60 * 60) {
            $ableAction = [];
        }

        return ['able_cut' => $ableCut, 'able_action' => $ableAction];
    }

}