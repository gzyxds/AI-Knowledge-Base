<?php

namespace app\adminapi\logic\search;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class SettingLogic extends BaseLogic
{
    /**
     * @notes 基础配置详情
     * @return array
     * @author fzr
     */
    public static function basisConfig(): array
    {
        $config = ConfigService::get('ai_search');
        return [
            'status'  => intval($config['status']??0),
            'channel' => $config['channel']??'tiangong',
            'price'   => $config['price']??0
        ];
    }

    /**
     * @notes 基础配置保存
     * @param array $post
     * @return bool
     */
    public static function basisSave(array $post): bool
    {
        if (!isset($post['price']) || !is_numeric($post['price']) || $post['price'] < 0) {
            self::setError('请正确设置消耗价格');
            return false;
        }

        ConfigService::set('ai_search', 'status', $post['status']??0);
        ConfigService::set('ai_search', 'channel', $post['channel']??'');
        ConfigService::set('ai_search', 'price', $post['price']);
        return true;
    }

    /**
     * @notes 示例配置详情
     * @return array
     * @author fzr
     */
    public static function exampleConfig(): array
    {
        $config = ConfigService::get('ai_search');
        return [
            'example_status'  => intval($config['example_status']??0),
            'example_content' => $config['example_content']??''
        ];
    }

    /**
     * @notes 示例配置保存
     * @param array $post
     * @return void
     * @author fzr
     */
    public static function exampleSave(array $post): void
    {
        ConfigService::set('ai_search', 'example_status', $post['example_status']??0);
        ConfigService::set('ai_search', 'example_content', $post['example_content']??'');
    }
}