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

namespace app\adminapi\logic\music;

use app\common\enum\ChatEnum;
use app\common\enum\MusicEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\service\ConfigService;
use Exception;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * ai配置
 */
class MusicConfigLogic extends BaseLogic
{
    /**
     * @notes 配置详情
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @author mjf
     * @date 2024/5/28 10:57
     */
    public static function detail(): array
    {
        $musicModels  = MusicEnum::getChannelDefaultConfig();
        $musicChannel = ConfigService::get('music_model', 'channel', MusicEnum::GOAPI);
        $cacheConfigs = ConfigService::get('music_model', 'configs', []);

        foreach ($musicModels as $key => &$item) {
            $cacheConfig  = $cacheConfigs[$key] ?? [];
            $item = array_merge($item, $cacheConfig);

            $item['checked'] = $key == $musicChannel;
            $item['name'] = $item['name'] == 'Go_API' ? 'GoAPI' : $item['name'];
        }

        $imagineStatus = ConfigService::get('music_imagine', 'status', 0);
        $imaginePrice = ConfigService::get('music_imagine', 'price', 0);
        $imagineModelId = ConfigService::get('music_imagine', 'model_id', 0);
        $imagineModelCostId = ConfigService::get('music_imagine', 'cost_id', 0);

        if (empty($imagineModelId)) {
            $imagineModelId = (new Models())
                    ->where(['type' => ChatEnum::MODEL_TYPE_CHAT, 'is_enable' => 1, 'is_default' => 1])
                    ->value('id') ?? 0;
            if ($imagineModelId > 0) {
                $imagineModelCostId = (new ModelsCost())
                        ->where(['model_id' => $imagineModelId, 'status' => 1])
                        ->order(['sort' => 'desc', 'id' => 'desc'])
                        ->value('id') ?? 0;
            }
        }

        $chatModels = (new Models())
            ->field(['id,type,channel,name,is_default'])
            ->where(['type' => ChatEnum::MODEL_TYPE_CHAT, 'is_enable' => 1])
            ->order('sort asc, id desc')
            ->select()
            ->toArray();

        foreach ($chatModels as $key => $chatModel) {
            $chatModels[$key]['models'] = (new ModelsCost())->field(['id,name,alias,price,sort,status'])
                ->where(['model_id'=>$chatModel['id']])
                ->select()->toArray();
        }

        return [
            'music_models' => $musicModels,
            'music_imagine' => [
                'status' => $imagineStatus,
                'price' => $imaginePrice,
                'model_id' => $imagineModelId,
                'cost_id' => $imagineModelCostId,
            ],
            'music_version' => MusicEnum::getVersion(),
            'chat_models' => $chatModels
        ];
    }

    /**
     * @notes 配置保存
     * @param array $post
     * @return bool
     * @author mjf
     * @date 2024/5/28 11:22
     */
    public static function save(array $post): bool
    {
        try {
            $musicChannel = MusicEnum::GOAPI;
            $musicModels = [];
            if (isset($post['music_models'])) {
                foreach ($post['music_models'] as $key => $item) {
                    if (isset($item['checked']) && $item['checked']) {
                        $musicChannel = $item['channel'];
                        if (empty($item['version'])) {
                            throw new Exception("最少选择一个版本");
                        }
                    }
                    if (isset($item['checked'])) {
                        unset($item['checked']);
                    }
                    $musicModels[$key] = $item;
                }
            }

            ConfigService::set('music_model', 'channel', $musicChannel);
            ConfigService::set('music_model', 'configs', $musicModels);

            $imagineConfig = $post['music_imagine'] ?? [];
            ConfigService::set('music_imagine', 'status', $imagineConfig['status'] ?? 0);
            ConfigService::set('music_imagine', 'price', $imagineConfig['price'] ?? 0);
            ConfigService::set('music_imagine', 'model_id', $imagineConfig['model_id'] ?? 0);
            ConfigService::set('music_imagine', 'cost_id', $imagineConfig['cost_id'] ?? 0);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}