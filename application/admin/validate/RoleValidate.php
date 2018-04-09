<?php

namespace app\admin\validate;
use think\Validate;

class RoleValidate extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填'%name
                              'title'=>'require',


                       'description'=>'require',


                       'is_super'=>'require',





     
    ];
    
    protected $message=[
       
                        'title.require' => '角色不能为空',
                'description.require' => '描述不能为空',
                'is_super.require' => '是否为超管不能为空',


    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
       
    ];
}