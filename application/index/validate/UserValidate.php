<?php
namespace app\index\validate;
use think\Validate;

class UserValidate extends Validate{
    //在对应的规则类中制定规则
    protected  $rule = [
        //必填字段
        'username'=>'require|max:10',
        'password'=>'require|min:6',
        'email'=>'emali',
        'number'=>'number|between:10,100',
         ];
         protected  $message = [
             'username.require'=>'用户名必须填写',
             'username.max'=>'用户名最大长度为10',
             'password.min'=>'用户密码最小长度为6',
             'email.email'=>'邮箱格式错误',
             'number.email'=>'number值请输入10-100的数字',
             
         ];
         
         /* 
            可以为某一个场景设置验证规则
          *          
          *  */
         
         protected $scene = [
           'edit'=>['username'],
         ];
         
         
}




?>
