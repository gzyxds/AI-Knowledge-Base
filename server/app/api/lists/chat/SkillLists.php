<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\api\lists\chat;
use app\api\lists\BaseApiDataLists;
use app\common\enum\ChatRecordEnum;
use app\common\model\chat\ChatRecord;
use app\common\model\skill\Skill;

class SkillLists extends BaseApiDataLists
{


    /**
     * @notes 搜索条件
     * @return array
     * @author cjhao
     * @date 2024/5/17 10:29
     */
    public function searchWhere()
    {
        $where = [];
        if(isset($this->params['keyword']) && $this->params['keyword']){
            $where[] = ['name','like','%'.$this->params['keyword'].'%'];
        }
        if(isset($this->params['category_id']) && $this->params['category_id']){
            $where[] = ['category_id','=',$this->params['category_id']];
        }
        return $where;
    }


    public function lists(): array
    {

        $skillLists = Skill::where($this->searchWhere())
                ->field('id,category_id,name,describe,image,content')
                ->order('sort desc,id desc')
                ->limit($this->limitOffset, $this->limitLength)
                ->select()
                ->toArray();

        $skilIds = array_column($skillLists,'id');
        $countLists  = (new ChatRecord())->where(['other_id'=>$skilIds,'type'=> ChatRecordEnum::CHAT_SKILL])
            ->group('other_id')
            ->column('COUNT(DISTINCT user_id) AS user_count','other_id');

        foreach ($skillLists as $key => $skill){
            $skillLists[$key]['use_count'] = $countLists[$skill['id']] ?? 0;
        }
        return $skillLists;

    }



    public function count(): int
    {
        return Skill::where($this->searchWhere())->count();

    }
}