<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
header("Access-Control-Allow-Origin:*");
use think\Route;
Route::get('web', '@home/Index/Web');
Route::get('web', '@home/Index/Web');
Route::post('cases', '@home/Index/Cases');
Route::post('content', '@home/Index/Content');
Route::post('content_detal', '@home/Index/ContentDetal');
Route::post('board', '@home/Index/Board');