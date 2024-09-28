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

namespace app\adminapi\logic\setting\ai;

use app\common\enum\ChatEnum;
use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;

/**
 * AI对话配置逻辑类
 */
class AiChatLogic extends BaseLogic
{
    /**
     * @notes 对话配置详情
     * @return array
     * @author fzr
     */
    public static function detail(): array
    {
        $chatLogo    = ConfigService::get('default_image','chat_logo');
        $chatExample = ConfigService::get('default_image','chat_example');
        $modelExample = ConfigService::get('default_image','model_example');
        $config      = ConfigService::get('chat');
        return [
            'chat_logo'         => FileService::getFileUrl($config['chat_logo']??$chatLogo),
            'chat_default'      => FileService::getFileUrl($chatLogo),
            'chat_example'      => FileService::getFileUrl($chatExample),
            'model_example'      => FileService::getFileUrl($modelExample),
            'global_directives' => $config['global_directives']??'',
            'price_unit'        => $config['price_unit']??'',

            'is_reopen'         => $config['is_reopen'] ?? 0,            // 是否重开对话
            'default_reply_open'=> $config['default_reply_open'] ?? 0,   // 对话默认回复开关
            'default_reply'     => $config['default_reply'] ?? '',       // 对话默认回复内容
            'is_show_model'     => $config['is_show_model'] ?? 0,        // 对话模型
            'chat_title'        => $config['chat_title'] ?? '',          // 对话标题
            'global_directives_model' => ChatEnum::getAiModelName(),     // 对话模型
            'watermark'  => $config['watermark'] ?? '内容由AI生成仅供参考', // 水印

            'min_consume_status' => $config['min_consume_status']  ?? 0,  // 最低消费状态
            'min_consume_price'  => $config['min_consume_price']   ?? '', // 最低消费金额
            'min_consume_tips'   => $config['min_consume_tips']    ?? '', // 最低消费提示

            'related_issues_num' => $config['related_issues_num'] ?? 0,  // 相关问题数量
        ]??[];
    }

    /**
     * @notes 对话配置保存
     * @param array $post
     * @author fzr
     */
    public static function save(array $post): void
    {
        ConfigService::set('chat','chat_logo', FileService::setFileUrl($post['chat_logo']??''));
        ConfigService::set('chat','global_directives', $post['global_directives']??'');
        ConfigService::set('chat','price_unit', $post['price_unit']??'');

        ConfigService::set('chat','is_reopen', $post['is_reopen']??0);
        ConfigService::set('chat','default_reply_open', $post['default_reply_open']??0);
        ConfigService::set('chat','default_reply', $post['default_reply']??'');
        ConfigService::set('chat','chat_title', $post['chat_title']??'');
        ConfigService::set('chat','watermark', $post['watermark']??'');
        ConfigService::set('chat','is_show_model',$post['is_show_model']??0);

        ConfigService::set('chat','min_consume_status',$post['min_consume_status']??0);
        ConfigService::set('chat','min_consume_price',$post['min_consume_price']??'');
        ConfigService::set('chat','min_consume_tips',$post['min_consume_tips']??'');

        ConfigService::set('chat','related_issues_num',$post['related_issues_num']??0);
    }
}