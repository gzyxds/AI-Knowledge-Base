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

namespace app\api\controller\draw;

use app\api\controller\BaseApiController;
use app\api\lists\draw\DrawRecordsLists;
use app\api\logic\draw\DrawRecordsLogic;
use app\api\validate\draw\DrawCollectValidate;

/**
 * 绘画记录
 * Class DrawRecordsController
 * @package app\api\controller
 */
class DrawRecordsController extends BaseApiController
{

    /**
     * @notes 绘图记录
     * @return \think\response\Json
     * @author JXDN
     * @date 2024/05/29 17:28
     */
    public function records(): \think\response\Json
    {
        return $this->dataLists(new DrawRecordsLists());
    }

    /**
     * @notes 删除绘画记录
     * @return \think\response\Json
     * @author JXDN
     * @date 2024/05/29 17:28
     */
    public function delete(): \think\response\Json
    {
        $ids = $this->request->post('ids');
        DrawRecordsLogic::delete($this->userId, $ids);
        return $this->success();
    }

    /**
     * @notes 绘画记录详情
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author JXDN
     * @date 2024/05/29 17:48
     */
    public function detail(): \think\response\Json
    {
        $params = $this->request->post();
        $result = DrawRecordsLogic::getDrawDetail($params, $this->userId);
        return $this->data($result);
    }

}