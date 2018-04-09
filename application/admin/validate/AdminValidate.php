<?php

namespace app\admin\validate;
use think\Validate;

class AdminValidate extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填
        
        'password'=>'require',
        'password_confirm'=>'require|confirm:password',
        'username'=>'require',
        
    ];
    
    protected $message=[
       
        'password_confirm.confirm'=>'两次密码输入不一致',
        'username.require'=>'请输入要修改的内容哦哦哦哦哦！',
    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
        'setPassword'=>['password','password_confirm'],
        'update'=>['username'],
    ];
}
