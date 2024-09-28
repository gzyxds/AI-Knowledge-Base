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

namespace app\adminapi\logic\creation;

use app\common\logic\BaseLogic;
use app\common\model\creation\CreationCategory;
use app\common\model\creation\CreationModel;
use app\common\service\FileService;
use Exception;

/**
 * 创作类别逻辑类
 */
class CreationCategoryLogic extends BaseLogic
{
    /**
     * @notes 创建类别详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        return (new CreationCategory())
            ->withoutField('delete_time,update_time')
            ->findOrEmpty($id)
            ->toArray();
    }

    /**
     * @notes 创作类别新增
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        try {
            CreationCategory::create([
                'image'       => FileService::setFileUrl($post['image']??''),
                'name'        => $post['name'],
                'sort'        => $post['sort']??0,
                'status'      => $post['status']??0,
                'create_time' => time(),
                'update_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作类别编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        try {
            CreationCategory::update([
                'image'       => FileService::setFileUrl($post['image']??''),
                'name'        => $post['name'],
                'sort'        => $post['sort']??0,
                'status'      => $post['status']??0,
                'update_time' => time()
            ], ['id'=>intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作类别删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            $creationSample = (new CreationModel())->where(['category_id'=>$id])->findOrEmpty();
            if(!$creationSample->isEmpty()){
                throw new Exception('该分类已经被使用,不能删除');
            }

            CreationCategory::destroy($id);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 创作类别状态
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function status(int $id): bool
    {
        try {
            $creationCategory =(new CreationCategory())->where(['id'=>$id])->findOrEmpty();
            if($creationCategory->isEmpty()){
                return true;
            }
            $creationCategory->status = $creationCategory->status ? 0 : 1;
            $creationCategory->save();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}