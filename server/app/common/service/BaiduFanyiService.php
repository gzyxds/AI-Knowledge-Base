<?php
// +----------------------------------------------------------------------
// | likeshop开源商城系统
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | gitee下载：https://gitee.com/likeshop_gitee
// | github下载：https://github.com/likeshop-github
// | 访问官网：https://www.likeshop.cn
// | 访问社区：https://home.likeshop.cn
// | 访问手册：http://doc.likeshop.cn
// | 微信公众号：likeshop技术社区
// | likeshop系列产品在gitee、github等公开渠道开源版本可免费商用，未经许可不能去除前后端官方版权标识
// |  likeshop系列产品收费版本务必购买商业授权，购买去版权授权后，方可去除前后端官方版权标识
// | 禁止对系统程序代码以任何目的，任何形式的再发布
// | likeshop团队版权所有并拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeshop.cn.team
// +----------------------------------------------------------------------

namespace app\common\service;

use think\Exception;
use WpOrg\Requests\Requests;

/**
 * 百度翻译
 */
class BaiduFanyiService
{
    protected string     $from      = 'auto';// 翻译源语言
    protected string     $to        = 'en';  // 翻译目标语言
    protected mixed      $appid     = '';
    protected mixed      $secretKey = '';
    protected string     $apiUrl    = 'https://fanyi-api.baidu.com/api/trans/vip/translate';
    protected string|int $salt      = '';// 随机数
    protected string     $sign      = '';// 签名


    /**
     * @throws \Exception
     */
    public function __construct($appid = '', $secretKey = '')
    {
        if (!empty($appid) && !empty($secretKey)) {
            $this->appid = $appid;
            $this->secretKey = $secretKey;
        } else {
            $this->appid = ConfigService::get('baidufanyi_config', 'appid');
            $this->secretKey = ConfigService::get('baidufanyi_config', 'secret_key');
        }

        if (empty($this->appid) || empty($this->secretKey)) {
            throw new Exception('请联系管理员检查百度翻译配置');
        }

        $this->salt = rand(10000, 99999);
    }

    /**
     * @notes 通用文本翻译
     * @param string $prompt
     * @return mixed
     * @throws \Exception
     * @author ljj
     * @date 2024/5/13 6:55 下午
     */
    public function translate(string $prompt): mixed
    {
        // 计算签名
        $sign = $this->appid . $prompt . $this->salt . $this->secretKey;
        $sign = md5($sign);
        // 请求数据
        $data = [
            'q'     => urlencode($prompt),
            'appid' => $this->appid,
            'salt'  => $this->salt,
            'from'  => $this->from,
            'to'    => $this->to,
            'sign'  => $sign
        ];
        // 设置超时时间
        $options = [];
        $options['timeout'] = 20;
        $options['verify'] = false;
        $url = $this->apiUrl;
        foreach ($data as $key => $val) {
            if (stripos($url, "?") > 0) {
                $url .= "&$key=$val";
            } else {
                $url .= "?$key=$val";
            }
        }
        $response = Requests::get($url, [], $options);
        $result = @json_decode($response->body);
        if (isset($result->error_code) && $result->error_code == 54004) {
            throw new Exception('翻译额度不足');
        }
        if (isset($result->error_code) || !isset($result->trans_result[0]->dst)) {
            throw new Exception($result->error_msg ?? '翻译失败');
        }
        return $result->trans_result[0]->dst;
    }
}