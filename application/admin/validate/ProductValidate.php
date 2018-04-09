<?php

namespace app\admin\validate;
use think\Validate;

class ProductValidate extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填'%name
                              'title'=>'require',


                       'upc'=>'require',


                       'image'=>'require',


                       'quantity'=>'require',


     
    ];
    
    protected $message=[
       
                        '%field%.require' => '名称不能为空',
                '%field%.require' => '通用代码不能为空',
                '%field%.require' => '图像不能为空',
                '%field%.require' => '库存不能为空',

    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
       
    ];
}