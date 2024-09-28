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


namespace app\common\service\pay;


use app\common\enum\PayEnum;
use app\common\enum\user\UserTerminalEnum;
use app\common\logic\PayNotifyLogic;
use app\common\model\member\MemberOrder;
use app\common\model\Order;
use app\common\model\recharge\RechargeOrder;
use app\common\model\user\UserAuth;
use app\common\service\wechat\WeChatConfigService;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Pay\Application;
use EasyWeChat\Pay\Message;
use Exception;
use Psr\Http\Message\ResponseInterface;
use think\facade\Log;
use think\Model;


/**
 * 微信支付
 * Class WeChatPayService
 * @package app\common\server
 */
class WeChatPayService extends BasePayService
{
    /**
     * 授权信息
     * @var UserAuth|array|Model
     */
    protected mixed $auth;

    /**
     * 微信配置
     * @var
     */
    protected mixed $config;

    /**
     * easyWeChat实例
     * @var
     */
    protected mixed $app;

    /**
     * 当前使用客户端
     * @var int
     */
    protected int $terminal;

    /**
     * 初始化微信支付配置
     * @param $terminal //用户终端
     * @param null $userId //用户id(获取授权openid)
     * @throws InvalidArgumentException
     */
    public function __construct($terminal, $userId = null)
    {
        $this->terminal = $terminal;
        $this->config   = WeChatConfigService::getPayConfigByTerminal($terminal);
        $this->app = new Application($this->config);
        if ($userId !== null) {
            $this->auth = (new UserAuth())->where(['user_id' => $userId, 'terminal' => $terminal])->findOrEmpty();
        }
    }

