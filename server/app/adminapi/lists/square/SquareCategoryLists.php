<?php
namespace app\adminapi\lists\square;
use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsExtendInterface;
use app\common\lists\ListsSearchInterface;
use app\common\model\draw\DrawSquare;
use app\common\model\music\MusicSquare;
use app\common\model\square\SquareCategory;
use app\common\model\video\VideoSquare;

class SquareCategoryLists extends BaseAdminDataLists implements ListsSearchInterface,ListsExtendInterface
{
    /**
     * @notes  设置搜索条件
     * @return array
     * @author heshihu
     * @date 2022/2/8 18:39
     */
    public function setSearch(): array
    {
        return [
            '%like%' => ['name'],
            '=' => ['status', 'type']
        ]??[];
    }


    public function lists(): array
    {
        $lists = (new SquareCategory())
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->withoutField('delete_time,update_time')
            ->order('id desc')
            ->select()
            ->toArray();

        $type = $this->params['type'] ?? '';
        $drawLists = [];
        $musicLists = [];
        $videoLits = [];
        $ids = array_column($lists,'id');
        switch ($type){
            case '':
                $drawLists  = DrawSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                $musicLists = MusicSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                $videoLits  = VideoSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                break;
            case 1:
                $drawLists = DrawSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                break;
            case 2:
                $musicLists = MusicSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                break;
            case 3:
                $videoLits = VideoSquare::where(['category_id'=>$ids])
                    ->group('category_id')
                    ->column("count('id') as count",'category_id');
                break;
        }
        foreach ($lists as $key => $list){
            $relevanceNum = '';
            switch ($list['type']){
                case 1:
                    $relevanceNum = $drawLists[$list['id']] ?? 0;
                    break;
                case 2:
                    $relevanceNum = $musicLists[$list['id']] ?? 0;
                    break;
                case 3:
                    $relevanceNum = $videoLits[$list['id']] ?? 0;
                    break;
            }
            $lists[$key]['relevance_num'] = $relevanceNum;
        }
        return $lists;
    }

    public function count(): int
    {
        return (new SquareCategory())
            ->where($this->searchWhere)
            ->count();
    }


    public function extend()
    {
        $where = [];
        if(isset($this->params['name']) && $this->params['name']){
            $where[] = ['name','like','%'.$this->params['name'].'%'];
        }
        if(isset($this->params['status']) && '' != $this->params['status']){
            $where[] = ['status','=',$this->params['status']];
        }
        return [
            'all_count'     => (new SquareCategory())->where($where)->count(),
            'draw_count'    => (new SquareCategory())->where($where)->where(['type'=>1])->count(),
            'music_count'   => (new SquareCategory())->where($where)->where(['type'=>2])->count(),
            'video_count'   => (new SquareCategory())->where($where)->where(['type'=>3])->count(),
        ];
    }
}