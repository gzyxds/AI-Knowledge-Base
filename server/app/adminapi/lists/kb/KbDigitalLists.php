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

namespace app\adminapi\lists\kb;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\VoiceEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\kb\KbDigital;
use app\common\service\FileService;

/**
 * 数字人列表
 */
class KbDigitalLists extends BaseAdminDataLists implements ListsSearchInterface
{
    /**
     * @notes 列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function lists(): array
    {
        $where = [];
        if (!empty($this->params['user']) && $this->params['user']) {
            $where[] = ['U.sn|U.nickname', 'like', '%'.$this->params['user'].'%'];
        }

        $model = new KbDigital();
        $lists = $model
            ->alias('D')
            ->field('D.*,U.sn,U.nickname')
            ->join('user U', 'U.id = D.user_id')
            ->where($where)
            ->where($this->searchWhere)
            ->order('id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['user'] = [
                'id'       => $item['user_id'],
                'sn'       => $item['sn'],
                'nickname' => $item['nickname'],
            ];

            $item['dubbing'] = match ($item['channel']) {
                VoiceEnum::KDXF   => VoiceEnum::getKdxfPronounceList($item['dubbing']),
                VoiceEnum::OPENAI => VoiceEnum::getOpenAiPronounceList($item['dubbing']),
                default => '-',
            };

            unset($item['sn']);
            unset($item['user_id']);
            unset($item['nickname']);
            unset($item['delete_time']);
        }

        return $lists;
    }

    /**
     * @notes 统计
     * @return int
     * @throws @\think\db\exception\DbException
     * @author fzr
     */
    public function count(): int
    {
        $where = [];
        if (!empty($this->params['user']) && $this->params['user']) {
            $where[] = ['U.sn|U.nickname', 'like', '%'.$this->params['user'].'%'];
        }

        $model = new KbDigital();
        return $model
            ->alias('D')
            ->field('D.*,U.sn,U.nickname')
            ->join('user U', 'U.id = D.user_id')
            ->where($where)
            ->where($this->searchWhere)
            ->count();
    }

    /**
     * @notes 搜索
     * @return array[]
     * @author fzr
     */
    public function setSearch(): array
    {
        return [
            '='      => ['D.is_disable'],
            '%like%' => ['D.name']
        ];
    }
}