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

namespace app\api\lists\kb;

use app\api\lists\BaseApiDataLists;
//use app\common\enum\ChatEnum;
//use app\common\lists\ListsExtendInterface;
//use app\common\model\chat\ModelsCost;
use app\common\model\kb\KbKnow;
//use app\common\model\kb\KbKnowShared;
use app\common\model\kb\KbKnowTeam;
use app\common\model\user\User;

/**
 * 知识库列表
 */
class KbKnowLists extends BaseApiDataLists
{
    /**
     * @notes 筛选条件
     * @return array
     * @author fzr
     */
    public function where(): array
    {
        $where = [];
        $type = intval($this->request->get('type', 0));
        switch ($type){
            case 1: // 我的知识库
                $where[] = ['user_id', '=', $this->userId];
                break;
            case 2: // 共享给我的
                $shareKbIds = (new KbKnowTeam())->where(['user_id'=>$this->userId])->column('kb_id');

                if ($shareKbIds) {
                    $kbUidS  = (new KbKnow())->whereIn('id', $shareKbIds)->column('user_id', 'id');
                    $userIds = (new User())->whereIn('id', (array_values($kbUidS)?:[0]))->column('id');
                    $kbIds = [];
                    $deleteIds = [];
                    foreach ($kbUidS as $kbId => $uid) {
                        if (!in_array($uid, $userIds)) {
                            $deleteIds[] = $kbId;
                        } else {
                            $kbIds[] = $kbId;
                        }
                    }

                    $shareKbIds = $kbIds;
                    if ($deleteIds) {
                        KbKnowTeam::destroy($deleteIds);
                    }
                }

                $where[] = ['id', 'in', $shareKbIds??[0]];
                break;
            default: // 我的 + 共享的
                $shareKbIds = (new KbKnowTeam())->where(['user_id'=>$this->userId])->column('kb_id');
                if ($shareKbIds) {
                    $kbUidS  = (new KbKnow())->whereIn('id', $shareKbIds)->column('user_id', 'id');
                    $userIds = (new User())->whereIn('id', (array_values($kbUidS)?:[0]))->column('id');
                    $deleteIds = [];
                    $kbIds = [];
                    foreach ($kbUidS as $kbId => $uid) {
                        if (!in_array($uid, $userIds)) {
                            $deleteIds[] = $kbId;
                        } else {
                            $kbIds[] = intval($kbId);
                        }
                    }
                    $shareKbIds = $kbIds;
                    if ($deleteIds) {
                        KbKnowTeam::destroy($deleteIds);
                    }
                }

                $where[] = ['user_id', '=', $this->userId];
                $where[] = ['id', 'in', $shareKbIds??[0]];
                break;
        }

        return $where;
    }

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
        $model = new KbKnow();
        $lists = $model
            ->field(['id,user_id,image,name,intro,is_enable,create_time'])
            ->whereOr($this->where())
            ->order('id desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        $modelKbKnowTeam = new KbKnowTeam();
//        $modelModelsCost = new ModelsCost();
//        $modelsCostList = $modelModelsCost->field(['channel', 'name', 'alias'])->where(['type'=>ChatEnum::MODEL_TYPE_EMB])->select()->toArray();
//        $vectorModels = config('ai.VectorModels');
        foreach ($lists as &$item) {
//            $embeddingModel = '';
//            foreach ($modelsCostList as $m) {
//                if ($m['name'] == $item['embedding_model']) {
//                    $embeddingModel = $m['alias'];
//                    break;
//                }
//            }
//
//            if (!$embeddingModel) {
//                foreach ($vectorModels as $m) {
//                    if ($m['name'] == $item['embedding_model']) {
//                        $embeddingModel = $m['alias'];
//                        break;
//                    }
//                }
//            }

            // $item['embedding_model'] = $embeddingModel;
            $item['team_people'] = $modelKbKnowTeam->where(['kb_id'=>$item['id']])->count() + 1;
            $item['is_super'] = $item['user_id'] == $this->userId ? 1 : 0;
            unset($item['user_id']);
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
        $model = new KbKnow();
        return $model
            ->whereOr($this->where())
            ->count();
    }
}