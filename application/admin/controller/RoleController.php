<?php

namespace app\Admin\controller;

use think\Config;
use think\Session;
use think\Controller;
use app\admin\model\Action;
use app\admin\model\Role; //因为不是每一个controller用到的model都是admin'
use app\admin\validate\RoleValidate;
use think\Db;

class RoleController extends Controller {

    public function index() {



        //第二部分，公共列表内容
        $model = Role::where(null);
        //获取前台搜索条件
        $field = [];
        //拼出来的搜索条件 where_list只是为了替换内容的时候能找到
        $title = input('title', ''); //获取页面传来的值
        if ('' != $title) {
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('title', 'like', '%' . $title . '%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['title'] = $title;
            //这个操作为了搜索栏的回显
            $this->assign('title', $title);
        }

        $description = input('description', ''); //获取页面传来的值
        if ('' != $description) {
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('description', 'like', '%' . $description . '%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['description'] = $description;
            //这个操作为了搜索栏的回显
            $this->assign('description', $description);
        }

        $is_super = input('is_super', ''); //获取页面传来的值
        if ('' != $is_super) {
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('is_super', 'like', '%' . $is_super . '%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['is_super'] = $is_super;
            //这个操作为了搜索栏的回显
            $this->assign('is_super', $is_super);
        }

        $sort = input('sort', ''); //获取页面传来的值
        if ('' != $sort) {
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('sort', 'like', '%' . $sort . '%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['sort'] = $sort;
            //这个操作为了搜索栏的回显
            $this->assign('sort', $sort);
        }



        $paginate = $model->paginate(5, FALSE, ['query' => $field]);
        $this->assign('paginate', $paginate);
        return $this->fetch();
    }

//管理员信息的添加与修改
    public function set($id = null) {
        $this->assign('id', $id);
        //获取请求方式
        $request = request();
        //如果是get请求  说明是跳转页面 但是也是一种是新增另一种是编辑
        if ($request->isGet()) {
            if (is_null($id)) {
                return $this->fetch();
            } else {
                //查询结果
                $data = Role::get($id);
                //将用户信息发送到页面上
                $this->assign('data', $data);
                return $this->fetch();
            }
        }
        if ($request->isPost()) {

            $validate = new RoleValidate();
            //批量校验
            $validate->batch(true);

            if (!is_null($id)) {
                
            }
            if (!$validate->check($request->post())) {
                $this->assign('message', $validate->getError());
                return $this->fetch();
            } else {

                if (!is_null($id)) {
                    $model = Role::get($id);
                    $result = $model->data($request->post())->save();
                    if ($result) {
                        return $this->redirect('index');
                    } else {
                        return $this->fetch();
                    }
                } else {
                    //新增
                    $model = new Role();
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
        Role::destroy($selected);
        return $this->redirect('index');
    }

//给角色授权方法
    public function setAction($id){
        //得到请求的内容
        $reqest= request();
        //根据id值来获取角色信息
        $role=Role::get($id);
        //将角色信息传给页面
        $this->assign('role',$role);
        //将权限信息传到页面
        $this->assign('action_list',Action::select());
        //将id信息传到页面
        $this->assign('id',$id);
        //获取当前角色已有的权限
        $owner_actions=Db::name('role_action')->where('role_id',$id)->column('action_id');
        //isGet代表打开页面操作
        if($reqest->isGet()){
            $this->assign('check_list',$owner_actions);
            return $this->fetch();
        }
        //isPost代表提交数据
        //获取页面传来的参数actions是个数组
        $actions=input('actions/a',[]);
        //如果一开始是选中状态，改为不选中，就把没选中的删掉  返回第一个参数中没有的部分数据
        $deletes=array_diff($owner_actions,$actions);//差集
        Db::name('role_action')->where([
            'role_id'=>$id,
            'action_id'=>['in',$deletes],
        ])->delete();
        //保存
        $inserts=array_diff($actions,$owner_actions);
        $rows=array_map(function($action_id) use($id){
            return [
                'role_id'=>$id,
                'action_id'=>$action_id,
                'create_time'=>time(),
                'update_time'=>time(),
            ];
        },$inserts);
        Db::name('role_action')->insertAll($rows);
        return $this->redirect('index');
    }

}
