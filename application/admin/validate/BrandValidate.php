<?php

namespace app\admin\validate;
use think\Validate;

class BrandValidate extends Validate{
    //在对应的规则类中制定规则
    protected $rule=[
        //必填'%name
                              'title'=>'require',


                       'logo'=>'require',


                       'site'=>'require',


     
    ];
    
    protected $message=[
       
                        'title.require' => '名称不能为空',
                'logo.require' => 'LOGO不能为空',
                'site.require' => '官网不能为空',

    ];
    
    
    //可以为某一个场景验证规则
    protected  $scene=[
       
    ];
}