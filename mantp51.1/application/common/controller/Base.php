<?php
namespace app\common\controller;
use think\Controller;
use think\facade\Session;

class Base extends Controller
{
    //检查是否登陆
    public function islogin()
    {
        if(!Session::has('admin_info.id'))
        {
            $this->redirect('login');
        }
    }
    //----退出登陆-----退出登陆------退出登陆
    public function outlogin()
    {
        Session::delete('admin_info');
        $this->error('退出成功','login');
    }
    //你已经登陆了
    public function onlogin()
    {
        if(Session::has('admin_info.id'))
        {
            $this->redirect('index');
        }
    }
}