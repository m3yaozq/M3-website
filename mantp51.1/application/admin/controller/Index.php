<?php
namespace app\admin\controller;
use app\common\controller\Base;
use think\facade\Request;
use app\common\model\Saminfo;
use app\common\model\User as UserModel;
use think\facade\Session;

class Index extends Base
{
    public function ccf()
    {
      return $this->fetch();
    }
    ////-----------主页面--------主页面--------主页面--------
    public function index()
    {
        $this->islogin();
        $this->assign('title','漫瑞管理系统');
        return $this->fetch();
    }
    //------------登陆界面----登陆界面----登陆界面---登陆界面
    public function login()
    {
        $this->onlogin();
        $this->assign('title','漫瑞登陆界面');
        return $this->fetch();
    }
    //-----------认证登陆-----认证登陆-----认证登陆-----认证登陆----
    public function inlogin()
    {
        //if(Request::isAjax())
        //{
            $data = Request::param();
            $rule = [
                'email|邮箱'=>'require|email',
                'password|密码'=>'require|length:6,20'
            ];
            $res = $this->validate($data, $rule);
            if(true !== $res)
            {
                //return ['status'=>0, 'mes'=>$res];
                $this->error($res);
            }else{
                $result = UserModel::get(function ($query) use ($data) {
                    $query->where('email',$data['email'])->where('password',sha1($data['password']));
                });
                if(is_null($result))
                {
                    //return ['status'=>1, 'mes'=>'邮箱或密码错误，请认真检查！！！'];
                    $this->error('邮箱或密码错误，请认真检查！！！');
                }
                    Session::set('admin_info', $result->getData());
                    //return ['status'=>2, 'mes' => '恭喜，登陆成功！'];
                    $this->error('恭喜，登陆成功！','index/index');
            }
        //}else{
         //   $this->error('请求类型错误','login');
       // }
    }
    //欢迎界面
    public function welcome()
    {
        $this->islogin();
        return $this->fetch();
    }
    //样本界面
    public function samplelist()
    {
        $this->islogin();
        $email = Session::get('admin_info.email');
        $data = Saminfo::where('is_dele','0')->order('id desc')->select();
        $length = count($data);
        $this->assign('length',$length);
        $this->assign('xinx',$data);
        $this->assign('title',"收录信息");
        return $this->fetch();
    }
    //用户管理列表
    public function adminlist()
    {
        $this->islogin();
        $data['admin_info.id'] = Session::get('admin_info.id');
        $userlist = UserModel::where('id',$data['admin_info.id'])->order('id desc')->select();
        if(Session::get('admin_info.is_admin') == 2)
        {
            $userlist = UserModel::all();
        }
        $length = count($userlist);
        $this->assign('userlist',$userlist);
        $this->assign('length',$length);
        $this->assign('title','管理员列表');

        return $this->fetch();
    }
    //显示添加用户界面
    public function adminadd()
    {
        $this->islogin();
        $this->assign('title','添加用户');
        return $this->fetch();
    }
    //--------------添加用户----------
    public function useradd()
    {
        $data = Request::param();
        $rule = 'app\common\validate\User';
        $res = $this->validate($data, $rule);
        if(true !== $res)
        {
            return ['status'=>0, 'mes'=>$res];
        }else{
            if(UserModel::create($data))
            {
                return ['status'=>1, 'mes'=>"恭喜添加成功"];
            }
                return ['status'=>2, 'mes'=>"添加失败！"];
        }
    }
    //禁用用户-------
    public function adminstop()
    {
        $data = Request::param('id');
        //$data = 57;
        //return ['a'=>$data];
        $status = UserModel::where('id',$data)->find();
        //halt($status);
        //halt($status['status']);
        if($status['status'] == "启用")
        {
            UserModel::update(['status'=>0],['id'=>$data]);
      }else{
            UserModel::update(['status'=>1],['id'=>$data]);
      }

        //if($status['status'] == 1)
        //{
         //   $status['status'] = 0;
         //   UserModel::where('id',$data)->data($status['status'])->update();
       // }else {
        //    $status['status'] = 1;
          //  UserModel::where('id', $data)->data($status['status'])->update();
          //  }
    }
    //删除管理用户账号
    public function deleuser()
    {
        //获取要删除的ID
        $id = Request::param('id');
        //执行删除操作
        if(UserModel::where('id',$id)->delete())
        {
            return ['status'=>1, 'message'=>'删除成功'];
        }
        return ['status'=>0, 'message'=>'删除失败'];
    }
    //超级用户查看收录信息
    public function adminsamplelist()
    {
        $this->islogin();
        $email = Session::get('admin_info.email');
        $data = Saminfo::where('is_dele','0')->order('id desc')->select();
        $length = count($data);
        $this->assign('length',$length);
        $this->assign('xinx',$data);
        $this->assign('title',"收录信息");
        return $this->fetch();
    }
    //样本收信息管理
    public function samplemanagement()
    {
        $this->islogin();
        $email = Session::get('admin_info.email');
        $data = Saminfo::where('is_dele','0')->where('email',$email)->order('id desc')->select();
        $length = count($data);
        $this->assign('length',$length);
        $this->assign('xinx',$data);
        $this->assign('title',"收录信息");
        return $this->fetch();
    }
    //回收站
    public function recyling()
    {
        $this->islogin();
        $email = Session::get('admin_info.email');
        $data = Saminfo::where('is_dele','1')->where('email',$email)->order('id desc')->select();
        $length = count($data);
        $this->assign('length',$length);
        $this->assign('xinx',$data);
        $this->assign('title',"回收列表");
        return $this->fetch();
    }
    //--------------------------------恢复已永久删除----------------------------
    public function dele()
    {
        $id = Request::param('id');
        if(Saminfo::where('id',$id)->delete())
        {
            return ['status'=>1 ,'mes'=>"永久删除成功"];
        }
        return ['status'=>0 ,'mes'=>"永久删除失败了"];
    }
    //恢复样本数据
    public function restore()
    {
        $id = Request::param('id');
        $isdele['is_dele'] = '0';
        if(Saminfo::where('id',$id)->data($isdele)->update())
        {
            return ['status'=>1 ,'mes'=>"恭喜，成功恢复"];
        }
        return ['status'=>0 ,'mes'=>"恢复失败"];
    }
    //样本删除
    public function sampledele()
    {
        $id = Request::param('id');
        $isdele['is_dele'] = '1';
        if(Saminfo::where('id',$id)->data($isdele)->update())
        {
            return ['status'=>1 ,'mes'=>"恭喜，成功删除"];
        }
        return ['status'=>0 ,'mes'=>"删除失败"];
    }
    //样本显示页面
    public function sampleadd()
    {
        $this->islogin();
        $this->assign('title',"样本添加");
        return $this->fetch();
    }
    //---------------样本添加功能
    public function sampadd()
    {
        $data = Request::param();
        $data['email'] = Session::get('admin_info.email');
        $data['username'] = Session::get('admin_info.name');
        $rule = [
            'sample_num|样本编号'=>'require|unique:sample'
        ];
        $res = $this->validate($data, $rule);
        if(true !== $res)
        {
                return ['status' => 2, 'mes' =>$res];
        }else {
            if (Saminfo::create($data))
            {
                return ['status' => 1, 'mes' => "添加成功"];
            }
                return ['status' => 0, 'mes' => "添加失败"];
        }
    }
    //样本编辑页面
    public function editor()
    {
        $this->islogin();
        $data = Request::param();
        $da = Saminfo::where($data)->select();
        $this->assign('tt',$da);
        $this->assign('title',"样本编辑");
        return $this->fetch();
    }
    //样本操作更新
    public function sampleupdate()
    {
        $data = Request::post();
        $rule = [
            'sample_num|样本编辑'=>'require|unique:sample',
        ];
        $res = $this->validate($data,$rule);
        if(true !== $res)
        {
            return ['status'=>0,'mes'=>$res];
        }else {
            if(Saminfo::where('id','=',$data['id'])->update([
                 'sample_num'=>$data['sample_num'],'position_num'=>$data['position_num'],'name'=>$data['name'],
                'sample_type'=>$data['sample_type'],'save_time'=>$data['save_time'],'serial_num'=>$data['serial_num'],
                'qubit'=>$data['qubit'],'volume'=>$data['volume'],'total'=>$data['total'],'alu_a'=>$data['alu_a'],
                'alu_b'=>$data['alu_b'],'alu_c'=>$data['alu_c'],'total_ng'=>$data['total_ng'],'expect_volume'=>$data['expect_volume'],
                'expect_qubit'=>$data['expect_qubit'],'input_volume'=>$data['input_volume'],'input_total'=>$data['input_volume'],
                'cycle_num'=>$data['cycle_num'],'index'=>$data['index'],'index_qubit'=>$data['index_qubit'],
                'complete_date'=>$data['complete_date'],'note'=>$data['note']
                ]))
            {
                return ['status' => 1, 'mes' => "恭喜！"];
            }
            return ['status' => 0, 'mes' => "对不起，修改失败！"];

        }

    }

