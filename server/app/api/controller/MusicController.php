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

use app\api\lists\music\MusicRecordsLists;
use app\api\lists\music\MusicStyleLists;
use app\api\logic\MusicLogic;
use app\api\validate\MusicCollectValidate;
use app\api\validate\MusicValidate;
use think\response\Json;

/**
 * 音乐管理
 */
class MusicController extends BaseApiController
{
    public array $notNeedLogin = ['style','detail', 'config','recommendLits'];

    /**
     * @notes 生成音乐
     * @return Json
     * @author mjf
     * @date 2024/5/29 16:24
     */
    public function generate(): Json
    {
        $params = (new MusicValidate())->post()->goCheck('generate');
        $result = MusicLogic::generate($this->userId, $params);
        if ($result === false) {
            return $this->fail(MusicLogic::getError());
        }
        return $this->data($result);
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
        $result = MusicLogic::detail($params, $this->userId);
        return $this->data($result);
    }

    /**
     * @notes 歌词联想
     * @return Json
     * @author mjf
     * @date 2024/5/29 19:03
     */
    public function imagine(): Json
    {
        $params = $this->request->post();
        $result = MusicLogic::imagine($this->userId, $params);
        if ($result === false) {
            return $this->fail(MusicLogic::getError());
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
        return $this->dataLists(new MusicRecordsLists());
    }

    /**
     * @notes 风格列表
     * @return Json
     * @author mjf
     * @date 2024/5/29 17:41
     */
    public function style(): Json
    {
        return $this->dataLists(new MusicStyleLists());
    }

    /**
     * @notes 音乐配置
     * @return Json
     * @author mjf
     * @date 2024/5/30 17:05
     */
    public function config(): Json
    {
        $result = MusicLogic::config($this->userId);
        return $this->data($result);
    }

    /**
     * @notes 删除
     * @return Json
     * @author mjf
     * @date 2024/5/31 19:01
     */
    public function del()
    {
        $params = $this->request->post();
        MusicLogic::del($params, $this->userId);
        return $this->success();
    }

    /**
     * @notes 收藏
     * @return mixed
     * @author 段誉
     * @date 2023/6/27 11:34
     */
    public function collect()
    {
        $params = (new musicCollectValidate())->post()->goCheck();
        MusicLogic::collect($this->userId, $params);
        return $this->success('操作成功', [], 1, 1);
    }




}