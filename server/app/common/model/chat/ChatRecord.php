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

namespace app\common\model\chat;

use app\common\enum\ChatRecordEnum;
use app\common\model\BaseModel;
use app\common\model\creation\CreationCategory;
use app\common\model\creation\CreationModel;
use app\common\model\skill\Skill;
use app\common\model\skill\SkillCategory;
use app\common\service\FileService;
use think\model\concern\SoftDelete;

/**
 * 对话记录模型
 */
class ChatRecord extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';

    protected $json = ['extra'];

    /**
     * @notes 用户头像
     * @param $value
     * @return string
     * @author ljj
     */
    public function getAvatarAttr($value): string
    {
        return empty($value) ? '' : FileService::getFileUrl($value);
    }

    /**
     * @notes 提问获取器
     * @param $value
     * @return mixed
     * @author ljj
     */
    public function getAskAttr($value): mixed
    {
        $data = json_decode($value,true);
        if ($data) {
            return $data;
        }
        return $value;
    }

    /**
     * @notes 回复获取器
     * @param $value
     * @return mixed
     * @author ljj
     * @date 2023/6/19 7:43 下午
     */
    public function getReplyAttr($value): mixed
    {
        $result = json_decode($value,true);
        if (!$result) {
            $result = $value;
        }
        if (!is_array($result)) {
            $result = [$result];
        }
        return $result;
    }

    /**
     * @notes 创作类别/使用技能
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2023/4/25 10:39 上午
     */
    public function getOtherDescAttr($value, $data): string
    {
        $result = '';
        switch ($data['type']) {
            case 1:
                break;
            case 2:
                $creation_model = (new CreationModel())->where('id',$data['other_id'])->findOrEmpty()->toArray();
                if (empty($creation_model)) {
                    break;
                }
                $creation_category = (new CreationCategory())->where('id',$creation_model['category_id'])->findOrEmpty()->toArray();
                $result = ($creation_category['name'] ?? '').'/'.$creation_model['name'];
                break;
            case 3:
                $skill_model = (new Skill())->where('id',$data['other_id'])->findOrEmpty()->toArray();
                if (empty($skill_model)) {
                    break;
                }
                $skill_category = (new SkillCategory())->where('id',$skill_model['category_id'])->findOrEmpty()->toArray();
                $result = ($skill_category['name'] ?? '').'/'.$skill_model['name'];
                break;
                break;
        }

        return $result;
    }

    /**
     * @notes 审核状态
     * @param $value
     * @param $data
     * @return string
     * @author ljj
     * @date 2023/6/21 10:57 上午
     */
    public function getCensorStatusDescAttr($value, $data): string
    {
        return ChatRecordEnum::getCensorStatusDesc($data['censor_status']);
    }

    /**
     * @notes 审核结果
     * @param $value
     * @param $data
     * @return array
     * @author ljj
     * @date 2023/7/5 3:34 下午
     */
    public function getCensorResultDescAttr($value, $data): array
    {
        $result = [];
        $key = 0;
        if ($data['censor_status'] > ChatRecordEnum::CENSOR_STATUS_COMPLIANCE) {
            $censor_result = json_decode($data['censor_result'],true);
            foreach ($censor_result as $censor_result_val) {
                if (isset($censor_result_val['error_msg'])) {
                    $result[] = $censor_result_val['error_msg'];
                    break;
                }
                if (isset($censor_result_val['data']) && !empty($censor_result_val['data'])) {
                    foreach ($censor_result_val['data'] as $val) {
                        if (isset($val['msg'])) {
                            $result[$key] = $val['msg'];
                        }
                        if (isset($val['hits']) && !empty($val['hits'])) {
                            foreach ($val['hits'] as $hits_val) {
                                if (isset($hits_val['words'])) {
                                    $result[$key] .= '（敏感词：'.implode('、',$hits_val['words']).'）';
                                }
                            }
                        }
                        $key++;
                    }
                }
            }
        }

        return $result;
    }
}