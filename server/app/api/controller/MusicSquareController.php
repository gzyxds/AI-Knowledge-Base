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

namespace app\api\controller;
use app\api\lists\music\MusicSquareLists;
use app\api\logic\MusicSquareLogic;
use app\common\logic\BaseLogic;

class MusicSquareController extends BaseApiController
{
    public array $notNeedLogin = ['lists','styleLists','recommendLits','detail'];

    /**
     * @notes 绘画广场列表
     * @return \think\response\Json
     * @author ljj
     * @date 2023/8/31 4:10 下午
     */
    public function lists()
    {
        return $this->dataLists(new MusicSquareLists());
    }

    /**
     * @notes 分类列表
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:22 下午
     */
    public function styleLists()
    {
        $result = (new MusicSquareLogic())->styleLists($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 分享至绘画广场
     * @return \think\response\Json
     * @author ljj
     * @date 2023/8/31 4:59 下午
     */
    public function add()
    {
        $params = $this->request->post();
        $result = (new MusicSquareLogic())->add($params,$this->userId,$this->terminal);
        if (true !== $result) {
            return $this->fail($result);
        }
        $tips = BaseLogic::getReturnData() ?: '分享成功';
        return $this->success($tips, [], 1, 1);
    }

    /**
     * @notes 推荐列表
     * @author cjhao
     * @date 2024/8/9 09:10
     */
    public function recommendLits()
    {
        $lists = (new MusicSquareLogic())->recommendLits($this->userId);
        return $this->success('', $lists);
    }


    /**
     * @notes 获取详情
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/8/29 17:38
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = (new MusicSquareLogic())->detail($id,$this->userId);
        return $this->success('', $result);

    }


}