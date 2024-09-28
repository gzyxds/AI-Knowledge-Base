<?php
namespace app\common\model\square;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class SquareCategory extends BaseModel
{

    use SoftDelete;

    protected string $deleteTime = 'delete_time';

}
