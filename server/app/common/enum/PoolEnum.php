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

namespace app\common\enum;

/**
 * key池枚举类
 */
class PoolEnum
{
    const TYPE_CHAT         = 1;  // 对话模型
    const TYPE_EMB          = 2;  // 向量模型
    const TYPE_VOICE_OUTPUT = 3;  // 语音合成
    const TYPE_VOICE_INPUT  = 4;  // 语音输入
    const TYPE_MUSIC        = 5;  // AI音乐
    const TYPE_VIDEO        = 6;  // AI视频
    const TYPE_SEARCH       = 7;  // AI搜索
    const TYPE_DRAW         = 8;  // AI绘画
}