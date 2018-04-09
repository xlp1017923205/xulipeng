<?php
namespace app\Admin\model;
use think\Model;
class Admin extends Model{
    //自动加密
    protected $insert = ["password"];
    protected function setPasswordAttr($value){
        return md5($value);
    }
}




?>
