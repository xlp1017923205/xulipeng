<?php

namespace app\Admin\controller;

use think\Config;
use think\Session;
use think\Controller;
use app\admin\model\Category; //因为不是每一个controller用到的model都是admin'
use app\admin\validate\CategoryValidate;

class CategoryController extends Controller {

    public function index(){
        
        //获取分类树的内容
        $rows = (new Category())->getTree();
        //传值
        $this->assign('rows',$rows);
        return $this->fetch();
        
    }

//管理员信息的添加与修改
    public function set($id = null) {
        $this->assign('id', $id);
        $rows = (new Category())->getTree();
        $this->assign('rows', $rows);
        //获取请求方式
        $request = request();
        //如果是get请求  说明是跳转页面 但是也是一种是新增另一种是编辑
        if ($request->isGet()) {
            if (is_null($id)) {
                return $this->fetch();
            } else {
                //查询结果
                $data = Category::get($id);
                //将用户信息发送到页面上
                $this->assign('data', $data);
                return $this->fetch();
            }
        }
        if ($request->isPost()) {

            $validate = new CategoryValidate();
            //批量校验
            $validate->batch(true);

            if (!is_null($id)) {
                
            }
            if (!$validate->check($request->post())) {
                $this->assign('message', $validate->getError());
                return $this->fetch();
            } else {

                if (!is_null($id)) {
                    $model = Category::get($id);
                    $result = $model->data($request->post())->save();
                    if ($result) {
                        return $this->redirect('index');
                    } else {
                        return $this->fetch();
                    }
                } else {
                    //新增
                    $model = new Category();
                    $result = $model->data($request->post())->save();
                    //allowField作用是去除掉form表单中数据库不存在的字段
                    if ($result) {
                        return $this->redirect('index');
                    } else {
                        return $this->fetch();
                    }
                }
            }
        }
    }

//批量删除
    public function multi() {
        //得到需要删除的用户的id，默认值为空
        $selected = input('selected/a', []);
        //如果没有获取到值，就直接重定向到index方法
        if (empty($selected)) {
            return $this->redirect('index');
        }

        //否则，批量删除选中的对应的id的数据
       Category::destroy($selected);
        return $this->redirect('index');
    }

    public function aaaa(){
        
        $this->fetch();
    }
    
    
}
