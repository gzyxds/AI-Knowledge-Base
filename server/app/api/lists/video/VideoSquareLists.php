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

namespace app\api\lists\video;

use app\api\lists\BaseApiDataLists;
use app\common\enum\DrawSquareEnum;
use app\common\enum\VideoSquareEnum;
use app\common\model\user\User;
use app\common\model\video\VideoRecordsCollect;
use app\common\model\video\VideoSquare;
use app\common\service\ConfigService;
use app\common\service\FileService;


class VideoSquareLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 4:07 下午
     */
    public function where()
    {
        $where[] = ['vs.is_show','=',1];
        $where[] = ['vs.verify_status','=',VideoSquareEnum::VERIFY_STATUS_SUCCESS];
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['vr.prompt', 'like', '%'.$this->params['keyword'].'%'];
        }
        $categoryId = $this->params['category_id'] ?? '';
        $this->orderBy = 'vs.id desc';
        if ('' != $categoryId) {
            if (0 == $categoryId) {
                $squareIds = VideoRecordsCollect::where(['user_id'=>$this->userId])
                    ->order('id desc')
                    ->column('square_id');
                $where[] = ['vs.id', 'in', $squareIds];
                $squareIds && $this->orderBy = 'FIELD(vs.id,'.implode(',',$squareIds).')';

            } else {
                $where[] = ['vs.category_id', '=',$categoryId];
            }
        }

        return $where;
    }

    /**
     * @notes 广场列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author ljj
     * @date 2023/8/31 4:09 下午
     */
    public function lists(): array
    {
        $lists = VideoSquare::alias('vs')
            ->leftjoin('video_record vr', 'vr.id = vs.records_id')
            ->field(
                'vs.records_id,vs.id,vs.source,vs.operate_id,vs.prompt,
                vs.video_url,vs.image,vs.create_time,vs.category_id'
            )
            ->append(['category_name','user_info','source_desc'])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->orderRaw($this->orderBy)
            ->select()
            ->toArray();
        $showUser = ConfigService::get('video_award', 'is_show_user');

        $collecIds = VideoRecordsCollect::where(['user_id'=>$this->userId])
            ->column('square_id');

        foreach ($lists as $key => $list){
            $lists[$key]['is_collect'] = 0;
            if(in_array($list['id'],$collecIds)){
                $lists[$key]['is_collect'] = 1;
            }

            $lists[$key]['user_info'] = [];
            if(0 == $showUser){
                continue;
            }
            if ($list['source'] == VideoSquareEnum::SOURCE_ADMIN) {
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
        return VideoSquare::alias('vs')
            ->leftjoin('video_record vr', 'vr.id = vs.records_id')
            ->where($this->where())
            ->count();
    }
}