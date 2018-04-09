<?php
namespace app\admin\behavior;
use think\Session;
use priv\Privilege;

class CheckAuth{
    public function run(& $params){
        //获取请求
        $request = request();
        //判断是否登陆
        //解决无限循环login问题
        //首先定义特例
        $except = [
            'admin' =>['login','index'],
            'make' =>['table','generate','info'],
            'group' =>['index'],
            'product' =>['index','set'],
            'category' =>['index','set','multi'],
        ];
        //判断特例的遥控器   $request->controller获取请求中controller的名字
        if(isset($except[strtolower($request->controller())])){
            //判断特例的控制器中的特例方法
            if(in_array($request->action(), $except[strtolower($request->controller())])){
                return ;
            }
        }
        //判断是否登陆了
        if(!Session::has('admin')){
            //没用$this-》redirect（)的原因是当前类不是个控制器，没有继承controller
            //出现的问题 ， 会提示Firefox 检测到该服务器正在将指向此网址的请求无限循环重定向
            redirect('admin/admin/login')->send();
            die();
        }
        
        //判断是否拥有权限访问
        if(!Privilege::checkpriv($request->path())){
            //不能经过校检就返回登陆页面
            redirect('admin/admin/login',[],'302',[
                'message'=>'用户无权访问',
            ])->send();
        }
        
        $path = $request->module().'/'.strtolower($request->controller()).'/'.$request->action();
        //判断是否拥有访问权限
        if(!Privilege::checkpriv($path)){
            //不能经过校检就返回登陆页面
            redirect('admin/admin/login',[],'302',[
                'message' =>'用户无权访问',
            ])->send();
            die;
        }
        
        
    }
    
    
}


?>
