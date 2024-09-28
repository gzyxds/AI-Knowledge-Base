<?php
// +----------------------------------------------------------------------
// | WaitChat智能聊天系统
// +----------------------------------------------------------------------
// | 这不是一个自由软件,您只能在不用于商业目的的前提下对程序代码进行修改和使用。
// | 任何企业和个人不允许对程序代码以任何形式任何目的再发布,商业使用请获取授权。
// | 获取商业授权后,允许对程序进行二次开发修改,并且可进行商业运营使用。
// +----------------------------------------------------------------------
// | 官方网站: https://www.waitadmin.cn
// | WaitAdmin团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | Author: WaitAdmin Team <2474369941@qq.com | 2273716447@qq.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\common\cache;

use app\common\enum\ChatEnum;
use app\common\enum\SearchEnum;
use app\common\enum\VoiceEnum;
use app\common\model\chat\KeyPool;
use app\common\model\chat\KeyRule;
use app\common\service\ConfigService;
use think\facade\Cache;

/**
 * key池缓存类
 */
class KeyPoolCache
{
    /**
     * 缓存键名
     * @var string
     */
    private string $cacheName;

    /**
     * AI键名
     * @var string|int
     */
    private string|int $aiKey;

    /**
     * API密钥
     * @var string|array
     */
    private string|array $apiKey = '';

    /**
     * 密钥类型
     * @var int
     */
    protected int $type = 0;

    /**
     * 密钥渠道
     * @var string
     */
    protected string $channel = '';

    /**
     * 密钥缓存
     * @var array
     */
    private array $cacheApiKey = [];

    /**
     * @notes 初始化
     * @param $key (AI密钥名如: kdxf、openai, 或者是 模型的ID)
     * @param int $type (密钥类型如: 1=对话,2=向量, 3=语音播报, 4=语音输入, 5=AI音乐, 6=AI视频, 7=AI搜索)
     * @param string $channel (通道名称: 对话/向量必传)
     * @author fzr
     */
    public function __construct($key, int $type=1, string $channel='')
    {
        $this->aiKey = $key;
        $this->channel = $channel;
        $this->cacheName = 'ai:key:' . $key;
        if ($type) {
            $this->type = $type;
            $this->cacheName.= ':'.$type;
        }
    }

    /**
     * @notes 获取Key
     * @return string|array|null
     * @author fzr
     */
    public function getKey(): string|array|null
    {
        $where = [];
        $where[] = ['type', '=', $this->type];

        if (in_array($this->type, [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
            $where[] = ['model_id', '=', intval($this->aiKey)];
        } else {
            $where[] = ['model_id', '=', 0];
            $where[] = ['channel', '=', $this->aiKey];
        }

        $cacheKey = Cache::get($this->cacheName);
        if(empty($cacheKey)){
            $keyPool = (new KeyPool())
                ->where($where)
                ->where(['status'=>1])
                ->column('key,appid,secret');

            $multiKeyModel = [
                ChatEnum::XUNFEI,
                ChatEnum::BAIDU,
                ChatEnum::MINIMAX,
                VoiceEnum::KDXF,
                SearchEnum::TIANGONG
            ];

            if(in_array($this->channel, $multiKeyModel)){
                $cacheKey = $keyPool;
            } else {
                $cacheKey = array_column($keyPool,'key');
            }
        }

        // 没有配置KEY
        if (empty($cacheKey)) {
            return '';
        }

        // 从数组取出KEY
        $key = array_shift($cacheKey);
        $this->cacheApiKey = $cacheKey;
        $this->apiKey = $key;

        // 重新设置缓存
        $this->setKey();

        // 返回读取KEY
        return $key;
    }

    /**
     * @notes  设置缓存Key
     * @param array $key
     * @return bool
     * @author fzr
     */
    public function setKey(array $key = []): bool
    {
        $apiKey = $this->cacheApiKey;
        if($key){
            $apiKey = $key;
        }
        return Cache::set($this->cacheName,$apiKey);
    }

    /**
     * @notes  删除Key
     * @return bool
     * @author fzr
     */
    public function delKey(): bool
    {
        return Cache::delete($this->cacheName);
    }

    /**
     * 下架无效Key
     *
     * @param string|array $errorMsg
     * @param string $api
     * @return string
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public function takeDownKey(string|array $errorMsg, string $api=''): string
    {
        $status = intval(ConfigService::get('key_pool','key_auto_down', 1));

        $where = [];
        $where[] = ['type', '=', $this->type];
        if (in_array($this->type, [ChatEnum::MODEL_TYPE_CHAT, ChatEnum::MODEL_TYPE_EMB])) {
            $where[] = ['model_id', '=', (int)$this->aiKey];
        } else {
            $where[] = ['model_id', '=', 0];
            $where[] = ['channel', '=', $this->aiKey];
        }

        $tisMsg = '';
        $keyDownRule = (new KeyRule())->field('rule,prompt')->where(['status'=>1])->where($where)->select()->toArray();
        if (!empty($keyDownRule)) {
            foreach ($keyDownRule as $rule) {
                if (str_contains($errorMsg, $rule['rule'])) {
                    $tisMsg = $rule['prompt'] .': '. $rule['rule'];
                }
            }
        }

        if (!$status) {
            return $tisMsg ?: $errorMsg;
        }

        if (empty($tisMsg)) {
            return $errorMsg;
        }

        if (is_array($this->apiKey)) {
            foreach ($this->apiKey as $item => $value){
                $where[] = [$item, '=', $value];
            }
        } else {
            $where[] = ['key', '=', $this->apiKey];
        }

        (new KeyPool())->where($where)->update([
            'status' => 0,
            'api'    => $api,
            'notice' => $tisMsg
        ]);

        $this->delKey();
        return $tisMsg;
    }
}