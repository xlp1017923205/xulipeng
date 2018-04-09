<?php
namespace app\index\controller;
use think\Controller;
use think\Validate;
use app\index\validate\UserValidate;
/*
验证器的使用
 *  */

class UserController extends Controller{
    public function index(){
         //在实例化的时候要添加验证器规则 require
        $validate = new Validate([
           //必填字段
            'username'=>'require|max:10',
            'password'=>'require|min:6'
        ]);
        //模拟数据
        $data = [
            'username'=>'impossable',
            'password'=>'admin'
        ];
        //执行验证规则，对数据进行验证
        if(!$validate->check($data)){
            //当验证没有通过的时候执行的内容
            dump($validate->getError());
        }
        
    }
    
    public function index2(){
        $data = [
            'username'=>'impossable',
            'password'=>'admin123',
            'email'=>'admin'
        ];
        $validate = new UserValidate;
        if(!$validate->check($data)){
            dump($validate->getError());
        }
        
    }
   
    public function index3(){
        //模拟数据
        $data = [
            'username'=>'impossable',
            'password'=>'admin123',
            'email'=>'admin'
        ];
        $validate = new UserValidate;
        if(!$validate->check($data)){
            dump($validate->getError());
        }
    }
    /*
        特定场景的验证
     *      */
    public function index4(){
        $data = [
            'username'=>'impossableaaa',
            'password'=>'admin123',
            'email'=>'admin@qq.com',
            'number'=>'101',
        ];
        $validate = new UserValidate();
        $result = $validate->scene('edit')->check($data);
        if(!$result){
            dump($validate->getError());  
        }
    }
    
    
    public function index5(){
        return $this->fetch();
    }
    
    public function  index6(){
        
    }
    
    
}













?>

