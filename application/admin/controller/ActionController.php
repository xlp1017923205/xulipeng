<?php
namespace app\Admin\controller;
use think\Config;
use think\Session;
use think\Controller;
use app\admin\model\Action;//因为不是每一个controller用到的model都是admin'
use app\admin\validate\ActionValidate;



class ActionController extends Controller{
    public function index(){

    
    
    //第二部分，公共列表内容
    $model=Action::where(null);
    //获取前台搜索条件
    $field=[];
    //拼出来的搜索条件 where_list只是为了替换内容的时候能找到
    

 //判断搜索条件中是否具有title条件
        $title = input('title','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('title','like','%'.$title.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['title']=$title;
            //这个操作为了搜索栏的回显
            $this->assign('title',$title);
        }


 //判断搜索条件中是否具有rule条件
        $rule = input('rule','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('rule','like','%'.$rule.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['rule']=$rule;
            //这个操作为了搜索栏的回显
            $this->assign('rule',$rule);
        }

    
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
            $data=Action::get($id);
            //将用户信息发送到页面上
            $this->assign('data',$data);
            return $this->fetch();
        }
        
    }
    if($request->isPost()){
        
        $validate=new ActionValidate();
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
            $model=Action::get($id);
            $result=$model->data($request->post())->save();
            if($result){
                return $this->redirect('index');
            }else{
                return $this->fetch();
            }
            
        }else{
            //新增
            $model=new Action();
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
    Action::destroy($selected);
    return $this->redirect('index');
}

}