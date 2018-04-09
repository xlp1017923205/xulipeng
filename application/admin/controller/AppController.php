<?php
namespace app\Admin\controller;
use think\Db;
use think\Session;

class AppController extends Controller{
    public function app($admin_id = null){
        if(is_null($admin_id)){
            $admin_id = Session::get('admin.id');
            dump($admin_id);
        }
    }
}
