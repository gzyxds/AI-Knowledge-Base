<?php

namespace app\common\model\search;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * Ai搜索记录模型
 */
class AiSearchRecord extends BaseModel
{
    use SoftDelete;

    protected string $deleteTime = 'delete_time';
}