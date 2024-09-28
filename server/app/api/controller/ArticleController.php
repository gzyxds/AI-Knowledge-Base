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

use app\api\lists\article\ArticleCollectLists;
use app\api\lists\article\ArticleLists;
use app\api\logic\ArticleLogic;
use think\response\Json;

/**
 * 文章管理
 */
class ArticleController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'cate', 'detail'];

    /**
     * @notes 文章列表
     * @return Json
     * @author 段誉
     * @date 2022/9/20 15:30
     */
    public function lists(): Json
    {
        return $this->dataLists(new ArticleLists());
    }

    /**
     * @notes 文章分类列表
     * @return Json
     * @author 段誉
     * @date 2022/9/20 15:30
     */
    public function cate(): Json
    {
        return $this->data(ArticleLogic::cate());
    }

    /**
     * @notes 文章详情
     * @return Json
     * @author 段誉
     * @date 2022/9/20 17:09
     */
    public function detail(): Json
    {
        $id = $this->request->get('id/d');
        $result = ArticleLogic::detail($id, $this->userId);
        return $this->data($result);
    }

    /**
     * @notes 收藏列表
     * @return Json
     * @author 段誉
     * @date 2022/9/20 16:31
     */
    public function collect(): Json
    {
        return $this->dataLists(new ArticleCollectLists());
    }

    /**
     * @notes 加入收藏
     * @return Json
     * @author 段誉
     * @date 2022/9/20 17:01
     */
    public function addCollect(): Json
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::addCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }

    /**
     * @notes 取消收藏
     * @return Json
     * @author 段誉
     * @date 2022/9/20 17:01
     */
    public function cancelCollect(): Json
    {
        $articleId = $this->request->post('id/d');
        ArticleLogic::cancelCollect($articleId, $this->userId);
        return $this->success('操作成功');
    }
}