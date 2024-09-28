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

namespace app\adminapi\lists\draw;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\enum\DrawSquareEnum;
use app\common\lists\ListsExcelInterface;
use app\common\model\draw\DrawSquare;
use app\common\model\user\User;
use app\common\service\ConfigService;


class DrawSquareLists extends BaseAdminDataLists implements ListsExcelInterface
{
    /**
     * @notes 搜索条件
     * @return array
     * @author ljj
     * @date 2023/8/31 11:48 上午
     */
    public function where()
    {
        $where = [];
        if(isset($this->params['user_info']) && $this->params['user_info'] != ''){
            $userIds = User::where('sn|nickname','like','%'.$this->params['user_info'].'%')->column('id');
            empty($userIds) && $userIds = [];
            $where[] = ['ds.operate_id','in',implode(',',$userIds)];
        }
        if (isset($this->params['keyword']) && $this->params['keyword'] != '') {
            $where[] = ['dr.prompt|dr.prompt_en|ds.prompts_cn|ds.prompts', 'like', '%'.$this->params['keyword'].'%'];
        }
        if (isset($this->params['category_id']) && $this->params['category_id'] != '') {
            $where[] = ['ds.category_id', '=', $this->params['category_id']];
        }
        if (isset($this->params['verify_status']) && $this->params['verify_status'] != '') {
            $where[] = ['ds.verify_status', '=', $this->params['verify_status']];
        }
        if (isset($this->params['is_show']) && $this->params['is_show'] != '') {
            $where[] = ['ds.is_show', '=', $this->params['is_show']];
        }
        if (isset($this->params['start_time']) && $this->params['start_time'] != '') {
            $where[] = ['ds.create_time','>=',strtotime($this->params['start_time'])];
        }
        if (isset($this->params['end_time']) && $this->params['end_time'] != '') {
            $where[] = ['ds.create_time','<=',strtotime($this->params['end_time'])];
        }
        if (isset($this->params['source']) && $this->params['source'] != '') {
            $where[] = ['ds.source', '=', $this->params['source']];
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
     * @date 2023/8/31 12:01 下午
     */
    public function lists(): array
    {
        $lists = DrawSquare::alias('ds')
            ->leftjoin('draw_records dr', 'dr.id = ds.records_id')
            ->field('ds.id,ds.source,ds.verify_status,ds.is_show,ds.operate_id,ds.category_id,ds.prompts,ds.prompts_cn,ds.image,ds.create_time,ds.thumbnail,ds.records_id,ds.avatar,ds.nickname, dr.model as draw_model, dr.scale')
            ->append(['category_name','verify_status_desc','user_info','source_desc','original_prompts'])
            ->where($this->where())
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['ds.id' => 'desc'])
            ->select()
            ->toArray();
        return $lists;
    }

    /**
     * @notes 绘画广场数量
     * @return int
     * @author ljj
     * @date 2023/8/31 12:01 下午
     */
    public function count(): int
    {
        return DrawSquare::alias('ds')
            ->leftjoin('draw_records dr', 'dr.id = ds.records_id')
            ->where($this->where())
            ->count();
    }

    /**
     * @notes 导出文件名
     * @return string
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setFileName(): string
    {
        return '绘画广场列表';
    }

    /**
     * @notes 导出字段
     * @return string[]
     * @author ljj
     * @date 2023/8/24 2:49 下午
     */
    public function setExcelFields(): array
    {
        return [
            'prompts_cn' => '中文提示词',
            'prompts' => '英文提示词',
            'category_name' => '所属分类',
            'source_desc' => '添加来源',
            'verify_status_desc' => '审核状态',
            'create_time' => '创建时间',
        ];
    }
}