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

namespace app\adminapi\logic\music;

use app\common\logic\BaseLogic;
use app\common\model\music\MusicStyle;
use app\common\service\FileService;
use Exception;

/**
 * 音乐风格类
 */
class MusicStyleLogic extends BaseLogic
{
    /**
     * @notes 详情
     * @param int $id
     * @return array
     * @author mjf
     * @date 2024/5/27 12:03
     */
    public static function detail(int $id): array
    {
        return (new MusicStyle())
            ->withoutField('delete_time,update_time')
            ->findOrEmpty($id)
            ->toArray();
    }

    /**
     * @notes 风格新增
     * @param array $post
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:04
     */
    public static function add(array $post): bool
    {
        try {
            MusicStyle::create([
                'image'       => FileService::setFileUrl($post['image'] ?? ''),
                'name'        => $post['name'],
                'value'       => $post['value'] ?? '',
                'sort'        => $post['sort'] ?? 0,
                'status'      => $post['status'] ?? 0,
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
     * @notes 风格编辑
     * @param array $post
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:04
     */
    public static function edit(array $post): bool
    {
        try {
            MusicStyle::update([
                'image'       => FileService::setFileUrl($post['image'] ?? ''),
                'name'        => $post['name'],
                'value'       => $post['value'] ?? '',
                'sort'        => $post['sort'] ?? 0,
                'status'      => $post['status'] ?? 0,
                'update_time' => time()
            ], ['id' => intval($post['id'])]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除
     * @param array $id
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:13
     */
    public static function del(array $id): bool
    {
        if (!is_array($id)) {
            self::$error = '参数异常';
            return false;
        }

        foreach ($id as $item) {
            $styleModel = new MusicStyle();
            $check = $styleModel->alias('s')
                ->join('music_record r', 'r.style_id = s.id')
                ->field('s.name,r.id')
                ->where('r.style_id', $item)
                ->findOrEmpty();

            if (!$check->isEmpty()) {
                self::$error = $check['name'] . '已有关联生成记录,不可删除';
                return false;
            }
        }

        return MusicStyle::destroy($id);
    }

    /**
     * @notes 状态
     * @param int $id
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:14
     */
    public static function status(int $id): bool
    {
        try {
            $MusicStyle =(new MusicStyle())->where(['id'=>$id])->findOrEmpty();
            if($MusicStyle->isEmpty()){
                return true;
            }
            $MusicStyle->status = $MusicStyle->status ? 0 : 1;
            $MusicStyle->save();
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}