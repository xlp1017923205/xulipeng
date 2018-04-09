<?php

function checkPrivRedirect($rules,$admin_id=NULL){
    if(!priv\Privilege::checkpriv($rules,$admin_id)){
        //不能经过效验就返回登陆页面
        redirect('admin/admin/login',[],'302',[
            'message'=>'用户无权访问',
        ])->send();
        die;
    }
}

function checkPrivForm($rules,$admin_id=null){
    
    return priv\Privilege::checkpriv($rules,$admin_id);
    
}


























