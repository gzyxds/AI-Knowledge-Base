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

namespace app\api\logic;

use app\api\service\VideoService;
use app\common\enum\draw\DrawEnum;
use app\common\enum\member\MemberPackageEnum;
use app\common\enum\VideoEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\chat\Models;
use app\common\model\user\User;
use app\common\model\video\VideoRecord;
use app\common\model\video\VideoRecordsCollect;
use app\common\model\video\VideoStyle;
use app\common\service\ai\chat\OpenaiService;
use app\common\service\ai\chat\ZhipuService;
use app\common\service\BaiduFanyiService;
use app\common\service\ConfigService;
use Exception;

/**
 * 视频逻辑
 */
class VideoLogic extends BaseLogic
{
    /**
     * @notes 生成视频
     * @param int $userId
     * @param array $params
     * @return bool|array
     * @author mjf
     * @date 2024/5/29 16:23
     */
    public static function generate(int $userId, array $params): bool|array
    {
        try {
            $videoService = new VideoService($userId, $params);
            $videoService->video();
            $recordId = $videoService->getTaskRecordId();
            return ['record_id' => $recordId];
        } catch (Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }


    /**
     * @notes 详情
     * @param $params
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/5/30 10:38
     */
    public static function detail($params, $userId): array
    {
        if (empty($params['id'])) {
            return [];
        }
        $model = new VideoRecord();
        $lists = $model->field(['id','prompt', 'type','user_id', 'tags', 'image','channel',
            'video_url', 'style_id','status', 'create_time'])
//            ->where(['user_id' => $userId])
            ->whereIn('id', $params['id'])
            ->order('id desc')
            ->append(['status_desc', 'style_desc', 'type_desc'])
            ->select()
            ->toArray();
        $recordsId = VideoRecordsCollect::alias('vr')
            ->where(['vr.user_id'=>$userId])
            ->join('video_square vs','vr.square_id = vs.id')
            ->column('vs.records_id') ?: [] ;
        $userIds = array_column($lists,'user_id');
        $nicknameLists = User::where(['id'=>$userIds])->column('nickname','id');
        foreach ($lists as $key => $list){
            $lists[$key]['is_collect'] = 0;
            $lists[$key]['nickname'] = $nicknameLists[$list['user_id']] ?? '';
            if(in_array($list['id'],$recordsId)){
                $lists[$key]['is_collect'] = 1;
            }
        }
        return $lists;
    }

    /**
     * @notes 配置
     * @param $userId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author mjf
     * @date 2024/7/1 14:22
     */
    public static function config($userId): array
    {
        $styleModel = new VideoStyle();
        $style = $styleModel
            ->field(['id,name,image'])
            ->where(['status' => YesNoEnum::YES])
            ->order(['sort'=>'desc','id'=>'desc'])
            ->select()
            ->toArray();

        $exampleStatus = ConfigService::get('video_example', 'status', 0);
        $exampleData = ConfigService::get('video_example', 'data', []);

        $videoService = new VideoService($userId);
        $videoModels = $videoService->getVideoConfig();

        $translateData    = ConfigService::get('video_translate', 'data', VideoEnum::getTranslateDefault());

        return [
            'is_member' => $videoService->checkVip(MemberPackageEnum::APPLY_VIDEO),
            'channel'   => $videoModels['channel'],
            'model'     => $videoModels['models'],
            'style'     => $style,
            'example'   => [
                'status'    => $exampleStatus,
                'data'      => $exampleData,
            ],
            'translate_switch' => (int)$translateData['status'] ?? 0,
        ];
    }

    /**
     * @notes 删除
     * @param int|array $params
     * @return bool
     * @author mjf
     * @date 2024/5/27 12:13
     */
    public static function del(int|array $params, $userId): bool
    {
        return VideoRecord::destroy(['user_id' => $userId, 'id' => $params['id'] ?? 0]);
    }

    /**
     * @notes 翻译
     * @param $prompt
     * @return bool|array
     * @author mjf
     * @date 2024/7/12 12:12
     */
    public static function translate($prompt): bool|array
    {
        try {
            if (empty($prompt)) {
                throw new Exception('请填写描述词');
            }

            $config = ConfigService::get('video_translate', 'data', VideoEnum::getTranslateDefault());
            if ($config['status'] != 1) {
                throw new Exception('翻译功能未开启');
            }
            if (empty($config['api'])) {
                throw new Exception('翻译配置参数不全，请联系管理员处理');
            }

            switch ($config['api']) {
                case DrawEnum::TRANSLATE_BAIDU:
                    if (empty($config['baidu_appid']) || empty($config['baidu_secret_key'])) {
                        throw new Exception('翻译配置参数不全，请联系管理员处理');
                    }
                    $result = (new BaiduFanyiService($config['baidu_appid'], $config['baidu_secret_key']))->translate($prompt);
                    break;
                case DrawEnum::TRANSLATE_OPENAI:
                    $model               = (new Models())->where(['id' => 1])->findOrEmpty()->toArray();
                    $configs             = json_decode($model['configs'], true);
                    $configs['channel']  = $model['channel'];
                    $configs['model']    = $config['api_model'];
                    $configs['model_id'] = $model['id'];
                    $reprompt            = str_replace('{prompt}', $prompt, $config['prompt']);
                    $message             = [[
                        'role'    => 'user',
                        'content' => $reprompt
                    ]];
                    $translateModel      = (new OpenaiService($configs))->chatHttpRequest($message);
                    if (!isset($translateModel['choices'][0]['message']['content'])) {
                        throw new Exception('翻译失败');
                    }
                    $result = $translateModel['choices'][0]['message']['content'];
                    break;
                case DrawEnum::TRANSLATE_ZHIPU:
                    $model               = (new Models())->where(['id' => 4])->findOrEmpty()->toArray();
                    $configs             = json_decode($model['configs'], true);
                    $configs['channel']  = $model['channel'];
                    $configs['model']    = $config['api_model'];
                    $configs['model_id'] = $model['id'];
                    $reprompt            = str_replace('{prompt}', $prompt, $config['prompt']);
                    $message             = [[
                        'role'    => 'user',
                        'content' => $reprompt
                    ]];
                    $translateModel      = (new ZhipuService($configs))->chatHttpRequest($message);
                    if (!isset($translateModel['choices'][0]['message']['content'])) {
                        throw new Exception('翻译失败');
                    }
                    $result = $translateModel['choices'][0]['message']['content'];
                    break;
            }
            return ['result' => $result];
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 收藏
     * @param $userId
     * @param $params
     * @author 段誉
     * @date 2023/6/27 11:33
     */
    public static function collect($userId, $params)
    {
        if ($params['status']) {
            $collect = VideoRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->findOrEmpty();
            if($collect->isEmpty()) {
                // 收藏
                VideoRecordsCollect::create([
                    'user_id' => $userId,
                    'square_id' => $params['records_id'],
                ]);
            }
        } else {
            // 取消收藏
            VideoRecordsCollect::where([
                'user_id' => $userId,
                'square_id' => $params['records_id'],
            ])->delete();
        }
    }
}