<?php

namespace app\adminapi\logic\search;

use app\common\enum\SearchEnum;
use app\common\logic\BaseLogic;
use app\common\model\search\AiSearchRecord;
use Exception;

class RecordLogic extends BaseLogic
{
    /**
     * @notes 搜索记录详情
     * @param int $id
     * @return array
     * @author fzr
     */
    public static function detail(int $id): array
    {
        $detail = (new AiSearchRecord())
            ->withoutField('user_id,markdown,update_time,delete_time')
            ->where(['id'=>$id])
            ->findOrEmpty()
            ->toArray();

        if (!$detail) {
            return [];
        }

        $detail['model'] = SearchEnum::getModelDesc($detail['model']);
        $detail['type'] = SearchEnum::getTypeDesc($detail['type']);
        $detail['context'] = json_decode($detail['context']??'[]', true);
        $detail['results'] = json_decode($detail['results']??'[]', true);
        $detail['price'] = format_amount_zero($detail['price']);
        return $detail;
    }

    /**
     * @notes 搜索记录删除
     * @param $ids
     * @return bool
     * @author fzr
     */
    public static function del($ids): bool
    {
        try {
            if (!$ids) {
                throw new Exception('请选择要删除的数据');
            }

            AiSearchRecord::destroy($ids);
            return true;
        } catch (Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}