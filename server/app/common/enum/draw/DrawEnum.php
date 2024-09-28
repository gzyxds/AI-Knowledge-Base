<?php
// +----------------------------------------------------------------------
// | likeshop100%开源免费商用商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | 商业版本务必购买商业授权，以免引起法律纠纷
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshopTeam
// +----------------------------------------------------------------------
namespace app\common\enum\draw;

use app\common\model\chat\Models;

/**
 * 绘图枚举
 * Class DrawEnum
 * @package app\common\enum
 */
class DrawEnum
{
    // 状态
    const STATUS_NOT = 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                // 待处理
    const STATUS_IN_PROGRESS = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  // 执行中
    const STATUS_FAIL = 2;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    // 失败
    const STATUS_SUCCESS = 3;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    // 成功

    // 类型
    const TYPE_TEXT_TO_IMAGE = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     // 文生图
    const TYPE_IMAGE_TO_IMAGE = 2;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   // 图生图
    const TYPE_UPSCALE_IMAGE = 3;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          // 图变大
    const TYPE_VARIATION_IMAGE = 4;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             // 图变化

    // 绘画api配置
    const API_SD = 'sd';
    const API_DALLE3 = 'dalle3';
    const API_MJ_GOAPI = 'mj_goapi';

    // 提示词翻译API
    const TRANSLATE_BAIDU = 'baidu';
    const TRANSLATE_OPENAI = 'openai';
    const TRANSLATE_ZHIPU = 'zhipu';

    // 绘画操作
    const ACTION_GENERATE = "generate";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       // 生图
    const ACTION_UPSCALE = "upscale";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        // 放大
    const ACTION_VARIATION = "variation";                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       // 变换

