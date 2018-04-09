<?php

namespace app\Admin\controller;

use think\Controller;
use think\Session;
use app\admin\model\Admin;
use app\admin\validate\AdminValidate;
use think\Db;

/*
  路由的使用
 *  */

class AdminController extends Controller {

    //管理员管理的默认方法 ***
    public function index() {
        //首先用户是否登陆
        if (!Session::get('admin')) {
            //如果没登陆就跳转到登陆的方法
            return $this->redirect('login');
        } else {
            $admin = Session::get('admin');
            //第二部分 管理员列表内容
            $order_username = input('fild', '');
            $order_order = input('order', '');
            $this->assign('order_order', $order_order);
            $paginate = Admin::where(null)->order($order_username, $order_order)->paginate(5);
            ;
            //获取前台是否传了username值
            $username = input('username', '');
            if ($username != '') {
                //这里是将用户输入的内容添加到查询中，得到要查询后的数据
                $paginate = Admin::where('username', "like", "%" . $username . "%")->paginate(3, false, ['query' => ['username' => $username]]);
                //这个操作是搜索框的数据的回显
                $this->assign('username', $username);
            }
            $this->assign('paginate', $paginate);
            return $this->fetch();
        }
    }

    public function login() {
        //文本框传过来两个参数 ***
        $username = input('username', '');
        $password = input('password', '');
        $condition = [
            'username' => $username,
            'password' => $password,
        ];
        //判断如果两个内容都不为空就跳主页面
        if ($username != '' && $password != '') {
            //数据库查询数据
            $admin = Admin::where($condition)->find();
            //判断如果有值
            if ($admin) {
                Session::set('admin', $admin);
                return $this->redirect('index');
            } else {
                $this->assign('message', '管理员信息错误');
                return $this->fetch();
            }
        } else {
            return $this->fetch();
        }
    }

    //注销操作 ***
    public function loginout() {
        //清除session
        Session::delete('admin');
        return $this->redirect('admin/login');
    }

    //修改数据操作
//      function update(){
//        $id= $_POST["id"];
//        $username = $_POST["username"];
//        $password = $_POST["password"];
//        $admin= Admin::get($id);
//        $admin->username="$username";
//        $admin->password="$password";
//        $admin->save();
//        
//    }
    //数据回显
//    function select(){
//        
//        $id =$_POST["id"];
//        $admin=Admin::get($id);
//        
//        return json($admin);
//        
//    }
    //重置密码
//    public function reset(){
//       $id= $_POST["id"];
//        $admin= Admin::get($id);
//        $admin->password="123456";
//        $admin->save();
//    }
    //添加操作
//    public function insert(){
//         $username = $_POST["username"];
//        $password = $_POST["password"];
//        
//        $admin = new Admin();
//        $admin->username="$username";
//        $admin->password="$password";
//        $admin->save();
//         $this->redirect("http://localhost/shop/public/index.php/admin/admin/index");
//    }
    //彻底删除
//    function delete(){
//        $id = $_POST["id"];
//        $admin = Admin::destroy($id,true);
//        return $admin;
//    }
    //批量删除
    //双重验证  ***

    public function multi() {
        //得到需要删除的用户的ID，默认值为空
        $selected = input('selected/a', []);
        //如果没有获取到值，就直接重定向到index方法
        if (empty($selected)) {
            return $this->redirect('index');
        }
        //否则，批量删除选中的对应的ID的数据
        Admin::destroy($selected);
        return $this->redirect("index");
    }

    //修改密码
    public function setPassword($id) {
        //将ID传到页面
        $this->assign('id', $id);
        //根据id获取用户信息
        $admin = Admin::get($id);
        //将用户信息发送到页面上
        $this->assign("admin", $admin);
        //获取请求方式
        $request = request();
        //获取请求方式，为了调液面到setpassword页面

        if ($request->isGet()) {
            return $this->fetch();
        } elseif ($request->isPost()) {
            //添加验证
            $validate = new AdminValidate;
            //批量检验
            $validate->batch(true);
            //setPasswoed场景
            if (!is_null($id)) {
                //调用setpassword的场景
                $validate->scene("setPassword");
            }
            //验证未通过
            if (!$validate->check($request->post())) {


                $this->assign('message', $validate->getError());
                $this->assign('id', $id);
                $this->assign('data', $request->post());
                return $this->fetch();
            }
            //验证通过的情况==》》更新数据

            $result = $admin->data(['password' => md5(input('password'))])->save();
            if ($result) {
                return $this->redirect('index');
            } else {
                return $this->redirect('setPassword', [$id => $id]);
            }
        }
    }

    //管理员新增数据，修改用户名
    //如果没有获取id值
    public function set($id = null) {
        //向网页传id值
        $this->assign('id', $id);
        //请求方式
        $requset = request();
        //自动校检
        if(is_null($id)){
            checkPrivRedirect("admin/admin-create");
        }    else{
            checkPrivRedirect("admin/admin-edit");
        }
        
        
        //页面默认选中已选择项 select role_id from shop_admin where admin_id =$id;
        $checked_roles = Db::name('role_admin')->where('admin_id', $id)->column('role_id');
        echo is_null($checked_roles);
        //如果是get请求就是跳转页面新增页面    post请求获取id值就是编辑页面
        if ($requset->isGet()) {
            //获取所有角色
            $this->assign('role_list', Db::name('role')->select());
            if (is_null($id)) {
                return $this->fetch();
            } else {
                //查询结果
                $data = Admin::get($id);
                //将用户信息发送到页面上
                //回显数据
                $this->assign('data', $data);
                $this->assign('checked_roles', $checked_roles);
                return $this->fetch();
            }
        }
        //如果是post请求 说明进入到了新增页面完成数据保存
        if ($requset->isPost()) {
            //实例化验证规则
            $validate = new AdminValidate();
            //批量校验
            $validate->batch(true);
            //如果有id的话
            if (!is_null($id)) {
                //调用setPassword的场景
                $validate->scene('update');
            }
            //如果没有获取post数据的话
            if (!$validate->check($requset->post())) {
                //返回错误信息
                $this->assign('message', $validate->getError());
                //返回到编辑
                return $this->fetch();
            } else {
                //编辑页面
                //如果有id的话
                if (!is_null($id)) {
                    //获取admin数据库的该id数据
                    $admin = Admin::get($id);                    
                } else {
                        $admin = new Admin();
                    } 
                    
                    $admin->data($requset->post());
                    $result = $admin->allowField(true)->save();
                    if ($result) {
                        //当保存成功后保存角色信息
                        $roles = input('roles/a',[]);
                        //先删除  $checked_roles = 1,2  $roles = 2,3  array_diff====1
                        $deletes = array_diff($checked_roles, $roles);
                        db::name('role_admin')->where([
                            'admin_id'=>$id,
                            'role_id'=>['in',$deletes],
                        ])->delete();
                        $insert = array_diff($roles, $checked_roles);
                        $rows = array_map(function($role_id) use ($id){
                            return [
                                'role_id' => $role_id,
                                'admin_id' => $id,
                        ];
                                    
                        },$insert);
                       Db::name('role_admin')->insertAll($rows);
                        return $this->redirect('index');
                    } else {
                        return $this->fetch();
                    }
                }
            }
        }
    

}

?>
