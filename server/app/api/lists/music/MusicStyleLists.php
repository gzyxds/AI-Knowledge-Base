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

namespace app\api\lists\music;

use app\api\lists\BaseApiDataLists;
use app\common\enum\YesNoEnum;
use app\common\model\music\MusicStyle;

/**
 * 音乐风格列表
 */
class MusicStyleLists extends BaseApiDataLists
{
    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/29 17:40
     */
    public function lists(): array
    {
        $model = new MusicStyle();
        return $model
            ->field(['id,name,image'])
            ->where(['status'=>YesNoEnum::YES])
            ->order(['sort'=>'desc','id'=>'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
    }

    /**
     * @notes 获取数量
     * @return int
     * @throws \think\db\exception\DbException
     * @author mjf
     * @date 2024/5/29 17:40
     */
    public function count(): int
    {
        $model = new MusicStyle();
        return $model
            ->where(['status'=>YesNoEnum::YES])
            ->count();
    }
}