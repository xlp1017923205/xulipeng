<?php

namespace app\admin\validate;
use think\Validate;

class %validate% extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填'%name
       %validateField%     
    ];
    
    protected $message=[
       
        %validateMessage%
    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
       
    ];
}