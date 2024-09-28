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
namespace app\api\controller\chat;
use app\api\controller\BaseApiController;
use app\api\lists\chat\SkillLists;
use app\api\logic\chat\SkillLogic;

/**
 * 技能控制器类
 * Class SkillController
 * @package app\api\chat\controller
 */
class SkillController extends BaseApiController
{

    public array $notNeedLogin = ['lists','detail','categoryLists','mobileLists'];


    /**
     * @notes 获取角色列表
     * @author cjhao
     * @date 2023/4/17 18:56
     */
    public function lists()
    {
        $lists = (new SkillLogic())->lists($this->request->get());
        return $this->success('',$lists);
    }

    /**
     * @notes 角色详情
     * @return \think\response\Json
     * @author ljj
     * @date 2023/4/25 5:14 下午
     */
    public function detail()
    {
        $params = $this->request->get();
        $result = (new SkillLogic())->detail($params['id'] ?? 0);
        return $this->success('',$result);
    }

    /**
     * @notes 分类列表
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/5/17 10:35
     */
    public function categoryLists()
    {
        $result = (new SkillLogic())->categoryLists();
        return $this->success('',$result);
    }

    /**
     * @notes 移动端技能列表
     * @return \think\response\Json
     * @author cjhao
     * @date 2024/5/17 10:35
     */
    public function mobileLists()
    {
        return $this->dataLists((new SkillLists()));
    }

}