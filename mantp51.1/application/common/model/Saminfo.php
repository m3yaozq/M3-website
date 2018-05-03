<?php
namespace app\common\model;
use think\Model;

class Saminfo extends Model
{
    protected $pk = 'id';
    protected $table = 'sample';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y年m月d日 H:i:s';
}