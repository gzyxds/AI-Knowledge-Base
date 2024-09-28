<?php

namespace app\adminapi\logic\setting\ai;

use app\common\enum\draw\DrawEnum;
use app\common\enum\draw\DrawTaskEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\ModelsCost;
use app\common\service\ConfigService;
use think\Exception;

class AiDrawLogic extends BaseLogic
{
    /**
     * @notes 获取SD绘画配置
     * @return array
     * @author JXDN
     * @date 2024/5/15 18:25
     */
    public static function detail(string $model): array
    {
        $result = ConfigService::get('draw_config', $model, DrawEnum::getDrawDefaultConfig($model));
        $result['time_out'] = floatval($result['time_out'] ?? 10);
        $result['translate_api_model'] = $result['translate_api_model'] ?? '';
        $result['translate_api_list'] = DrawEnum::getTranslateConfig($result['translate_api_list']);
        if ($model == DrawEnum::API_MJ_GOAPI) {
            if (empty($result['process_mode'])) {
                $result['process_mode'] = DrawTaskEnum::MODE_FAST;
            }
            if (empty($result['website']) || empty($result['doc'])) {
                $result['website'] = 'https://dashboard.goapi.ai/?referrerId=e6d5f588-d5ff-4028-b606-7baf0f7fc915';
                $result['doc'] = 'https://www.goapi.ai/docs/pricing-plan';
            }
        }
        return $result;
    }

    /**
     * @notes 保存SD绘画配置
     * @param array $post
     * @return bool
     * @author JXDN
     * @date 2024/5/15 18:25
     */
    public static function save(array $post): bool
    {
        try {
            $translateApi = $post['translate_api'];
            $translateApiModel = $post['translate_api_model'];
            if(empty($translateApi)){
                throw new Exception('请选择翻译接口');
            }
            if('baidu' != $translateApi){
                if(empty($translateApiModel)){
                    throw new Exception('请选择子模型');
                }
                $model = ModelsCost::where(['channel'=>$translateApi,'name'=>$translateApiModel])->findOrEmpty();
                if($model->isEmpty()){
                    throw new Exception('模型错误，请刷新页面重新选择');
                }
            }

            $drawModel = $post['model'] ?? DrawEnum::API_SD;
            ConfigService::set('draw_config', $drawModel, $post);
            ConfigService::set('baidufanyi_config', 'appid', $post['appid'] ?? '');
            ConfigService::set('baidufanyi_config', 'secret_key', $post['secret_key'] ?? '');
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}