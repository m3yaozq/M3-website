<?php
namespace app\common\validate;
use think\Validate;

class User extends Validate
{
    protected $rule = [
        'email|邮箱'=>'require|email|unique:m3user',
        'mobile|手机号'=>'require|mobile|unique:m3user',
        'password|密码'=>'require|length:6,20|alphaNum|confirm',
    ];
}