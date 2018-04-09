<?php
namespace app\Admin\controller;
use think\Config;
use think\Session;
use think\Controller;
use app\admin\model\%model%;//因为不是每一个controller用到的model都是admin'
use app\admin\validate\%validate%;



class %controller% extends Controller{
    public function index(){

    
    
    //第二部分，公共列表内容
    $model=%model%::where(null);
    //获取前台搜索条件
    $field=[];
    //拼出来的搜索条件 where_list只是为了替换内容的时候能找到
    %where_list%
    
   $paginate=$model->paginate(5,FALSE,['query'=>$field]);
    $this->assign('paginate',$paginate);
    return $this->fetch();

    
}
//管理员信息的添加与修改
public  function set($id=null){
    $this->assign('id',$id);
    //获取请求方式
    $request= request();
    //如果是get请求  说明是跳转页面 但是也是一种是新增另一种是编辑
    if($request->isGet()){
        if(is_null($id)){
            return $this->fetch();
            
        }else{
            //查询结果
            $data=%model%::get($id);
            //将用户信息发送到页面上
            $this->assign('data',$data);
            return $this->fetch();
        }
        
    }
    if($request->isPost()){
        
        $validate=new %model%Validate();
        //批量校验
        $validate->batch(true);
        
        if(!is_null($id)){
            
        }
        if(!$validate->check($request->post())){
            $this->assign('message',$validate->getError());
            return $this->fetch();
            
        }else{
                            
        if(!is_null($id)){
            //编辑啊
            $model=%model%::get($id);
            $result=$model->data($request->post())->save();
            if($result){
                return $this->redirect('index');
            }else{
                return $this->fetch();
            }
            
        }else{
            //新增
            $model=new %model%();
            $result=$model->data($request->post())->save();
            //allowField作用是去除掉form表单中数据库不存在的字段
            if($result){
                return $this->redirect('index');
            }else{ 
                return $this->fetch();
            }
           
        }
        }
    }
    
}



//批量删除
public function multi(){
    //得到需要删除的用户的id，默认值为空
    $selected=input('selected/a',[]);
    //如果没有获取到值，就直接重定向到index方法
    if(empty($selected)){
        return $this->redirect('index');
    }
    
    //否则，批量删除选中的对应的id的数据
    %model%::destroy($selected);
    return $this->redirect('index');
}

}