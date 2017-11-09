<?php
namespace app\common\controller;

use think\Controller;

class HomeCom extends Controller
{
    function is_login()
    {
        if (session('CSUID')) {
            $users = get_users(open_secret(session('CSUID')));
            if ($users) {
                $user_map = [
                    'id'=>$users['id'],
                    'last_login_time'=>$users['last_login_time'],
                    'last_login_ip' => $users['last_login_ip'],
                ];
                $CTOKEN_LOGIN = data_signature($user_map);
                if (session('CTOKEN_LOGIN') == $CTOKEN_LOGIN) {
                    cookie('CUID', set_secret($users['id']),604800);//再次计时
                    return true;
                }
            }
        }else{
            if (cookie('CUID')) {
                $users=get_users(open_secret(cookie('CUID')));
                if ($users && $users['user_status'] != 0) {
                    cookie('CUID', set_secret($users['id']),604800);//再次计时
                    return true;
                }
            }
        }
    }
}