<?php

namespace app\adminapi\logic\draw;

use app\common\enum\draw\DrawEnum;
use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\draw\DrawLora;
use app\common\model\draw\DrawModelLoraRelation;
use app\common\service\ConfigService;
use app\common\service\draw\engine\DrawSd;
use app\common\service\FileService;

class DrawLoraLogic extends BaseLogic
{
    /**
     * @notes 微调模型详情
     * @param int $id
     * @return array
     * @author JXDN
     * @date 2024/5/15 16:29
     */
    public static function detail(int $id): array
    {
        return (new DrawLora())
            ->withoutField('delete_time,update_time')
            ->withAttr(['cover' => function ($value) {
                return FileService::getFileUrl($value);
            }])
            ->findOrEmpty($id)
            ->toArray();
    }

    /**
     * @notes 微调模型新增
     * @param array $post
     * @return bool
     * @author JXDN
     * @date 2024/5/15 16:29
     */
    public static function add(array $post): bool
    {
        try {
            DrawLora::create([
                'model_name'  => $post['model_name'],
                'title'       => $post['title'] ?? $post['model_name'],
                'cover'       => FileService::setFileUrl($post['cover'] ?? ''),
                'sort'        => $post['sort'] ?? 0,
                'status'      => $post['status'] ?? 0,
                'create_time' => time(),
                'update_time' => time()
            ]);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 微调模型编辑
     * @param array $post
     * @return bool
     * @author JXDN
     * @date 2024/5/15 16:29
     */
    public static function edit(array $post): bool
    {
        try {
            DrawLora::update([
                'model_name'  => $post['model_name'],
                'title'       => $post['title'],
                'cover'       => FileService::setFileUrl($post['cover'] ?? ''),
                'sort'        => $post['sort'] ?? 0,
                'status'      => $post['status'] ?? 0,
                'update_time' => time()
            ], ['id' => intval($post['id'])]);

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 微调模型删除
     * @param int $id
     * @return bool
     * @author JXDN
     * @date 2024/5/15 16:29
     */
    public static function del(int $id): bool
    {
        try {
            DrawModelLoraRelation::where(['lora_id' => $id])->delete();
            DrawLora::destroy($id);
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 修改示例状态
     * @param int $id
     * @return bool
     * @author JXDN
     * @date 2024/5/15 16:29
     */
    public static function status(int $id): bool
    {
        try {
            $questionCategory = (new DrawLora())->where(['id' => $id])->findOrEmpty();
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
     * @author mjf
     * @date 2024/1/4 15:39
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
                            'name'  => $model['name'],
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

    public static function all(): array
    {
        return (new DrawLora())
            ->withoutField(["delete_time", "update_time"])
            ->where(['status' => YesNoEnum::YES])
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->withAttr(['cover' => function ($value) {
                return FileService::getFileUrl($value);
            }])
            ->select()
            ->toArray();
    }
}