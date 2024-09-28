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

use app\api\lists\video\VideoRecordsLists;
use app\api\logic\VideoLogic;
use app\api\validate\VideoCollectValidate;
use app\api\validate\VideoValidate;
use think\response\Json;

/**
 * 视频管理
 */
class VideoController extends BaseApiController
{
    public array $notNeedLogin = ['config','detail'];


    /**
     * @notes 配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/7/1 14:24
     */
    public function config(): Json
    {
        $result = VideoLogic::config($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 生成视频
     * @return Json
     * @author mjf
     * @date 2024/7/1 15:37
     */
    public function generate(): Json
    {
        $params = (new VideoValidate())->post()->goCheck('generate');
        $result = VideoLogic::generate($this->userId, $params);
        if ($result === false) {
            return $this->fail(VideoLogic::getError());
        }
        return $this->data($result);
    }

    /**
     * @notes 记录列表
     * @return Json
     * @author mjf
     * @date 2024/5/29 19:10
     */
    public function lists(): Json
    {
        return $this->dataLists(new VideoRecordsLists());
    }

    /**
     * @notes 详情
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/30 10:39
     */
    public function detail():Json
    {
        $params = $this->request->get();
        $result = VideoLogic::detail($params, $this->userId);
        return $this->data($result);
    }

    /**
     * @notes 删除
     * @return Json
     * @author mjf
     * @date 2024/5/31 19:01
     */
    public function del(): Json
    {
        $params = $this->request->post();
        VideoLogic::del($params, $this->userId);
        return $this->success();
    }

    /**
     * @notes 翻译
     * @return Json
     * @author mjf
     * @date 2024/7/12 12:10
     */
    public function translate(): Json
    {
        $prompt = $this->request->get('prompt', '');
        $result = VideoLogic::translate($prompt);
        if (false === $result) {
            return $this->fail(VideoLogic::getError());
        }
        return $this->success('', $result);
    }

    /**
     * @notes 收藏
     * @return mixed
     * @author 段誉
     * @date 2023/6/27 11:34
     */
    public function collect()
    {
        $params = (new videoCollectValidate())->post()->goCheck();
        VideoLogic::collect($this->userId, $params);
        return $this->success('操作成功', [], 1, 1);
    }


}