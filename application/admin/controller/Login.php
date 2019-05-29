<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/29
 * Time: 下午5:24
 */



namespace app\admin\controller;

use think\Controller;

class Login extends Controller
{


    public function in()
    {
        $re = $this -> request;

        if ($re->isPost()){

            $data = $re->only(['mobile','password']);

            $rule = [
                'mobile'=> 'require|mobile',
                'password'=> 'require|length:6,12'
            ];

            $msg = [
                'mobile.require' => '手机号为必填项',
                'mobile.mobile' => '手机号填写有误',
                'password.require' => '请输入密码',
                'password.length' => '密码长度过长或者过短',
            ];

            $info = $this->validate($data,$rule,$msg);

            if ($info != true){
                return $this->error($info);
            }

            $admin = admin::where('mobile',$data['mobile'])->find();

            if (!$admin){
                $this->error('账户或密码有误');
            }
            if (password_verify($data['password'],$admin->password)){
                session('adminLoginInfo',$admin);
                $this->success('👌',url('admin/Index/index'));
            }else{
                $this->error('账户或密码有误');
            }

        }

        if ($re->isGet()){
            return $this->fetch();
        }


    }
}