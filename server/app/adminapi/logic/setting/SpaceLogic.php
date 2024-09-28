<?php

namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class SpaceLogic extends BaseLogic
{
    /**
     * @notes 空间设置详情
     * @return array
     * @author fzr
     */
    public static function detail(): array
    {
        $space = ConfigService::get('know', 'space', '');
        return [
            'space' => $space
        ];
    }

    /**
     * @notes 空间设置保存
     * @param array $params
     * @return bool
     * @author fzr
     */
    public static function save(array $params): bool
    {
        if (is_numeric($params['space']) and $params['space'] < 0) {
            self::setError('空间大小不能少于0');
            return false;
        }

        ConfigService::set('know', 'space', $params['space']??'');
        return true;
    }
}