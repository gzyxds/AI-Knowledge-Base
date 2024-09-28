<?php

namespace app\common\enum;

/**
 * AI搜索枚举
 */
class SearchEnum
{
    // 模型通道
    const TIANGONG = 'tiangong';

    /**
     * @notes 获取模型通道
     * @param bool|string $from
     * @return array|string
     * @author fzr
     */
    public static function getChannel(bool|string $from = true): array|string
    {
        $desc = [
            self::TIANGONG => '天工AI',
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 获取查询模式描述
     * @param bool|string $from
     * @param string $channel
     * @return string|array
     */
    public static function getModelDesc(bool|string $from = true, string $channel='tiangong'): string|array
    {
        $desc = [];
        if ($channel == self::TIANGONG) {
            $desc = [
                'search'   => '简易模式',
                'copilot'  => '增强模式',
                'research' => '研究模式',
            ];
        }

        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 获取搜索类型描述
     * @param bool|string $from
     * @param string $channel
     * @return string|array
     */
    public static function getTypeDesc(bool|string $from = true, string $channel='tiangong'): string|array
    {
        $desc = [];
        if ($channel == self::TIANGONG) {
            $desc = [
                'all'     => '全网搜索',
                'doc'     => '文档搜索',
                'scholar' => '学术搜索'
            ];
        }

        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}