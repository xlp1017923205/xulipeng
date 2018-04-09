<?php
namespace app\Admin\controller;
use think\Config;
use think\Session;
use think\Controller;
use app\admin\model\Product;//因为不是每一个controller用到的model都是admin'
use app\admin\validate\ProductValidate;
use think\Db;


class ProductController extends Controller{
    public function index(){

    
    
    //第二部分，公共列表内容
    $model=Product::where(null);
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


 //判断搜索条件中是否具有upc条件
        $upc = input('upc','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('upc','like','%'.$upc.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['upc']=$upc;
            //这个操作为了搜索栏的回显
            $this->assign('upc',$upc);
        }


 //判断搜索条件中是否具有stock_status_id条件
        $stock_status_id = input('stock_status_id','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('stock_status_id','like','%'.$stock_status_id.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['stock_status_id']=$stock_status_id;
            //这个操作为了搜索栏的回显
            $this->assign('stock_status_id',$stock_status_id);
        }


 //判断搜索条件中是否具有is_sale条件
        $is_sale = input('is_sale','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('is_sale','like','%'.$is_sale.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['is_sale']=$is_sale;
            //这个操作为了搜索栏的回显
            $this->assign('is_sale',$is_sale);
        }


 //判断搜索条件中是否具有brand_id条件
        $brand_id = input('brand_id','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('brand_id','like','%'.$brand_id.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['brand_id']=$brand_id;
            //这个操作为了搜索栏的回显
            $this->assign('brand_id',$brand_id);
        }


 //判断搜索条件中是否具有category_id条件
        $category_id = input('category_id','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('category_id','like','%'.$category_id.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['category_id']=$category_id;
            //这个操作为了搜索栏的回显
            $this->assign('category_id',$category_id);
        }


 //判断搜索条件中是否具有admin_id条件
        $admin_id = input('admin_id','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('admin_id','like','%'.$admin_id.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['admin_id']=$admin_id;
            //这个操作为了搜索栏的回显
            $this->assign('admin_id',$admin_id);
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
            $this->assign('sku_list', Db::name('sku')->select());
            $this->assign('stock_status_list', Db::name('stock_status')->select());
            return $this->fetch();
            
        }else{
            //查询结果
            $data=Product::get($id);
            //将用户信息发送到页面上
            $this->assign('data',$data);
            return $this->fetch();
        }
        
    }
    if($request->isPost()){
        
        $validate=new ProductValidate();
        //批量校验
        $validate->batch(true);
        
        if(!is_null($id)){
            
        }
        if(!$validate->check($request->post())){
            $this->assign('message',$validate->getError());
            return $this->fetch();
            
        }else{

        if(!is_null($id)){
            $model=Product::get($id);
            $result=$model->data($request->post())->save();
            if($result){
                return $this->redirect('index');
            }else{
                return $this->fetch();
            }
            
        }else{
            //新增
            $model=new Product();
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
    Admin::destroy($selected);
    return $this->redirect('index');
}

}