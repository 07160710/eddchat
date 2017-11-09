<?php
use think\Db;
/**
 * 获取管理员角色
 * @param $user_id
 * @return mixed
 */
function get_role($user_id)
{
    $name=Db::name('users')
        ->alias('u')
        ->where('ra.user_id',$user_id)
        ->join('role_user ra', 'ra.user_id=u.id')
        ->join('role r', 'r.id=ra.role_id')
        ->value('name');
    return $name;
}

function del_role_log($user_id, $role_id)
{
    $map = [
        'user_id'=>$user_id,
        'role_id'=>$role_id
    ];

    $role_user_id=Db::name('role_user')->where($map)->value('role_user_id');
    if ($role_user_id) {
        Db::name('role_user')->delete($role_user_id);
    }
    return 'ok';
}

/**
 * 添加图片日志
 * @param $path
 */
function add_img_db($path)
{
    $map = [
        'upload_date'=>time(),
        'img_size' => getFileSize(filesize('.'.$path)),
        'ip'=>request()->ip(),
        'user_id' => open_secret(cookie('UID')),
        'img_path'=>$path
    ];
    Db::name('imgs')->insert($map);
}
function del_img_db($path)
{
    $img_id=Db::name('imgs')
        ->where('img_path', $path)
        ->value('img_id');
    Db::name('imgs')->delete($img_id);
}
/**
 * 是否禁用
 * @param $type
 */
function is_stop($type)
{
    if ($type == 0) {
        return '<span class="label label-danger">非正常</span>';
    } elseif ($type == 1) {
        return '<span class="label label-success">正常</span>';
    }
}

/**
 * 菜单类型
 * @param $type
 * @return string
 */
function menu_type($name,$type)
{
    if ($type == 0) {
        return '<span class="label label-default">'.$name.'</span>';
    } elseif ($type == 1) {
        return '<span class="label label-warning">'.$name.'</span>';
    }
}

/**
 * select框是否选中
 * @param $select_id
 * @param $id
 * @return string
 */
function is_selected($select_id, $id)
{
    return $select_id == $id ? 'selected' : '';
}

/**
 * checked是否选中
 * @param $is_ok
 * @return string
 */
function is_checked($is_ok)
{
    return $is_ok ? 'checked' : '';
}

/**
 * 默认头像
 * @param $img
 */
function is_img($img)
{
    return $img ? $img : config('view_replace_str.__UPLOAD__') . '/admin/common/upload.svg';
}

/**
 * 获取文章分类名称
 * @param $posts_id文章id
 * @return false|PDOStatement|string|\think\Collection
 */
function get_terms($posts_id)
{
    $terms=Db::name('terms')
        ->alias('t')
        ->where('object_id',$posts_id)
        ->join('term_relationships r', 'r.term_id=t.term_id')
        ->select();
    return $terms;
}

/**
 * 判断文章所属类型
 * @param $posts_id
 * @param $term_id
 * @return string
 */
function post_term($posts_id,$term_id)
{
    $map = [
        'object_id'=>$posts_id,
        'term_id'=>$term_id
    ];
    $terms=Db::name('term_relationships')

        ->where($map)
        ->select();
    if (!empty($terms)) {
        return 'selected';
    }else{
        return '';
    }
}
/**
 * 获取插件参数
 * @param $param
 * @return array
 */
function get_param_arr($param)
{
    $arr=explode('|', $param);
    return $arr;
}

/**
 * 获取指定插件某个参数值
 * @param $wid_id
 * @param $wid_name
 * @return string
 */
function plugins_value($wid_id,$wid_name)
{
    $widgets = Db::name('widgets')->where('wid_id',$wid_id)->value('wid_params');
    $params_arr = explode('|', $widgets);
    foreach ($params_arr as $k=>$v){
        $chids_arr=explode(':',$v);
        foreach ($chids_arr as $k) {
            if ($k == $wid_name) {
                return  $chids_arr[1];
            }
        }
    }

    return '未找到';
}

/**
 * 获取站点配置信息
 * @param $name
 * @return mixed
 */
function get_options($name)
{
    $option_value=Db::name('options')->where('option_id',6)->value('option_value');
    return getAttr($option_value, $name);
}

function rm_cache_menu()
{
    $mem = new \think\cache\driver\Memcache();
    $mem->rm('menu');
}
/*==========================================================extra=====================================================*/