    /**
     * @notes 发起微信支付统一下单
     * @param $from
     * @param $order
     * @return array|false
     * @author 段誉
     * @date 2021/8/4 15:05
     */
    public function pay($from, $order): bool|array
    {
        try {
            switch ($this->terminal) {
                case UserTerminalEnum::WECHAT_MMP:
                    $config = WeChatConfigService::getMnpConfig();
                    $result = $this->jsapiPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::WECHAT_OA:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->jsapiPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::IOS:
                case UserTerminalEnum::ANDROID:
                    $config = WeChatConfigService::getOpConfig();
                    $result = $this->appPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::H5:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->mwebPay($from, $order, $config['app_id']);
                    break;
                case UserTerminalEnum::PC:
                    $config = WeChatConfigService::getOaConfig();
                    $result = $this->nativePay($from, $order, $config['app_id']);
                    break;
                default:
                    throw new Exception('支付方式错误');
            }

            return [
                'config' => $result,
                'pay_way' => PayEnum::WECHAT_PAY
            ];
        } catch (Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes jsapiPay
     * @param $from
     * @param $order
     * @param $appId
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 12:12
     */
    public function jsapiPay($from, $order, $appId): array
    {

        $response = $this->app->getClient()->postJson("v3/pay/transactions/jsapi", [
            "appid"        => $appId,
            "mchid"        => $this->config['mch_id'],
            "description"  => $this->payDesc($from),
            "out_trade_no" => $order['pay_sn'],
            "notify_url"   => $this->config['notify_url'],
            "amount" => [
                "total" => intval($order['order_amount'] * 100),
            ],
            "payer" => [
                "openid" => (isset($order['openid']) && !empty($order['openid'])) ? $order['openid'] : $this->auth['openid']
            ],
            'attach' => $from
        ]);

        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $this->getPrepayConfig($result['prepay_id'], $appId);
    }

    /**
     * @notes 网站native
     * @param $from
     * @param $order
     * @param $appId
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 12:12
     */
    public function nativePay($from, $order, $appId): mixed
    {
        $response = $this->app->getClient()->postJson('v3/pay/transactions/native', [
            'appid'        => $appId,
            'mchid'        => $this->config['mch_id'],
            'description'  => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url'   => $this->config['notify_url'],
            'amount' => [
                'total' => intval($order['order_amount'] * 100),
            ],
            'attach' => $from
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result['code_url'];
    }

    /**
     * @notes appPay
     * @param $from
     * @param $order
     * @param $appId
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 12:12
     */
    public function appPay($from, $order, $appId): mixed
    {
        $response = $this->app->getClient()->postJson('v3/pay/transactions/app', [
            'appid'        => $appId,
            'mchid'        => $this->config['mch_id'],
            'description'  => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url'   => $this->config['notify_url'],
            'amount' => [
                'total' => intval($order['order_amount'] * 100),
            ],
            'attach' => $from
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result['prepay_id'];
    }

    /**
     * @notes h5
     * @param $from
     * @param $order
     * @param $appId
     * @return string
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 12:13
     */
    public function mwebPay($from, $order, $appId): string
    {
        $ip = request()->ip();
        if (!empty(env('project.test_web_ip')) && env('APP_DEBUG')) {
            $ip = env('project.test_web_ip');
        }

        $response = $this->app->getClient()->postJson('v3/pay/transactions/h5', [
            'appid'        => $appId,
            'mchid'        => $this->config['mch_id'],
            'description'  => $this->payDesc($from),
            'out_trade_no' => $order['pay_sn'],
            'notify_url' => $this->config['notify_url'],
            'amount' => [
                'total' => intval($order['order_amount'] * 100),
            ],
            'attach' => $from,
            'scene_info' => [
                'payer_client_ip' => $ip,
                'h5_info' => [
                    'type' => 'Wap',
                ]
            ]
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);

        $domain = request()->domain();
        if (!empty(env('project.test_web_domain')) && env('APP_DEBUG')) {
            $domain = env('project.test_web_domain');
        }
        $redirectUrl = $domain . '/'. $order['redirect_url'] .'?id=' . $order['id'] . '&from='. $from . '&checkPay=true';
        return $result['h5_url'] . '&redirect_url=' . urlencode($redirectUrl);
    }

    /**
     * @notes 退款
     * @param array $refundData
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 16:53
     */
    public function refund(array $refundData): mixed
    {
        $response =  $this->app->getClient()->postJson('v3/refund/domestic/refunds', [
            'transaction_id' => $refundData['transaction_id'],
            'out_refund_no' => $refundData['refund_sn'],
            'amount' => [
                'refund' => intval($refundData['refund_amount'] * 100),
                'total' => intval($refundData['total_amount'] * 100),
                'currency' => 'CNY',
            ]
        ]);
        $result = $response->toArray(false);
        $this->checkResultFail($result);
        return $result;
    }

    /**
     * @notes 查询退款
     * @param $refundSn
     * @return mixed
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @author 段誉
     * @date 2023/3/1 11:16
     */
    public function queryRefund($refundSn): mixed
    {
        $response = $this->app->getClient()->get("v3/refund/domestic/refunds/{$refundSn}");
        return $response->toArray(false);
    }

    /**
     * @notes 支付描述
     * @param $from
     * @return string
     * @author 段誉
     * @date 2023/2/27 17:54
     */
    public function payDesc($from): string
    {
        $desc = [
            'order'    => '商品',
            'recharge' => '充值',
        ];
        return $desc[$from] ?? '商品';
    }

    /**
     * @notes 捕获错误
     * @param $result
     * @throws Exception
     * @author 段誉
     * @date 2023/2/28 12:09
     */
    public function checkResultFail($result)
    {
        if (!empty($result['code']) || !empty($result['message'])) {
            throw new Exception('微信:'. $result['code'] . '-' . $result['message']);
        }
    }

    /**
     * @notes 预支付配置
     * @param $prepayId
     * @param $appId
     * @return array
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     * @author 段誉
     * @date 2023/2/28 17:38
     */
    public function getPrepayConfig($prepayId, $appId): array
    {
        return $this->app->getUtils()->buildBridgeConfig($prepayId, $appId)??[];
    }

    /**
     * @notes 支付回调
     * @return ResponseInterface
     * @throws @InvalidArgumentException
     * @throws @\EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws @\ReflectionException
     * @throws @\Throwable
     * @author 段誉
     * @date 2023/2/28 14:20
     */
    public function notify(): ResponseInterface
    {
        $server = $this->app->getServer();
        $server->handlePaid(function (Message $message) {
            if ($message['trade_state'] === 'SUCCESS') {
                $extra['transaction_id'] = $message['transaction_id'];
                $attach = $message['attach'];
                $message['out_trade_no'] = mb_substr($message['out_trade_no'], 0, 18);
                switch ($attach) {
                    case 'recharge':
                        $order = (new RechargeOrder())->where(['order_sn' => $message['out_trade_no']])->findOrEmpty();
                        if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                            return true;
                        }
                        PayNotifyLogic::handle('recharge', $message['out_trade_no'], $extra);
                        break;
                    case 'member':
                        $order = MemberOrder::where(['order_sn' => $message['out_trade_no']])->findOrEmpty();
                        if($order->isEmpty() || $order->pay_status == PayEnum::ISPAID) {
                            return true;
                        }
                        PayNotifyLogic::handle('member', $message['out_trade_no'], $extra);
                        break;
                }
            }
            return true;
        });

        // 退款通知
        $server->handleRefunded(function (Message $message) {
            return true;
        });

        return $server->serve();
    }
}