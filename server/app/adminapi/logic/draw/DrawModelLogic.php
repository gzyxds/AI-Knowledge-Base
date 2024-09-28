<?php

namespace app\adminapi\logic\draw;

use app\common\enum\draw\DrawEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawLora;
use app\common\model\draw\DrawModel;
use app\common\model\draw\DrawModelLoraRelation;
use app\common\service\ConfigService;
use app\common\service\draw\engine\DrawSd;
use app\common\service\FileService;

class DrawModelLogic extends BaseLogic
{
    /**
     * @notes 主要模型详情
     * @param int $id
     * @return array
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function detail(int $id): array
    {
        return (new DrawModel())
            ->withoutField('delete_time,update_time')
            ->with(['loras' => function ($query) {
                $query->getQuery()->hidden(['delete_time', 'update_time', 'pivot']);
            }])
            ->withAttr(['cover' => function ($value) {
                return FileService::getFileUrl($value);
            }])
            ->findOrEmpty($id)
            ->toArray();
    }

    /**
     * @notes 主要模型新增
     * @param array $post
     * @return bool
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function add(array $post): bool
    {
        try {
            $result = DrawModel::create([
                'model_name' => $post['model_name'],
                'title' => $post['title'] ?? $post['model_name'],
                'cover' => FileService::setFileUrl($post['cover'] ?? ''),
                'category_id' => $post['category_id'],
                'sort' => $post['sort'] ?? 0,
                'status' => $post['status'] ?? 0,
                'create_time' => time(),
                'update_time' => time()
            ]);

            foreach ($post['loras'] as $lora_id) {
                DrawModelLoraRelation::create([
                    'model_id' => $result['id'],
                    'lora_id' => $lora_id,
                ]);
            }

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 主要模型编辑
     * @param array $post
     * @return bool
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function edit(array $post): bool
    {
        try {
            DrawModelLoraRelation::where(['model_id' => $post['id']])->delete();

            foreach ($post['loras'] as $lora_id) {
                DrawModelLoraRelation::create([
                    'model_id' => $post['id'],
                    'lora_id' => $lora_id,
                ]);
            }

            DrawModel::update([
                'model_name' => $post['model_name'],
                'title' => $post['title'] ?? $post['model_name'],
                'cover' => FileService::setFileUrl($post['cover'] ?? ''),
                'category_id' => $post['category_id'],
                'sort' => $post['sort'] ?? 0,
                'status' => $post['status'] ?? 0,
                'update_time' => time()
            ], ['id' => intval($post['id'])]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 主要模型删除
     * @param int | array $id
     * @return bool
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function delete(array | int $id): bool
    {
        try {
            DrawModelLoraRelation::where(['model_id' => $id])->delete();
            DrawModel::destroy($id);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改模型状态
     * @param int $id
     * @return bool
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function status(int $id): bool
    {
        try {
            $questionCategory = (new DrawModel())->where(['id' => $id])->findOrEmpty();
            if ($questionCategory->isEmpty()) {
                return true;
            }

            $questionCategory->status = $questionCategory->status ? 0 : 1;
            $questionCategory->save();
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes sd模型
     * @return array
     * @author JXDN
     * @date 2024/05/17 14:45
     */
    public static function getSdModel(): array
    {
        try {
            $apiConfig = ConfigService::get('draw_config', DrawEnum::API_SD, []);
            if (empty($apiConfig['proxy_url'])) {
                return [];
            } else {
                $engine = new DrawSd($apiConfig['proxy_url']);
                $models = $engine->getModel();

                $data = [];
                if (!empty($models)) {
                    foreach ($models as $model) {
                        $data[] = [
                            'title' => $model['title'],
                            'model_name' => $model['model_name'],
                        ];
                    }
                }
                return $data;
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @notes 获取微调模型
     * @return array
     * @author JXDN
     * @date 2024/05/22 14:46
     */
    public static function getSdLora(): array
    {
        try {
            $apiConfig = ConfigService::get('draw_config', DrawEnum::API_SD, []);
            if (empty($apiConfig['proxy_url'])) {
                return [];
            } else {
                $engine = new DrawSd($apiConfig['proxy_url']);
                // return $engine->getLoras();
                $models = $engine->getLoras();

                $data = [];
                if (!empty($models)) {
                    foreach ($models as $model) {
                        $data[] = [
                            'name' => $model['name'],
                            'alias' => $model['alias'],
                        ];
                    }
                }
                return $data;
            }
        } catch (\Exception $e) {
            return [];
        }
    }
}