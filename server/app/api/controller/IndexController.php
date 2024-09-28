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

namespace app\api\controller;

use app\api\logic\IndexLogic;
use app\common\logic\DistributionLogic;
use app\common\model\Order;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\User;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\response\Json;

/**
 * 首页
 */
class IndexController extends BaseApiController
{
    public array $notNeedLogin = ['index', 'models', 'visit', 'config', 'policy', 'decorate', 'test','mindMapConfig'];

    /**
     * @notes 计费模型
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author fzr
     */
    public function models(): Json
    {
        $result = IndexLogic::getAiModels($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 全局配置
     * @return Json
     * @author 段誉
     * @date 2022/9/21 19:41
     */
    public function config(): Json
    {
        $result = IndexLogic::getConfigData($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 知识库空间
     * @return Json
     * @throws DbException
     * @author fzr
     */
    public function space(): Json
    {
        $kbId = intval($this->request->get('kb_id', 0));
        if (!$kbId) {
            return $this->fail('请指定知识库');
        }
        $result = IndexLogic::space($this->userId, $kbId);
        return $this->data($result);
    }

    /**
     * @notes 装修数据
     * @return Json
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author fzr
     */
    public function decorate(): Json
    {
        $id = $this->request->get('id/d');
        $result = IndexLogic::getDecorate($id,$this->userId);
        return $this->data($result);
    }

    /**
     * @notes 政策协议
     * @return Json
     * @author 段誉
     * @date 2022/9/20 20:00
     */
    public function policy(): Json
    {
        $type = $this->request->get('type/s', '');
        $result = IndexLogic::getPolicyByType($type);
        return $this->data($result);
    }


    /**
     * @notes 增加访客记录
     * @return Json
     * @author fzr
     */
    public function visit(): Json
    {
        $result = IndexLogic::visit();
        if ($result) {
            return $this->success('');
        }
        return $this->fail(IndexLogic::getError(), [], 0, 0);
    }

    /**
     * @notes 获取思维导图配置
     * @return Json
     * @author cjhao
     * @date 2024/6/18 18:34
     */
    public function mindMapConfig(): Json
    {
        $result = IndexLogic::mindMapConfig($this->userId);
        return $this->success('',$result);
    }
}