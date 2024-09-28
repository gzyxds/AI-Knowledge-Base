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

namespace app\adminapi\logic\kb;

use app\common\logic\BaseLogic;
use app\common\pgsql\KbEmbedding;
use Exception;

/**
 * 训练数据管理
 */
class KbTeachLogic extends BaseLogic
{
    /**
     * @notes 删除数据
     * @author fzr
     * @param string $uuid
     * @return bool
     */
    public static function del(string $uuid): bool
    {
        try {
            $model = new KbEmbedding();
            $model->where(['uuid'=>$uuid])->update([
                'is_delete' => 1,
                'delete_time' => time()
            ]);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}