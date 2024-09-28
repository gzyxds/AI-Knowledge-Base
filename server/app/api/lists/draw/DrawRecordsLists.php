<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\api\lists\draw;

use app\api\lists\BaseApiDataLists;
use app\api\logic\draw\DrawRecordsLogic;
use app\api\logic\draw\OldDrawLogic;
use app\common\enum\draw\DrawRecordEnum;
use app\common\enum\draw\DrawEnum;
use app\common\model\draw\DrawRecords;
use app\common\model\draw\DrawRecordsCollect;
use app\common\model\draw\DrawSquare;
use app\common\service\FileService;
use think\facade\Config;

/**
 * 绘图记录
 * Class DrawRecordsLists
 * @package app\api\lists
 */
class DrawRecordsLists extends BaseApiDataLists
{

    public function queryWhere(): array
    {
        $where = [];
        if (isset($this->params['status']) && $this->params['status'] > 0) {
            $where[] = ['status', '=', $this->params['status']];
        }
        if (isset($this->params['model']) && $this->params['model'] != '') {
            $where[] = ['model', '=', $this->params['model']];
        }
        return $where;
    }

    /**
     * @notes 列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/20 12:04
     */
    public function lists(): array
    {
        $field = [
            'id', 'task_id', 'prompt', 'prompt_en', 'prompt_desc', 'prompt_other', 'status', 'image',
            'image_base', 'thumbnail', 'model', 'image_url', 'image_id', 'scale', 'able_actions',
            'fail_reason', 'negative_prompt', 'version', 'style', 'engine', 'quality', 'censor_status',
            'create_time', 'type', 'complex_params', 'loras', 'scale'
        ];
        $records = DrawRecords::field($field)
            ->where($this->queryWhere())
            ->where(['user_id' => $this->userId])
            ->limit($this->limitOffset, $this->limitLength)
            ->withAttr([
                'thumbnail' => function ($value) {
                    return FileService::getFileUrl($value);
                },
                'image_url' => function ($value) {
                    return FileService::getFileUrl($value);
                }])
            ->order('id desc')
            ->select()
            ->toArray();

        $shareIds = DrawSquare::where(['operate_id'=>$this->userId])
            ->column('records_id');

        // 违规时默认显示图片
        $defaultCensorImg = FileService::getFileUrl(Config::get('project.default_image.draw_censor_fail'));

        foreach ($records as &$item) {
            $item['loras'] = json_decode($item['loras']);
            $item['is_share'] = 0;
            if(in_array($item['id'],$shareIds)){
                $item['is_share'] = 1;
            }

            // 图片审核不合规的记录
            if ($item['censor_status'] == DrawRecordEnum::CENSOR_STATUS_NON_COMPLIANCE) {
                $item['image'] = $defaultCensorImg;
                $item['image_url'] = $defaultCensorImg;
                $item['thumbnail'] = $defaultCensorImg;
            }

            $ableStatus = DrawRecordsLogic::getRecordAbleStatus($item['able_actions'], $item['create_time']);
            $item['able_cut']     = $ableStatus['able_cut'];
            $item['able_actions'] = $ableStatus['able_action'];
        }

        return $records;
    }

    /**
     * @notes 数量
     * @return int
     * @throws \think\db\exception\DbException
     * @author JXDN
     * @date 2024/05/29 17:27
     */
    public function count(): int
    {
        return DrawRecords::where(['user_id' => $this->userId])
            ->where($this->queryWhere())
            ->count();
    }


}