<?php
namespace app\admin\controller;

use think\Db;

class Index extends Base
{
    public function index()
    {
        $self = get_users(open_secret(cookie('UID')));
        $role_name = get_role(open_secret(cookie('UID')));
        $menuAuth = new \Auth();
        $menuAuth=$menuAuth->get_auth_menu(open_secret(cookie('UID')));
        $menuAuth = my_sort($menuAuth, 'listorder', SORT_DESC);

        $map = [
            'self'=>$self,//登录信息
            'role_name'=>$role_name,
            'menuAuth'=>$menuAuth,//权限菜单
            'url'=>url('admin/'.config('controller').'/'.config('action'))//默认跳转地址
        ];
        return view('public/base',$map);
    }

    function two()
    {
        return view('two');
    }

    //退出登陆
    function login_out()
    {
        cookie('UID', null);
        session('SUID', null);
        session('TOKEN_LOGIN', null);
        $this->redirect(url('admin/Login/show_login'));
    }

    //php扩展检测
    function get_php_ext()
    {
        if (!extension_loaded('memcache')) {
            $this->error('Memcache扩展未安装');
        }else{
            $this->success('欢迎使用alphaCMS');
        }
    }
}
