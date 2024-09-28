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
use app\common\model\chat\Models;
use app\common\model\chat\ModelsCost;
use app\common\service\ConfigService;
use app\common\service\FileService;
use Exception;

/**
 * AI模型配置
 */
class AiModelsLogic extends BaseLogic
{
    /**
     * @notes 模型通道
     * @return array
     */
    public static function channel(): array
    {
        $chatModels    = config('ai.ChatModels');
        $vectorModels  = config('ai.VectorModels');
        $exampleModels = config('ai.ExampleModels');

        foreach ($chatModels as &$item) {
            $item['logo'] = FileService::getFileUrl($item['logo']);
        }

        foreach ($vectorModels as &$item) {
            $item['logo'] = FileService::getFileUrl($item['logo']);
        }

        return [
            'chatModels'    => $chatModels,
            'vectorModels'  => $vectorModels,
            'exampleModels' => $exampleModels
        ];
    }

    /**
     * @notes 模型列表
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public static function lists(): array
    {
        $chatModels = (new Models())
            ->field(['id,type,channel,logo,name,is_system,is_enable'])
            ->where(['type'=>ChatEnum::MODEL_TYPE_CHAT])
            ->order('sort asc, id desc')
            ->select()
            ->toArray();

        $vectorModels = (new Models())
            ->field(['id,type,channel,logo,name,is_enable'])
            ->where(['type'=>ChatEnum::MODEL_TYPE_EMB])
            ->order('sort asc, id desc')
            ->select()
            ->toArray();

        foreach ($chatModels as &$item) {
            $item['logo'] = FileService::getFileUrl($item['logo']);
        }

        foreach ($vectorModels as &$item) {
            $item['logo'] = FileService::getFileUrl($item['logo']);
        }

        return ['chatModels'=>$chatModels, 'vectorModels'=>$vectorModels];
    }

    /**
     * @notes 模型详情
     * @param int $id
     * @return array
     * @throws @\think\db\exception\DataNotFoundException
     * @throws @\think\db\exception\DbException
     * @throws @\think\db\exception\ModelNotFoundException
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $model = new Models();
        $detail = $model->withoutField(['delete_time'])->where(['id'=>$id])->findOrEmpty()->toArray();
        if (!$detail) {
            return [];
        }

        $keys = [];
        $configs = [];
        $setting = json_decode($detail['configs'], true);
        foreach ($setting as $k => $v) {
            $keys[] = $k;
            if (is_numeric($v)) {
                $configs[$k] = floatval($v);
            } else {
                $configs[$k] = $v;
            }
        }

        if ($detail['type'] == ChatEnum::MODEL_TYPE_CHAT) {
            $cacheConfigs = config('ai.ChatModels')[$detail['channel']]['configs'][0]['config'];
            foreach ($cacheConfigs as $item) {
                if (!in_array($item['key'], $keys)) {
                    $configs[$item['key']] = $item['default'];
                }
            }
        }

        $modelCost = new ModelsCost();
        $subModels = $modelCost
            ->field(['id,name,alias,price,sort,status'])
            ->where(['model_id'=>$detail['id']])
            ->order('sort asc, id desc')
            ->select()
            ->toArray();

        foreach ($subModels as &$item) {
            $item['price'] = format_amount_zero($item['price']);
        }

        $detail['logo'] = FileService::getFileUrl($detail['logo']);
        $detail['configs'] = $configs;
        $detail['models'] = $subModels;
        return $detail;
    }

    /**
     * @notes 模型创建
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function add(array $post): bool
    {
        $model = new Models();
        $model->startTrans();
        try {
            $configs = [];
            $channelName = intval($post['type']) === ChatEnum::MODEL_TYPE_EMB ? 'ai.VectorModels' : 'ai.ChatModels';
            $setting = config($channelName)[$post['channel']]['configs'];
            foreach ($setting as $conf) {
                if (!empty($conf['config'])) {
                    foreach ($conf['config'] as $item) {
                        $key = $item['key'];
                        $configs[$key] = empty($post['configs'][$key]) ? $item['default'] : $post['configs'][$key];
                    }
                } else {
                    $key = $conf['key'];
                    $configs[$key] = empty($post['configs'][$key]) ? $conf['default'] : $post['configs'][$key];
                }
            }

            $mainModel = Models::create([
                'type'      => $post['type'],
                'channel'   => $post['channel'],
                'name'      => $post['name'],
                'logo'      => FileService::setFileUrl($post['logo']),
                'is_enable' => intval($post['is_enable']??0),
                'configs'   => json_encode($configs, JSON_UNESCAPED_UNICODE)
            ]);

            if (ChatEnum::MODEL_TYPE_CHAT) {
                foreach ($post['models'] as $item) {
                    ModelsCost::create([
                        'model_id' => $mainModel['id'],
                        'type'     => $post['type'],
                        'channel'  => $post['channel'],
                        'name'     => $item['name'],
                        'alias'    => empty($item['alias']) ? $item['name'] : $item['alias'],
                        'price'    => $item['price']??0,
                        'status'   => intval($item['status']??0),
                        'sort'     => intval($item['sort']??0)
                    ]);
                }
            } else if (ChatEnum::MODEL_TYPE_EMB) {
                $postM = $post['models'][0];
                $emStatus = ($post['is_enable']??0) ? 1 : 0;
                ModelsCost::create([
                    'model_id' => $mainModel['id'],
                    'type'     => intval($post['type']),
                    'channel'  => $post['channel'],
                    'name'     => $postM['name'],
                    'alias'    => empty($postM['alias']) ? $postM['name'] : $postM['alias'],
                    'price'    => $item['price']??0,
                    'status'   => $emStatus,
                    'sort'     => intval($item['sort']??0)
                ]);
            }

            // 更新默认的模型
            $model->where(['type'=>$post['type']])->update(['is_default'=>0]);
            $model->where(['type'=>$post['type']])
                ->where(['is_enable'=>1])
                ->order('sort asc, id desc')
                ->update(['is_default'=>1]);

            $model->commit();
            return true;
        } catch (Exception $e) {
            $model->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 模型编辑
     * @param array $post
     * @return bool
     * @author fzr
     */
    public static function edit(array $post): bool
    {
        $model = new Models();
        $model->startTrans();
        try {
            $mainModel = $model->where(['id'=>intval($post['id'])])->findOrEmpty()->toArray();
            if (!$mainModel) {
                throw new Exception('模型已不存在了!');
            }

            $configs = [];
            $channelName = $mainModel['type'] === ChatEnum::MODEL_TYPE_EMB ? 'ai.VectorModels' : 'ai.ChatModels';
            $setting = config($channelName)[$mainModel['channel']]['configs'];

            foreach ($setting as $conf) {
                if (!empty($conf['config'])) {
                    foreach ($conf['config'] as $item) {
                        $key = trim($item['key']);
                        $configs[$key] = !isset($post['configs'][$key]) ? $item['default'] : $post['configs'][$key];
                        if ($item['type'] == 'switch') {
                            $configs[$key] = (bool)$configs[$key];
                        }
                    }
                } else {
                    $key = $conf['key'];
                    $configs[$key] = !isset($post['configs'][$key]) ? $conf['default'] : $post['configs'][$key];
                }
            }

            Models::update([
                'channel'   => $post['channel'],
                'remarks'   => $post['remarks']??'',
                'name'      => $post['name'],
                'logo'      => FileService::setFileUrl($post['logo']),
                'is_enable' => intval($post['is_enable']??0),
                'configs'   => json_encode($configs, JSON_UNESCAPED_UNICODE)
            ], ['id'=>intval($post['id'])]);

            // 向量模型处理逻辑
            if ($mainModel['type'] == ChatEnum::MODEL_TYPE_EMB) {
                $postM    = $post['models'][0];
                $emStatus = ($post['is_enable']??0) ? 1 : 0;
                if (empty($postM['id'])) {
                    ModelsCost::create([
                        'type'     => $mainModel['type'],
                        'model_id' => intval($post['id']),
                        'name'     => $postM['name'],
                        'alias'    => empty($postM['alias']) ? $postM['name'] : $postM['alias'],
                        'price'    => $postM['price'] ?? 0,
                        'status'   => $emStatus,
                        'sort'     => intval($item['sort'] ?? 0)
                    ]);
                } else {
                    ModelsCost::update([
                        'name'   => $postM['name'],
                        'alias'  => empty($postM['alias']) ? $postM['name'] : $postM['alias'],
                        'price'  => $postM['price'] ?? 0,
                        'status' => $emStatus,
                        'sort'   => intval($item['sort'] ?? 0)
                    ], ['id' => $postM['id'], 'model_id' => intval($post['id'])]);
                }
            }

            // 对话模型的处理逻辑
            if ($mainModel['type'] == ChatEnum::MODEL_TYPE_CHAT) {
                $insertData = [];
                $updateData = [];
                $deleteData = [];

                // 找出更新新增
                foreach ($post['models'] as $item) {
                    if (empty($item['id']) || $item['id'] == 0) {
                        $insertData[] = $item;
                    } elseif ($item['id'] > 0) {
                        $updateData[] = $item;
                    }
                }

                // 找出要删除的
                $mysqlModels = (new ModelsCost())->where(['model_id' => intval($post['id'])])->select()->toArray();
                $postModelIds = array_unique(array_column($post['models'], 'id'));
                foreach ($mysqlModels as $item) {
                    if (!in_array($item['id'], $postModelIds)) {
                        $deleteData[] = $item['id'];
                    }
                }

                if ($insertData) {
                    foreach ($insertData as $item) {
                        ModelsCost::create([
                            'model_id' => intval($post['id']),
                            'type' => $post['type'],
                            'channel' => $post['channel'],
                            'name' => $item['name'],
                            'alias' => empty($item['alias']) ? $item['name'] : $item['alias'],
                            'price' => $item['price'] ?? 0,
                            'status' => intval($item['status'] ?? 0),
                            'sort' => intval($item['sort'] ?? 0),
                        ]);
                    }
                }

                if ($updateData) {
                    foreach ($updateData as $item) {
                        ModelsCost::update([
                            'name'   => $item['name'],
                            'alias'  => empty($item['alias']) ? $item['name'] : $item['alias'],
                            'price'  => $item['price'] ?? 0,
                            'status' => intval($item['status'] ?? 0),
                            'sort'   => intval($item['sort'] ?? 0)
                        ], ['id' => $item['id'], 'model_id' => intval($post['id'])]);
                    }
                }

                if ($deleteData) {
                    (new ModelsCost())
                        ->whereIn('id', $deleteData)
                        ->where(['model_id' => intval($post['id'])])
                        ->delete();
                }
            }

            // 更新默认的模型
            $model->where(['type'=>$mainModel['type']])->update(['is_default'=>0]);
            $model->where(['type'=>$mainModel['type']])
                ->where(['is_enable'=>1])
                ->order('sort asc, id desc')
                ->update(['is_default'=>1]);

            $model->commit();
            return true;
        } catch (Exception $e) {
            $model->rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 模型删除
     * @param int $id
     * @return bool
     * @author fzr
     */
    public static function del(int $id): bool
    {
        try {
            $model = new Models();
            $detail = $model->where(['id'=>$id])->findOrEmpty()->toArray();

            if (!$detail) {
                throw new Exception('模型已不存在!');
            }

            Models::destroy($id);

            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 模型排序
     * @param array $post
     * @return bool
     */
    public static function sort(array $post): bool
    {
        try {
            foreach ($post['orders'] as $item) {
                Models::update([
                    'sort' => $item['sort']
                ], ['id'=>intval($item['id'])]);
            }

            if (!empty($post['orders'][0]['id'])) {
                $model = new Models();
                $mainModel = $model->where(['id' => $post['orders'][0]['id']])->findOrEmpty()->toArray();
       
                $model->where(['type' => $mainModel['type']])->update(['is_default' => 0]);
                $model->where(['type' => $mainModel['type']])
                    ->where(['is_enable' => 1])
                    ->order('sort asc, id desc')
                    ->update(['is_default' => 1]);
            }
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}