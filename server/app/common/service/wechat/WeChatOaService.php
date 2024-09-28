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
namespace app\common\service\wechat;

use EasyWeChat\Kernel\Contracts\Server;
use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Exceptions\Exception;
use EasyWeChat\Kernel\Exceptions\HttpException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Form\File;
use EasyWeChat\Kernel\Form\Form;
use EasyWeChat\Kernel\HttpClient\Response;
use EasyWeChat\OfficialAccount\Application;
use ReflectionException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Throwable;

/**
 * 公众号相关
 * Class WeChatOaService
 * @package app\common\service\wechat
 */
class WeChatOaService
{

    protected $app;

    protected $config;


    public function __construct()
    {
        $this->config = $this->getConfig();
        $this->app = new Application($this->config);
    }

    /**
     * @notes easywechat服务端
     * @return Server|\EasyWeChat\OfficialAccount\Server
     * @throws InvalidArgumentException
     * @throws ReflectionException
     * @throws Throwable
     * @author 段誉
     * @date 2023/2/27 14:22
     */
    public function getServer(): \EasyWeChat\OfficialAccount\Server|Server
    {
        return $this->app->getServer();
    }

    /**
     * @notes 配置
     * @return array
     * @throws \Exception
     * @author 段誉
     * @date 2023/2/27 12:03
     */
    protected function getConfig(): array
    {
        $config = WeChatConfigService::getOaConfig();
        if (empty($config['app_id']) || empty($config['secret'])) {
            throw new \Exception('请先设置公众号配置');
        }
        return $config;
    }

    /**
     * @notes 公众号-根据code获取微信信息
     * @param string $code
     * @return array
     * @throws Exception
     * @throws InvalidArgumentException
     * @author 段誉
     * @date 2023/2/27 11:04
     */
    public function getOaResByCode(string $code): array
    {
        $response = $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->userFromCode($code)
            ->getRaw();

        if (!isset($response['openid']) || empty($response['openid'])) {
            throw new Exception('获取openID失败');
        }

        return $response;
    }

    /**
     * @notes 公众号跳转url
     * @param string $url
     * @return string
     * @throws InvalidArgumentException
     * @author 段誉
     * @date 2023/2/27 10:35
     */
    public function getCodeUrl(string $url): string
    {
        return $this->app->getOAuth()
            ->scopes(['snsapi_userinfo'])
            ->redirect($url);
    }

    /**
     * @notes 创建公众号菜单
     * @param array $buttons
     * @param array $matchRule
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     * @author 段誉
     * @date 2023/2/27 12:07
     */
    public function createMenu(array $buttons, array $matchRule = [])
    {
        if (!empty($matchRule)) {
            return $this->app->getClient()->postJson('cgi-bin/menu/addconditional', [
                'button' => $buttons,
                'matchrule' => $matchRule,
            ]);
        }

        return $this->app->getClient()->postJson('cgi-bin/menu/create', ['button' => $buttons]);
    }

    /**
     * @notes 获取jssdkConfig
     * @param $url
     * @param $jsApiList
     * @param array $openTagList
     * @param false $debug
     * @return array
     * @throws HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @author 段誉
     * @date 2023/3/1 11:46
     */
    public function getJsConfig($url, $jsApiList, array $openTagList = [], bool $debug = false): array
    {
        return $this->app->getUtils()->buildJsSdkConfig($url, $jsApiList, $openTagList, $debug);
    }

    /**
     * @notes 上传图片
     * @param $path
     * @return array|Response|ResponseInterface
     * @throws BadResponseException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @author ljj
     * @date 2023/6/14 9:41 上午
     */
    public function uploadImage($path): array|ResponseInterface|Response
    {
        $options = Form::create(
            [
                'media' => File::fromPath($path),
            ]
        )->toArray();

        return $this->app->getClient()->post('cgi-bin/media/upload?type=image', $options)->toArray();
    }

    /**
     * @notes 发送客服消息
     * @param $data
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     * @author ljj
     * @date 2023/6/14 10:00 上午
     */
    public function customSend($data): ResponseInterface|Response
    {
        return $this->app->getClient()->postJson('/cgi-bin/message/custom/send', $data);
    }
}