    /**
     * @notes 获取模型名称
     * @param bool|string $from
     * @return mixed
     * @author JXDN
     * @date 2024/06/04 18:23
     */
    public static function getAiModelName(bool|string $from = true): mixed
    {
        $desc = [
            self::API_SD       => 'SD绘图',
            self::API_DALLE3   => 'DALLE3绘画',
            self::API_MJ_GOAPI => 'Midjourney绘画',
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }

    /**
     * @notes 绘画默认配置
     * @param bool $from
     * @return array|array[]
     * @author JXDN
     * @date 2024/06/03 12:04
     */
    public static function getDrawDefaultConfig(bool|string $from = true): array
    {
        $desc = [
            // sd绘图
            self::API_SD     => [
                "status"             => 0,
                "translate_api"      => DrawEnum::TRANSLATE_BAIDU,
                "translate_switch"   => 0,
                "translate_api_list" => self::getTranslateConfig(),
                "translate_prompt"   => "",
                "time_out"           => 10,
                "proxy_url"          => "",
                "app_name"           => "SD绘图",
                "diy_name"           => "SD绘图",
                "power"              => 10,
                "file_size"          => 10,
            ],
            // DALL·E3绘图绘图
            self::API_DALLE3 => [
                'status'             => 0,
                'proxy_url'          => '',
                "translate_api"      => DrawEnum::TRANSLATE_BAIDU,
                "translate_switch"   => 0,
                "translate_api_list" => self::getTranslateConfig(),
                "translate_prompt"   => "",
                "app_name"           => "DALLE3绘图",
                "diy_name"           => "DALLE3绘图",
                "power"              => 10,
            ],
            // GoApi-midjourney
            self::API_MJ_GOAPI  => [
                "status"             => 0,
                "translate_api"      => DrawEnum::TRANSLATE_BAIDU,
                "translate_switch"   => 0,
                "translate_api_list" => self::getTranslateConfig(),
                "translate_prompt"   => "",
                "time_out"           => 10,
                "proxy_url"          => "",
                "app_name"           => "Midjourney绘图",
                "diy_name"           => "Midjourney绘图",
                "power"              => 10,
                "file_size"          => 10,
                'process_mode'       => DrawTaskEnum::MODE_FAST,
                'website'            => 'https://dashboard.goapi.ai/?referrerId=e6d5f588-d5ff-4028-b606-7baf0f7fc915',
                'doc'                => 'https://www.goapi.ai/docs/pricing-plan',
            ],
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? [];
    }

    /**
     * @notes 获取翻译配置
     * @return array
     * @author cjhao
     * @date 2023/7/17 17:15
     */
    public static function getTranslateConfig(array $translateApiList = []): array
    {
        $gpt35 = (new Models())
            ->where(['id' => 1])
            ->field(['id,type,channel,logo,name'])
            ->append(['models_lists'])
            ->findOrEmpty()->toArray();
        $zhipu = (new Models())
            ->where(['id' => 4])
            ->field(['id,type,channel,logo,name,is_system,is_enable'])
            ->append(['models_lists'])
            ->findOrEmpty()->toArray();

        return [
            [
                'model'      => self::TRANSLATE_BAIDU,
                'model_name' => '百度翻译',
                'model_list' => [],
                'prompt'     => $translateApiList[0]['prompt'] ?? '',
            ],
            [
                'model'      => $gpt35['channel'],
                'model_name' => $gpt35['name'],
                'model_list' => $gpt35['models_lists'],
                'prompt'     => $translateApiList[1]['prompt'] ?? '',
            ],
            [
                'model'      => $zhipu['channel'],
                'model_name' => $zhipu['name'],
                'model_list' => $zhipu['models_lists'],
                'prompt'     => $translateApiList[2]['prompt'] ?? '',
            ]
        ];
    }

    /**
     * @notes 获取绘画操作及图片索引
     * @param string $action
     * @return array|int[]
     * @author 段誉
     * @date 2023/8/3 15:36
     */
    public static function getActionAndIndex(string $action): array
    {
        $specUv = [
            DrawTaskEnum::ACTION_HIGH_VARIATION,
            DrawTaskEnum::ACTION_LOW_VARIATION,
            DrawTaskEnum::ACTION_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_UPSCALE_CREATIVE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_CREATIVE
        ];

        if (!in_array($action, $specUv)) {
            // 处理带有数字后缀的动作
            $pattern1 = '/^' . preg_quote(DrawTaskEnum::ACTION_UPSCALE, '/') . '(\d+)$/';
            $pattern2 = '/^' . preg_quote(DrawTaskEnum::ACTION_VARIATION, '/') . '(\d+)$/';
            if (preg_match($pattern1, $action, $matches) || preg_match($pattern2, $action, $matches)) {
                return [
                    'action' => $action === DrawTaskEnum::ACTION_UPSCALE . $matches[1]
                        ? DrawTaskEnum::ACTION_UPSCALE
                        : DrawTaskEnum::ACTION_VARIATION,
                    'index'  => $matches[1],
                ];
            }
        }

        // 使用switch语句处理固定值的比较
        return match ($action) {
            DrawTaskEnum::ACTION_PAN_DOWN,
            DrawTaskEnum::ACTION_PAN_UP,
            DrawTaskEnum::ACTION_PAN_LEFT,
            DrawTaskEnum::ACTION_PAN_RIGHT,
            DrawTaskEnum::ACTION_ZOOM_OUT_2X,
            DrawTaskEnum::ACTION_ZOOM_OUT_1_5X,
            DrawTaskEnum::ACTION_LOW_VARIATION,
            DrawTaskEnum::ACTION_HIGH_VARIATION,
            DrawTaskEnum::ACTION_REDO_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_REDO_UPSCALE_CREATIVE,
            DrawTaskEnum::ACTION_UPSCALE_SUBTLE,
            DrawTaskEnum::ACTION_UPSCALE_CREATIVE => ['action' => $action, 'index' => '1'],
            default => ['action' => DrawTaskEnum::ACTION_GENERATE, 'index' => '1'],
        };
    }
}