    //个人中心界面
    public function grzx()
    {
        $this->islogin();
        $data = Session::get('admin_info.id');
        $info = UserModel::where('id',$data)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
    //保存修改信息操作
    public function baoc()
    {
        if(Request::isAjax())
        {
            $data = Request::param();

            if(UserModel::where('email',Session::get('admin_info.email'))->data($data)->update())
            {
                return ['status'=>1,'mes'=>"保存成功"];
            }
                return ['status'=>0,'mes'=>"保存失败了！"];
            }else{
            $this->error('提交类型错误');
        }
    }
    //密码修改界面
    public function xuig()
    {
        $this->islogin();
        return $this->fetch();
    }
    //密码进行修改操作
    public function mima()
    {
        if(Request::isAjax())
        {
            $data = Request::post();
            $rule = [
                'password|密码'=>'require|length:6,20|alphaNum|confirm',
            ];
            $res = $this->validate($data,$rule);
            if(true !== $res)
            {
                return ['status'=>0,'mes'=>$res];
            }else {
                $data = Request::except('password_confirm','post');
                $data['password'] = sha1($data['password']);
                if (UserModel::where('email', Session::get('admin_info.email'))->data($data)->update()) {
                    return ['status' => 2, 'mes' => "恭喜，密码修改成功！"];
                }
                    return ['status' => 1, 'mes' => "对不起，密码修改失败！"];

            }
        }else{
            $this->error('提交类型错误');
        }
    }

}
