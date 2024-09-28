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

namespace app\common\enum\draw;

/**
 * 绘画任务
 * Class DrawEnum
 * @package app\common\enum
 */
class DrawTaskEnum
{
    /**
     * 绘画动作
     * ACTION_GENERATE = 生图
     * ACTION_UPSCALE = 放大
     * ACTION_VARIATION = 变换
     */
    const ACTION_GENERATE = "generate";
    const ACTION_UPSCALE = "upscale";
    const ACTION_VARIATION = "variation";

    const ACTION_UPSCALE_2X = 'upscale_2x';
    const ACTION_UPSCALE_4X = 'upscale_4x';
    const ACTION_UPSCALE_SUBTLE = 'upscale_subtle';
    const ACTION_UPSCALE_CREATIVE = 'upscale_creative';
    const ACTION_REDO_UPSCALE_SUBTLE = 'redo_upscale_subtle';
    const ACTION_REDO_UPSCALE_CREATIVE = 'redo_upscale_creative';

    const ACTION_LOW_VARIATION = 'low_variation';
    const ACTION_HIGH_VARIATION = 'high_variation';
    const ACTION_ZOOM_OUT_2X = 'outpaint_2x';
    const ACTION_ZOOM_OUT_1_5X = 'outpaint_1.5x';

    const ACTION_PAN_LEFT = 'pan_left';
    const ACTION_PAN_RIGHT = 'pan_right';
    const ACTION_PAN_UP = 'pan_up';
    const ACTION_PAN_DOWN = 'pan_down';

    const ACTION_REROLL = 'reroll';

    const ACTION_INPAINT = 'inpaint';


    // 模型
    const MODEL_DEFAULT = 'mj';
    const MODEL_NIJI = 'niji';

    // 生成模式
    const MODE_RELAX = 'relax';
    const MODE_FAST = 'fast';
    const MODE_TURBO = 'turbo';

    /**
     * @notes mj版本
     * @return array[]
     * @author mjf
     * @date 2024/8/14 15:23
     */
    public static function getMjVersion(): array
    {
        $mj = ['6.1', '6.0', '5.2', '5.1', '5', '4',];

        $niji = ['6', '5'];

        return [
            'mj'   => $mj,
            'niji' => $niji,
        ];
    }

    /**
     * @notes mj风格
     * @return string[]
     * @author mjf
     * @date 2024/8/14 15:20
     */
    public static function getMjStyle(): array
    {
        return [
            'default'    => "动漫",
            'cute'       => "可爱",
            'expressive' => "丰富",
        ];
    }

    /**
     * @notes mj质量参数
     * @return string[]
     * @author mjf
     * @date 2024/8/21 16:18
     */
    public static function getQuality(): array
    {
        return [
            '1'   => "1",
            '.5'  => "0.5",
            '.25' => "0.25",
        ];
    }


}