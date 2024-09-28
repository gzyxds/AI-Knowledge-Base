<?php

namespace app\common\command;

use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use Exception;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class UpdateData extends Command
{
    protected function configure(): void
    {
        $this->setName('update_data')
            ->setDescription('模型数据更新');
    }

    protected function execute(Input $input, Output $output): bool
    {
        $mainModel = new Models();
        $mainModel->startTrans();
        try {
            $chatModels = [
                [
                    'type'    => 1,
                    'channel' => 'openai',
                    'name'    => 'Kimi',
                    'logo'    => 'resource/image/models/kimi.png',
                    'configs' => '{"context_num":"3","temperature":"1","presence_penalty":"0","frequency_penalty":"0","check_key":true,"agency_api":"https:\/\/api.moonshot.cn","global_directives":""}',
                    'sort'    => 14,
                    'children' => [
                        ['channel'=>'openai', 'name'=>'moonshot-v1-8k', 'alias'=>'moonshot-v1-8k'],
                        ['channel'=>'openai', 'name'=>'moonshot-v1-32k', 'alias'=>'moonshot-v1-32k'],
                        ['channel'=>'openai', 'name'=>'moonshot-v1-128k', 'alias'=>'moonshot-v1-128k']
                    ]
                ],
                [
                    'type'    => 1,
                    'channel' => 'minimax',
                    'name'    => 'MiniMax',
                    'logo'    => 'resource/image/models/minimax.png',
                    'configs' => '{"context_num":"3","temperature":"1","check_key":true,"agency_api":"","global_directives":""}',
                    'sort'    => 15,
                    'children' => [
                        ['channel'=>'minimax', 'name'=>'abab5.5-chat', 'alias'=>'abab5.5-chat'],
                    ]
                ],
                [
                    'type'    => 1,
                    'channel' => 'baichuan',
                    'name'    => '百川智能',
                    'logo'    => 'resource/image/models/baichuan.png',
                    'configs' => '{"context_num":"3","temperature":"1","presence_penalty":"1.1","frequency_penalty":"1","check_key":true,"agency_api":"https:\/\/api.baichuan-ai.com","global_directives":""}',
                    'sort'    => 16,
                    'children' => [
                        ['channel'=>'baichuan', 'name'=>'Baichuan4', 'alias'=>'Baichuan4'],
                        ['channel'=>'baichuan', 'name'=>'Baichuan2-Turbo', 'alias'=>'Baichuan2-Turbo'],
                        ['channel'=>'baichuan', 'name'=>'Baichuan3-Turbo', 'alias'=>'Baichuan3-Turbo'],
                        ['channel'=>'baichuan', 'name'=>'Baichuan2-Turbo-192k', 'alias'=>'Baichuan2-Turbo-192k'],
                        ['channel'=>'baichuan', 'name'=>'Baichuan3-Turbo-128k', 'alias'=>'Baichuan3-Turbo-128k']
                    ]
                ]
            ];

            foreach ($chatModels as $item) {
                $main = Models::create([
                    'type'       => $item['type'],
                    'channel'    => $item['channel'],
                    'logo'       => $item['logo'],
                    'name'       => $item['name'],
                    'configs'    => $item['configs'],
                    'sort'       => $item['sort'],
                    'is_enable'  => 0,
                    'is_system'  => 0,
                    'is_default' => 0
                ]);
                foreach ($item['children'] as $sub) {
                    ModelsCost::create([
                        'type'     => $item['type'],
                        'model_id' => $main['id'],
                        'channel'  => $sub['channel'],
                        'name'     => $sub['name'],
                        'alias'    => $sub['alias'],
                        'price'    => 0,
                        'sort'     => 0,
                        'status'   => 1
                    ]);
                }
            }

            $version = config('project.version');

            $mainModel->commit();
            echo "\n\n--------------------\n";
            echo "Successful ~ 当前版本为: $version \n";
            echo "--------------------\n";
            echo "恭喜您,脚本已执行成功, 不要再次重复执行哦~\n\n";
            return true;
        } catch (Exception $e) {
            echo "\n\n脚本执行失败了: " . $e->getMessage() . "\n\n";
            $mainModel->rollback();
            return false;
        }
    }
}