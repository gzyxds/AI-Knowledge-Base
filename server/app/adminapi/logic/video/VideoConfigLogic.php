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

namespace app\adminapi\logic\video;

use app\common\enum\draw\DrawEnum;
use app\common\enum\VideoEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\ModelsCost;
use app\common\service\ConfigService;
use Exception;

/**
 * ai配置
 */
class VideoConfigLogic extends BaseLogic
{
    /**
     * @notes 配置详情
     * @return array
     * @author mjf
     * @date 2024/6/28 10:57
     */
    public static function detail(): array
    {
        $videoModels  = VideoEnum::getChannelDefaultConfig();
        $videoChannel = ConfigService::get('video_model', 'channel', VideoEnum::OPENAIHK);
        $cacheConfigs = ConfigService::get('video_model', 'configs', []);

        foreach ($videoModels as $key => &$item) {
            $cacheConfig = $cacheConfigs[$key] ?? [];
            $item = array_merge($item, $cacheConfig);
            if ($item['version'] == 'relax') {
                $item['version'] = 'pro';
            }
            $item['checked'] = $key == $videoChannel;
        }

        $exampleStatus = ConfigService::get('video_example', 'status', 1);
        $exampleData = ConfigService::get('video_example', 'data', []);

        if ($exampleStatus && empty($exampleData)) {
            $exampleData = [
                'A teddy bear in sunglasses playing electric guitar, dancing and headbanging in the jungle in front of a large beautiful waterfall'
            ];
        }

        // 翻译
        $translateDefault           = VideoEnum::getTranslateDefault();
        $translateData              = ConfigService::get('video_translate', 'data', $translateDefault);
        $translateData['api_model'] = $translateData['api_model'] ?? '';
        $translateData['api_list']  = DrawEnum::getTranslateConfig($translateData['api_list']);

        return [
            'video_models'  => $videoModels,
            'video_example' => [
                'status' => $exampleStatus,
                'data'   => $exampleData,
            ],
            'translate'     => $translateData,
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
            $videoChannel = VideoEnum::OPENAIHK;
            $videoModels = [];
            if (isset($post['video_models'])) {
                foreach ($post['video_models'] as $key => $item) {
                    if (isset($item['checked']) && $item['checked']) {
                        $videoChannel = $item['channel'];
                    }
                    if (isset($item['checked'])) {
                        unset($item['checked']);
                    }
                    $videoModels[$key] = $item;
                }
            }

            ConfigService::set('video_model', 'channel', $videoChannel);
            ConfigService::set('video_model', 'configs', $videoModels);

            $videoExample = $post['video_example'] ?? [];
            $exampleStatus = $videoExample['status'] ?? 0;
            $exampleLists = $videoExample['data'] ?? [];

            $exampleLists = array_filter($exampleLists, function($item) {
                return ($item !== null && $item !== '');
            });

            if ($exampleStatus && empty($exampleLists)) {
                throw new Exception('开启示例时，最少保留一条不为空的示例');
            }

            ConfigService::set('video_example', 'status', $exampleStatus);
            ConfigService::set('video_example', 'data', $exampleLists);

            if (!empty($post['translate'])) {
                $translateApi      = $post['translate']['api'] ?? '';
                $translateApiModel = $post['translate']['api_model'] ?? '';
                if (empty($translateApi)) {
                    throw new Exception('请选择翻译接口');
                }
                if ('baidu' != $translateApi) {
                    if (empty($translateApiModel)) {
                        throw new Exception('请选择子模型');
                    }
                    $model = ModelsCost::where(['channel' => $translateApi, 'name' => $translateApiModel])->findOrEmpty();
                    if ($model->isEmpty()) {
                        throw new Exception('模型错误，请刷新页面重新选择');
                    }
                }
                ConfigService::set('video_translate', 'data', $post['translate']);
            }

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}