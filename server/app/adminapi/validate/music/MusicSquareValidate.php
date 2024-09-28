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

namespace app\adminapi\validate\music;

use app\common\enum\DrawSquareEnum;
use app\common\enum\MusicSquareEnum;
use app\common\enum\VideoSquareEnum;
use app\common\model\draw\DrawSquare;
use app\common\model\music\MusicSquare;
use app\common\model\video\VideoSquare;
use app\common\validate\BaseValidate;

class MusicSquareValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'image_url' => 'require',
        'audio_url' => 'require',
        'title' => 'require',
        'lyric' => 'require',
        'duration' => 'require',
//        'category_id' => 'require',
        'is_show' => 'require|in:0,1',
        'verify_status' => 'require|in:1,2',
        'verify_result' => 'requireIf:verify_status,2',

//        'is_allow_share' => 'require|in:0,1',
//        'is_auto_pass' => 'require|in:0,1',
        'is_show_user' => 'require|in:0,1',
//        'chat_rewards' => 'require|number',
//        'draw_rewards' => 'require|number',
//        'max_share' => 'require|number',
    ];

    protected $message = [
        'id.require' => '参数缺失',
        'id.array' => '参数错误',
        'image_url.require' => '请上传封面',
        'title.require' => '请输入标题',
        'lyric.require' => '请输入歌词',
        'duration.require' => '请输入时长',
        'audio_url.require' => '请上传音频',
//        'category_id.require' => '请选择所属分类',
        'is_show.require' => '请选择是否显示',
        'is_show.in' => '是否显示值错误',
        'verify_status.require' => '请选择审核状态',
        'verify_status.in' => '审核状态值错误',
        'verify_result.requireIf' => '请输入拒绝原因',

        'is_allow_share.require' => '允许用户分享值缺失',
        'is_allow_share.in' => '允许用户分享值错误',
        'is_auto_pass.require' => '自动通过审核值缺失',
        'is_auto_pass.in' => '自动通过审核值错误',
        'is_show_user.require' => '显示用户信息值缺失',
        'is_show_user.in' => '显示用户信息值错误',
        'chat_rewards.require' => '请输入奖励对话次数',
        'chat_rewards.number' => '奖励对话次数值错误',
        'draw_rewards.require' => '请输入奖励绘画次数',
        'draw_rewards.number' => '奖励绘画次数值错误',
        'max_share.require' => '请输入每天最多分享次数',
        'max_share.number' => '每天最多分享次数值错误',
    ];


    public function sceneAdd()
    {
        return $this->only(['image','prompts','is_show']);
    }

    public function sceneEdit()
    {
        return $this->only(['id','is_show'])
            ->append('id');
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneDel()
    {
        return $this->only(['id']);
    }

    public function sceneIsShow()
    {
        return $this->only(['id'])
            ->append('id','checkIsShow');
    }

    public function sceneVerifyStatus()
    {
        return $this->only(['id','verify_status','verify_result'])
            ->append('id','array|checkVerifyStatus');
    }

    public function sceneSetConfig()
    {
        return $this->only(['is_allow_share','is_auto_pass','is_show_user','chat_rewards','draw_rewards','max_share']);
//            ->append('draw_rewards','checkRewards');
    }


    /**
     * @notes 校验编辑
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/8/2 9:46 上午
     */
    public function checkEdit($value,$rule,$data)
    {
        $draw_prompts = DrawSquare::where('id',$value)->findOrEmpty()->toArray();
        if ($draw_prompts['source'] == DrawSquareEnum::SOURCE_ADMIN) {
            if ((!isset($data['prompts'])) || empty($data['prompts'])) {
                return '请输入英文提示词';
            }
            if ((!isset($data['prompts_cn'])) || empty($data['prompts_cn'])) {
                return '请输入中文提示词';
            }
            if ((!isset($data['image'])) || empty($data['image'])) {
                return '请选择图片';
            }
        }
        return true;
    }

    /**
     * @notes 校验显示状态
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/8/2 9:51 上午
     */
    public function checkIsShow($value,$rule,$data)
    {
        $musicSquare = MusicSquare::where('id',$value)->findOrEmpty()->toArray();
        if ($musicSquare['verify_status'] != MusicSquareEnum::VERIFY_STATUS_SUCCESS) {
            return '审核不通过，无法修改显示状态';
        }
        return true;
    }

    /**
     * @notes 校验审核状态
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/8/2 11:31 上午
     */
    public function checkVerifyStatus($value,$rule,$data)
    {
        foreach ($value as $id) {
            $musicSquare = MusicSquare::where('id','=',$id)->findOrEmpty()->toArray();
            if ($musicSquare['verify_status'] != VideoSquareEnum::VERIFY_STATUS_WAIT) {
                return '存在非待审核状态，无法审核';
            }
        }

        return true;
    }


    /**
     * @notes 校验奖励
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author ljj
     * @date 2023/12/13 9:56 上午
     */
    public function checkRewards($value,$rule,$data)
    {
        if ($data['chat_rewards'] == 0 && $data['draw_rewards'] == 0) {
            return '奖励的绘画次数跟对话次数其中一个必须大于0';
        }

        return true;
    }
}