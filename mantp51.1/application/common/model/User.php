<?php
namespace app\common\model;
use think\Model;

class User extends Model
{
    protected $pk = 'id';
    protected $table = 'm3user';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $dateFormat = 'Y年m月d日 H:i:s';
    public function getStatusAttr($value)
    {
        $status = ['1'=> '启用','0'=>'已禁用'];
        return $status[$value];
    }

    public function getIsAdminAttr($value)
    {
        $status = ['0'=> '普通会员','1'=> '管理员','2'=> '超级管理员'];
        return $status[$value];
    }
    public function setPasswordAttr($value)
    {

        return sha1($value);
    }

}