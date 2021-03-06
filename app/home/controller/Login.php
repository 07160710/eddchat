<?php
namespace app\home\controller;

use app\common\controller\HomeCom;
use GatewayClient\Gateway;
use think\cache\driver\Memcache;
use think\Db;
use think\Validate;

class Login extends HomeCom
{
    function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub

        if ($this->is_login()) {
            $this->redirect('home/Chat/home_page');
        }

        vendor('gateway.gatewayclient.Gateway');
        Gateway::$registerAddress = '127.0.0.1:1238';
    }
    //登录页面
    function home_page()
    {
        return view('home_page');
    }

    //登录逻辑
    function login_think()
    {
        $validate = new Validate([
            ['username', 'require', '用户名必须填写'],
            ['password', 'require', '密码必须填写'],
        ]);

        if (!$validate->check($_POST)) {
            $this->error($validate->getError());
        }

        $users=Db::name('users')
            ->where('user_login', input('username'))
            ->find();

        $mem = new Memcache();
        if ($mem->has($users['user_login'])) {
            $client_id=$mem->get($users['user_login']);
            if (Gateway::isOnline($client_id)) {
                $this->error('该用户已经上线');
            }
        }
        //判断用户是否存在
        if ($users&&$users['user_status']!=0) {

            $inp_pass = encrypt_password(input('password'), $users['user_pass_salt']);//输入密码转义
            $is_sure=$users['user_pass'] == $inp_pass;//密码比对

            //判断密码是否相同
            if ($is_sure) {

                $data_sign = [
                    'id'=>$users['id'],
                    'last_login_time'=>time(),
                    'last_login_ip' => request()->ip(),
                ];
                $save_users = $data_sign;
                $save_users['user_hits'] = $users['user_hits'] + 1;

                if (Db::name('users')->update($save_users)) {
                    cookie('CUID', set_secret($users['id']),604800);
                    session('CSUID', set_secret($users['id']));
                    session('CTOKEN_LOGIN', data_signature($data_sign));

                    $this->success('身份认证成功,即将登陆',url('home/chat/home_page'));
                } else {
                    $error = '用户登陆更新失败';
                }
            }else{
                $error = '密码不正确';
            }

        }else{
            $error = '该用户不存在或者被封';
        }

        $this->error($error);
    }
}