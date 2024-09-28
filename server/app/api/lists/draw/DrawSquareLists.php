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

namespace app\api\lists\draw;

use app\api\lists\BaseApiDataLists;
use app\common\enum\draw\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\model\draw\DrawRecordsCollect;
use app\common\model\draw\DrawSquare;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;


class DrawSquareLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 4:07 下午
     */
    public function where()
    {
        $where[] = ['ds.is_show','=',1];
        $where[] = ['ds.verify_status','=',DrawSquareEnum::VERIFY_STATUS_SUCCESS];
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['dr.prompt|dr.prompt_en|ds.prompts_cn|ds.prompts', 'like', '%'.$this->params['keyword'].'%'];
        }
        $categoryId = $this->params['category_id'] ?? '';
        $this->orderBy = 'ds.id desc';
        if ('' != $categoryId) {
            if (0 == $categoryId) {
                $squareIds = DrawRecordsCollect::where(['user_id'=>$this->userId])
                    ->order('id desc')
                    ->column('square_id');
                $where[] = ['ds.id', 'in', $squareIds];
                $squareIds && $this->orderBy = 'FIELD(ds.id,'.implode(',',$squareIds).')';
            } else {
                $where[] = ['ds.category_id', '=',$categoryId];
            }
        }

        return $where;
    }

    /**
     * @notes 绘画广场列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function lists(): array
    {

        $lists = DrawSquare::alias('ds')->withTrashed()
            ->leftjoin('draw_records dr', 'dr.id = ds.records_id')
            ->field('ds.records_id,ds.id,ds.source,ds.operate_id,ds.category_id,ds.prompts,ds.prompts_cn,ds.create_time,
            ds.records_id,ds.avatar,ds.nickname, dr.model as draw_model, dr.scale,dr.image,dr.thumbnail,
            ds.is_slice, ds.image as s_image, ds.thumbnail as s_thumbnail')
            ->append(['category_name','user_info','source_desc','original_prompts'])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->orderRaw($this->orderBy)
            ->select()
            ->toArray();
        $collectLists = DrawRecordsCollect::where(['user_id'=>$this->userId])
                ->column('square_id');
        $showUser = ConfigService::get('draw_award', 'is_show_user');
        foreach ($lists as $key => &$list){
            $lists[$key]['is_collect'] = 0;
            if(in_array($list['id'],$collectLists)){
                $lists[$key]['is_collect'] = 1;
            }

            $lists[$key]['user_info'] = [];
            if(0 == $showUser){
                continue;
            }
            if ($list['source'] == DrawSquareEnum::SOURCE_ADMIN) {
                $image = empty($list['avatar']) ? ConfigService::get('website', 'pc_logo') : $list['avatar'];
                $name = empty($list['nickname']) ? ConfigService::get('website', 'pc_title') : $list['nickname'];
                $lists[$key]['user_info'] = [
                    'image' => FileService::getFileUrl($image),
                    'name' => $name,
                ];
            }else{
                $user = User::where('id',$list['operate_id'])->findOrEmpty()->toArray();
                $lists[$key]['user_info'] = [
                    'image' => $user['avatar'] ?? '',
                    'name' => $user['nickname'] ?? '',
                ];
            }

            if ($list['is_slice']) {
                $list['image'] = FileService::getFileUrl($list['s_image']);
                $list['thumbnail'] = FileService::getFileUrl($list['s_thumbnail']);
            }
            unset($list['s_image'], $list['s_thumbnail']);
        }
        return $lists;
    }

    /**
     * @notes 绘画广场数量
     * @return int
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function count(): int
    {
        return DrawSquare::alias('ds')
            ->leftjoin('draw_records dr', 'dr.id = ds.records_id')
            ->where($this->where())
            ->count();
    }
}