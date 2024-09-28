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

namespace app\api\controller\draw;

use app\api\controller\BaseApiController;
use app\api\logic\draw\DrawLogic;
use app\api\validate\draw\DrawCollectValidate;
use app\api\validate\draw\DrawImageValidate;
use app\common\enum\draw\DrawEnum;
use app\common\enum\draw\DrawTaskEnum;
use app\common\service\ConfigService;
use think\response\Json;

class DrawController extends BaseApiController
{
    public array $notNeedLogin = ['detail', 'notifyMj', 'config'];

    /**
     * @notes 生成图片
     * @return Json
     * @author JXDN
     * @date 2024/05/28 10:30
     */
    public function drawing(): Json
    {
        $params = (new DrawImageValidate())->post()->goCheck("draw");
        try {
            $result = (new DrawLogic($this->userId, $params))->draw($this->userId, $params);
            return $this->success('', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @notes 获取绘画配置信息
     * @return Json
     * @author JXDN
     * @date 2024/06/05 14:45
     */
    public function config(): Json
    {
        $drawApi = $this->request->get('draw_api', DrawEnum::API_SD);
        $config  = ConfigService::get('draw_config', $drawApi, DrawEnum::getDrawDefaultConfig($drawApi));
        $result  = [
            "is_member"        => (new DrawLogic($this->userId, ['draw_api' => $drawApi]))->checkVip(),
            "power"            => $config['power'] ?? 0,
            "file_size"        => $config['file_size'] ?? 0,
            "translate_switch" => $config['translate_switch'] ?? 0,
        ];
        if ($drawApi == DrawEnum::API_MJ_GOAPI) {
            $result['mj_style']   = DrawTaskEnum::getMjStyle();
            $result['mj_version'] = DrawTaskEnum::getMjVersion();
            $result['mj_quality'] = DrawTaskEnum::getQuality();
        }
        return $this->success('', $result);
    }

    /**
     * @notes 提示词手动翻译
     * @return Json
     * @author JXDN
     * @date 2024/06/04 15:53
     */
    public function translate(): Json
    {
        $prompt = $this->request->get('prompt');
        return $this->success('', $prompt);
    }

    /**
     * @notes sd模型列表
     * @return Json
     * @author JXDN
     * @date 2024/05/28 10:28
     */
    public function getSdModel(): Json
    {
        $params = $this->request->get();
        try {
            $result = (new DrawLogic($this->userId))->getSdModel($params);
            return $this->success('', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @notes sd模型分类
     * @return Json
     * @author JXDN
     * @date 2024/05/28 15:36
     */
    public function getSdModelCategory(): Json
    {
        try {
            $result = (new DrawLogic($this->userId))->getSdModelCategory();
            return $this->success('', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @notes sd采样算法
     * @return Json
     * @author JXDN
     * @date 2024/05/28 15:36
     */
    public function getSdSamplers(): Json
    {
        try {
            $result = (new DrawLogic($this->userId))->getSdSamplers();
            return $this->success('', $result);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    /**
     * @notes 收藏
     * @return mixed
     * @author 段誉
     * @date 2023/6/27 11:34
     */
    public function collect()
    {
        $params = (new DrawCollectValidate())->post()->goCheck();
        DrawLogic::collect($this->userId, $params);
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * @notes 获取绘画详情
     * @return Json
     * @author cjhao
     * @date 2024/7/25 17:32
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $result = DrawLogic::detail($id,$this->userId);
        if(is_array($result)){
            return $this->success('',$result);
        }
        return $this->fail($result);
    }

    /**
     * @notes mj回调
     * @return Json
     * @author mjf
     * @date 2024/8/16 10:27
     */
    public function notifyMj(): Json
    {
        $post = $this->request->post();
        (new DrawLogic($this->userId))->notifyMj($post);
        return $this->success();
    }

}