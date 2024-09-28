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
use app\common\enum\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\model\music\MusicRecordsCollect;
use app\common\model\music\MusicSquare;
use app\common\model\user\User;
use app\common\service\ConfigService;
use app\common\service\FileService;


class MusicSquareLists extends BaseApiDataLists
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 4:07 下午
     */
    public function where()
    {
        $where[] = ['ms.is_show','=',1];
        $where[] = ['ms.verify_status','=',MusicSquareEnum::VERIFY_STATUS_SUCCESS];
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['mr.title|mr.lyric', 'like', '%'.$this->params['keyword'].'%'];
        }
        $categoryId = $this->params['category_id'] ?? '';
        $this->orderBy = 'ms.id desc';
        if ('' != $categoryId) {
            if (0 == $categoryId) {
                $squareIds = MusicRecordsCollect::where(['user_id'=>$this->userId])
                    ->order('id desc')
                    ->column('square_id');
                $where[] = ['ms.id', 'in', $squareIds];
                $squareIds && $this->orderBy = 'FIELD(ms.id,'.implode(',',$squareIds).')';

            } else {
                $where[] = ['ms.category_id', '=',$categoryId];
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
        $lists = MusicSquare::alias('ms')
            ->leftjoin('music_record mr', 'mr.id = ms.records_id')
            ->field(
                'ms.records_id,ms.id,ms.source,ms.operate_id,ms.title,mr.lyric,
                ms.image_url,ms.audio_url,ms.create_time,ms.duration,ms.category_id'
            )
            ->append(['category_name','source_desc'])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->orderRaw($this->orderBy)
            ->select()
            ->toArray();
        $showUser = ConfigService::get('music_award', 'is_show_user');
        $collectLists = MusicRecordsCollect::where(['user_id'=>$this->userId])
            ->column('square_id');
        foreach ($lists as $key => $list){
            //收藏
            $lists[$key]['is_collect'] = 0;
            if(in_array($list['id'],$collectLists)){
                $lists[$key]['is_collect'] = 1;
            }
            //用户信息
            $lists[$key]['user_info'] = [];
            if(0 == $showUser){
                continue;
            }
            if ($list['source'] == MusicSquareEnum::SOURCE_ADMIN) {
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
        return MusicSquare::alias('ms')
            ->leftjoin('music_record mr', 'mr.id = ms.records_id')
            ->where($this->where())
            ->count();
    }
}