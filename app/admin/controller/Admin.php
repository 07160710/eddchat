<?php
namespace app\admin\controller;
use think\Db;
use think\Validate;

/**
 * 管理员管理
 */
class Admin extends Base
{
    //管理员页面
    function home_page()
    {
        $admin_nums = Db::name('users')->count();
        $pageObject = new \Page($admin_nums);
        $admin_list = Db::name('users')
            ->order('create_time desc')
            ->epage()
            ->select();

        //角色列表
        $role_list=Db::name('role')
            ->order('create_time desc')
            ->select();

        $map = [
            'admin_nums'=>$admin_nums,
            'admin_list'=>$admin_list,
            'pages'=>$pageObject->showPages(3),
            'role_list'=>$role_list
        ];
        return view('home_page',$map);
    }

    //添加管理员
    function add_think()
    {
        $validate = new Validate([
            ['user_login','require|unique:users|min:5', '登录名称必填|登录名已经存在|登录名最小5个字符'],
            ['user_pass','confirm:confirm_pass|different:user_login','密码和确认密码不一致|密码不能和账号重复'],
            ['user_email','require|email','邮箱必填|邮箱格式不正确'],
        ]);

        if (!$validate->check($_POST))
            $this->error($validate->getError());

        $addMap = $_POST;
        $addMap = array_remove($addMap, 'confirm_pass');
        $addMap = array_remove($addMap, 'role_id');

        $randNums = random();
        $addMap['user_pass'] = encrypt_password(input('user_pass'),$randNums);
        $addMap['user_pass_salt'] =$randNums ;
        $addMap['user_nicename'] =input('user_login') ;
        $addMap['create_time'] =time() ;

        if ($admin_id = Db::name('users')->insertGetId($addMap)) {
            $addRoleMap = [
                'role_id'=>input('role_id'),
                'user_id'=>$admin_id
            ];

            if (Db::name('role_user')->insert($addRoleMap)) {
                $this->success('管理员添加成功');
            }else{
                $this->error('管理员添加失败');
            }

        }else{
            $this->error('管理员添加失败');
        }

    }

    //编辑页面
    function edit_page()
    {
        $users = get_users(input('id'));
        $role_name = get_role(input('id'));
        //角色列表
        $role_list=Db::name('role')
            ->order('create_time desc')
            ->select();

        $map = $users;
        $map['role_list'] = $role_list;
        $map['role_name'] = $role_name;
        return view('edit_page',$map);
    }

    //编辑逻辑
    function edit_think()
    {
        $validate = new Validate([
            ['user_login','require|unique:users|min:5', '登录名称必填|登录名已经存在|登录名最小5个字符'],
            ['user_pass','confirm:confirm_pass|different:user_login','密码和确认密码不一致|密码不能和账号重复'],
            ['user_email','email','邮箱格式不正确'],
        ]);

        if (!$validate->check($_POST))
            $this->error($validate->getError());

        $editMap = $_POST;
        $editMap = array_remove($editMap, 'confirm_pass');
        $editMap = array_remove($editMap, 'role_id');
        $editMap['update_time'] = time();

        //是否修改密码
        if (!empty(input('user_pass'))) {
            $randNums = random();
            $editMap['user_pass'] = encrypt_password(input('user_pass'), $randNums);
            $editMap['user_pass_salt'] = $randNums;
        }else{
            $editMap = array_remove($editMap, 'user_pass');
        }
        //是否更换状态
        if (!input('?user_status')) {
            $editMap['user_status'] = input('user_status')?input('user_status'):0;
        }

        $role_name = Db::name('role')->where('id', input('role_id'))->value('name');
        $old_role_id = Db::name('role')->where('name', get_role(input('id')))->value('id');

        //是否更换角色
        if ($role_name != get_role(input('id'))) {

            if (del_role_log(input('id'),$old_role_id)) {
                $addRoleMap = [
                    'role_id'=>input('role_id'),
                    'user_id'=>input('id')
                ];
                Db::name('role_user')->insert($addRoleMap);
            }else{
                $this->error('管理员角色分配修改失败');
            }
        }


        if (Db::name('users')->update($editMap)) {
            $this->success('管理员更新成功');
        }else{
            $this->error('管理员未做任何修改');
        }
    }

    function del_think()
    {
        $old_role_id = Db::name('role')->where('name', get_role(input('id')))->value('id');
        if (del_role_log(input('id'), $old_role_id)) {
           if (Db::name('users')->delete(input('id'))) {
               $this->success('该管理员删除成功');
           }else{
               $this->error('该管理员删除失败');
           }
        }
    }
}