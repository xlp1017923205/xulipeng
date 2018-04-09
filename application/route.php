<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//写法二路由
//use think\Route;
//Route::rule('admin/[:name]', function($name){
//    return '你输入的内容是'.$name;               //路由写法二二二二二二二二二
//});

return [
    /*

     * 写法一
     *      */
    //如果地址栏内容为admin/xxx会代替admin/admin/admin
//    'admin/:name'=> 'admin/admin/index',
//    'admin/[:name]$'=> 'admin/admin/index',//[]代表参数可选，添加$代码当前路由要完整匹配
    
//如果要显示时间，那么要把时间放到年月份下面
// 'admin/:year/:month'=>['admin/admin/archive',['method'=>'get'],['year'=>'\d{4}','month'=>'\d{2}']],   
// 'admin/:id'=>['admin/admin/get',['method'=>'get'],['id'=>'\d+']],   
// 'admin/:name'=>['admin/admin/read',['method'=>'get'],['name'=>'\w+']],    
//    
    
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];
