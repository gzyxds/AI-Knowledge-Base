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

namespace app\api\logic\draw;


use app\common\enum\draw\DrawEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\Models;
use app\common\model\draw\DrawPromptCategory;
use app\common\model\draw\DrawPrompt;
use app\common\model\draw\DrawPromptExample;
use app\common\service\ai\chat\OpenaiService;
use app\common\service\ai\chat\ZhipuService;
use app\common\service\BaiduFanyiService;
use app\common\service\ConfigService;


/**
 * 绘图关键词
 * Class DrawPromptLogic
 * @package app\api\logic
 */
class DrawPromptLogic extends BaseLogic
{
    /**
     * @notes 关键词列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 10:32
     */
    public static function category($model)
    {
        return DrawPromptCategory::where(['status' => 1, 'pid' => 0])
            ->where(['model' => $model])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->select()
            ->toArray();
    }

    /**
     * @notes 关键词
     * @param $cateId
     * @return DrawPrompt[]|array|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/6/28 11:12
     */
    public static function prompt($cateId, $model)
    {
        $prompts = DrawPrompt::where(['status' => 1, 'category_id' => $cateId])
            ->where(['model' => $model])
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();

        // 当前分类直属关键词
        $promptData = [];
        foreach ($prompts as $prompt) {
            $promptData[] = [
                'prompt'    => $prompt['prompt'],
                'prompt_en' => $prompt['prompt_en'],
            ];
        }

        $catePromptData = [];

        $childCateData = DrawPromptCategory::where(['status' => 1, 'pid' => $cateId])
            ->select()->toArray();

        foreach ($childCateData as $childCate) {
            $catePrompts = DrawPrompt::where(['status' => 1, 'category_id' => $childCate['id']])
                ->hidden(['create_time', 'update_time', 'delete_time'])
                ->order(['sort' => 'desc', 'id' => 'desc'])
                ->select()->toArray();

            $catePromptArr = [];
            foreach ($catePrompts as $catePrompt) {
                $catePromptArr[] = [
                    'prompt'    => $catePrompt['prompt'],
                    'prompt_en' => $catePrompt['prompt_en'],
                ];
            }

            $catePromptData[] = [
                'cate_id' => $childCate['id'],
                'name'    => $childCate['name'],
                'prompt'  => $catePromptArr
            ];
        }

        return [
            'prompt'      => $promptData,
            'cate_prompt' => $catePromptData,
        ];
    }

    /**
     * @notes 示例列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2023/7/17 16:31
     */
    public static function example($params)
    {
        $where = [];
        if (empty($params['model'])) {
            $where[] = ['model', '=', DrawEnum::API_SD];
        } else {
            $where[] = ['model', '=', $params['model']];
        }
        $lists = DrawPromptExample::where(['status' => YesNoEnum::YES])
            ->where($where)
            ->hidden(['create_time', 'update_time', 'delete_time'])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();
        return $lists;
    }

    /**
     * @notes 翻译
     * @param $prompt
     * @return mixed
     * @throws \Exception
     * @author 段誉
     * @date 2023/7/18 9:43
     */
    public static function translate($prompt, $model)
    {
        try {
            $config = ConfigService::get('draw_config', $model, DrawEnum::getDrawDefaultConfig($model));
            if(empty($config['translate_api'])){
                throw new \Exception('翻译配置参数不全，请联系管理员处理');
            }
            if($config['translate_switch'] != 1){
                throw new \Exception('翻译功能未开启');
            }

            switch ($config['translate_api']) {
                case DrawEnum::TRANSLATE_BAIDU:
                    $result = (new BaiduFanyiService())->translate($prompt);
                    break;
                case DrawEnum::TRANSLATE_OPENAI:
                    $model = (new Models())->where(['id' => 1])->findOrEmpty()->toArray();
                    $configs = json_decode($model['configs'], true);
                    $configs['channel'] = $model['channel'];
                    $configs['model'] = $config['translate_api_model'];
                    $configs['model_id'] = $model['id'];
                    $reprompt = str_replace('{prompt}', $prompt, $config['translate_prompt']);
                    $message = [[
                        'role'    => 'user',
                        'content' => $reprompt
                    ]];
                    $translateModel = (new OpenaiService($configs))->chatHttpRequest($message);
                    if(!isset($translateModel['choices'][0]['message']['content'])){
                        throw new \Exception('翻译失败');
                    }
                    $result = $translateModel['choices'][0]['message']['content'];
                    break;
                case DrawEnum::TRANSLATE_ZHIPU:
                    $model = (new Models())->where(['id' => 4])->findOrEmpty()->toArray();
                    $configs = json_decode($model['configs'], true);
                    $configs['channel'] = $model['channel'];
                    $configs['model'] = $config['translate_api_model'];
                    $configs['model_id'] = $model['id'];
                    $reprompt = str_replace('{prompt}', $prompt, $config['translate_prompt']);
                    $message = [[
                        'role'    => 'user',
                        'content' => $reprompt
                    ]];
                    $translateModel = (new ZhipuService($configs))->chatHttpRequest($message);
                    if(!isset($translateModel['choices'][0]['message']['content'])){
                        throw new \Exception('翻译失败');
                    }
                    $result = $translateModel['choices'][0]['message']['content'];
                    break;
            }
            return ['result'=>$result];
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }


    }

}