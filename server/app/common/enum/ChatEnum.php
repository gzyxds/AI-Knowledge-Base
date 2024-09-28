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
declare (strict_types = 1);

namespace app\common\enum;

class ChatEnum
{
    const MODEL_TYPE_CHAT = 1; // 对话模型
    const MODEL_TYPE_EMB  = 2; // 向量模型

    const GPT35   = 'gpt3.5';   // GPT3.5
    const GPT40   = 'gpt4.0';   // GPT4.0
    const API2D35 = 'api2d3.5'; // api2d3.5
    const API2D40 = 'api2d4.0'; // api2d4.0
    const ZHUPI   = 'zhipu';    // ChatGLM
    const XUNFEI  = 'xunfei';   // 讯飞星火
    const BAIDU   = 'baidu';    // 文言一心
    const QWEN    = 'qwen';     // 通义千问
    const DOUBAO  = 'doubao';   // 字节豆包
    const OLLAMA  = 'ollama';   // ollama
    const AZURE   = 'azure';    // azure
    const MINIMAX = 'minimax';  // MiniMax

    /**
     * 获取模型名称
     *
     * @param bool|string $from
     * @return array|string
     * @author fzr
     */
    public static function getAiModelName(bool|string $from = true): array|string
    {
        $desc = [
            self::GPT35   => 'gpt3.5',
            self::GPT40   => 'gpt4.0',
            self::API2D35 => 'api2d3.5',
            self::API2D40 => 'api2d4.0',
            self::ZHUPI   => '智普AI',
            self::XUNFEI  => '讯飞星火',
            self::BAIDU   => '文心一言',
            self::QWEN    => '通义千问',
            self::DOUBAO  => '字节豆包',
            self::OLLAMA  => 'Ollama',
            self::AZURE   => 'Azure',
            self::MINIMAX => 'MiniMax',
        ];
        if(true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}