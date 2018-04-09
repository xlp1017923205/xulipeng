<?php

namespace app\admin\validate;
use think\Validate;

class ActionValidate extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填'%name
                              'title'=>'require',


                       'rule'=>'require',


     
    ];
    
    protected $message=[
       
                        'title.require' => '权限不能为空',
                'rule.require' => '规则不能为空',

    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
       
    ];
}