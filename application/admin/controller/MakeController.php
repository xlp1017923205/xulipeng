<?php

namespace app\Admin\controller;

use think\Config;
use think\Controller;
use think\Db;

class MakeController extends Controller {

    protected $input = []; //代表的是获取的内容
    protected $lable = [];

    public function table() {
        return $this->fetch();
    }

    public function info() {
        //获取前台传来的表明
        $tablename = input('table', '');
        //根据表明得到想要的表注释
        //第一步sql
        $sql = "select table_comment from information_schema.tables where table_schema =? and table_name =?";
        //第二步 输入对应的库名
        $table_schema = Config::get('database.database');
        //第三部获取表名
        $table_name = Config::get('database.prefix') . $tablename;
        $row_table = Db::query($sql, [$table_schema, $table_name]);
        //第四步
        $sql = "select column_name,column_type,column_comment from information_schema.columns where table_schema=? and table_name=? ";
        $rows_field = Db::query($sql, [$table_schema, $table_name]);
        return [
            'comment' => $row_table[0]['table_comment'], //表明注释
            'table_schema' => $table_schema, //库名
            'table_name' => $table_name, //带前缀的表名
            'fields' => $rows_field, //表字段信息
            'table' => $tablename, //不带前缀的表名
        ];
    }

    //生成内容
    public function generate() {
        //第一步 获取表单提交的内容
        if (!request()->isPost()) {
            return $this->redirect("table");
        }
        //获取请求参数
        $this->input['table'] = input('table', '');
        $this->input['comment'] = input('comment', '');
        $this->input['fields'] = input('fields/a');
        //生成控制器
        $this->createController();
        $this->createModel();
        $this->createView();
        $this->createViewSet();
        $this->createValidate();
    }

    public function createController() {
        //给固定的模板赋值
        //第一步 整理需要的数据
        //模型名
        $model = $this->mkModel();
        $controller = $this->mkController();
        $validate = $this->mkValidate();
        //字段搜索
        $where_list = '';
        //读取模板内容 app_path项目路径
        $template = file_get_contents(APP_PATH . 'admin/code/indexWhere.php');
        //遍历字段，找到需要搜索的 使用子模板替换
        foreach ($this->input['fields'] as $field) {
            //遍历数组中search部分内容
            //第一步 去除掉不需要搜索的
            if (!isset($field['search']))
                continue;
            //第二步 找到需要搜索的
            $search = ['%field%', '%model%'];
            //准备替换
            $replace = [$field['name'], $model];
            //内容替换并累加
            $where_list .= str_replace($search, $replace, $template);
        }
        //执行controller模板文件的替换
        $template = file_get_contents(APP_PATH . 'admin/code/controller.php');
        //要被替换的占位符的值
        $search = ['%model%', '%validate%', '%controller%', '%where_list%'];
        //实际的值
        $replace = [$model, $validate, $controller, $where_list];
        //要生成的代码路径
        $file = APP_PATH . 'admin/controller/' . $controller . '.php';
        //将存储的结果内容占位符替换掉
        $content = str_replace($search, $replace, $template);
        //file_get_contents($file,$content),//第一个参数是生成的文件，第二个参数是文件的内同
        file_put_contents($file, $content);
        echo '控制器：', $file, '生成完毕', '<br>';
    }

    //生成model
    protected function createModel() {
        $model = $this->mkModel(); //创建模型
        $temolate = file_get_contents(APP_PATH . 'admin/code/model.php'); //获取路径里model的内容
        $search = ['%model%']; //赋值
        $replace = [$model]; //赋值
        $file = APP_PATH . 'admin/model/' . $model . '.php'; //定义路径
        $content = str_replace($search, $replace, $temolate); //model替换%model%
        file_put_contents($file, $content); //生成内容路径
        echo '模型：', $file, '生成完毕', '<br>';
    }

    //生成view
    public function createView() {
        //字段搜索
        $where_list = '';
        $index_data = '';
        $index_title = '';
        //读取项目路径
        $indexWhere_template = file_get_contents(APP_PATH . 'admin/code/indexWhere.html');
        $indexData_template = file_get_contents(APP_PATH . 'admin/code/indexData.php');
        $indexTitle_template = file_get_contents(APP_PATH . 'admin/code/indexTitle.php');
        //遍历字段
        foreach ($this->input['fields'] as $field) {
            
            //去除不需要搜索的
            if (isset($field['search'])) {
                $search = ['%name%', '%comment%'];
                $replace = [$field['name'], $field['comment']];
                $where_list .= str_replace($search, $replace, $indexWhere_template);
            }
            if (isset($field['index'])) {
                $search = ['%field%'];
                $replace = [$field['name']];
                $index_data .= str_replace($search, $replace, $indexData_template);
            }
            if (isset($field['index'])) {
                $search = ['%comment%'];
                $replace = [$field['comment']];
                $index_title .= str_replace($search, $replace, $indexTitle_template);
            }
        }
        //替换index中的内容
        $template = file_get_contents(APP_PATH . 'admin/code/index.html');
        $search = ['%comment%', '%index_where%', '%index_data%', '%index_title%'];
        $replace = [$this->input['comment'], $where_list, $index_data, $index_title];

        $file = APP_PATH . 'admin/view/' . $this->input['table'] . '/index.html';
        $dir = APP_PATH . 'admin/view/' . $this->input['table'];      
        
        if (is_dir($dir)) {
            $content = str_replace($search, $replace, $template);
            file_put_contents($file, $content);
        } else {
            mkdir(APP_PATH . 'admin/view/' . $this->input['table']);
            $content = str_replace($search, $replace, $template);
            file_put_contents($file, $content);            
        }

        //将存储的结果内容占位符替换掉

        echo '试图：', $file, '生成完毕', '<br>';
    }

         public function createViewSet(){
        //第一步，整理需要的数据
         //字段搜索
         $setForm='';
         //读取模板内容app_path项目路径
         $template=file_get_contents(APP_PATH.'admin/code/setForm.php');
         //遍历字段，找到需要搜素的，使用子模板替换
        foreach($this->input['fields'] as $field){
            //遍历数组中search部分内容
            //第一步：去除不需要搜索的
            if(!isset($field['set']))continue;
            //第二步：找到需要搜索的
            $search=['%comment%','%name%'];
            //准备替换
            $replace=[$field['comment'],$field['name']];
            //内容替换并累加
            $setForm .= str_replace($search,$replace,$template);
        }
        //执行controller模板文件的替换
        $template=file_get_contents(APP_PATH.'admin/code/set.php');
        //要被替换的占位符的值
        $search=['%comment%','%setForm%'];
        //实际值
        $replace=[$this->input['comment'],$setForm];
        //要生成的代码的路径
       $file=APP_PATH.'admin/view/'.$this->input['table'].'/set.html';
       //将储存的结果内容占位符替换掉
       $content=str_replace($search, $replace,$template);
       //file_put_contents($file,$content),//第一个参数生成的文件，第二参数是文件的内容
       file_put_contents($file,$content);
       echo 'set视图： ',$file,'生成完毕','<br>';
        
    }
    
    
    public function createValidate(){
        //得到Validate的名字
        $validate = $this->mkValidate();
        //初始化字段标签
        $label_list = '';
        $message_list = '';
        //读取循环的模版
        $template = file_get_contents(APP_PATH.'admin/code/validateField.php');
        $template_message = file_get_contents(APP_PATH.'admin/code/validateMessage.php');
        //循环遍历字段
        foreach ($this->input['fields'] as $field){
            //确定那些是必填的
            if(!isset($field['require']))continue;
            //判断哪些字段要被替换
            $search = ['%field%'];
            $search_message = ['%field%','%comment%'];
            //对应的内容值
            $replace = [$field['name']];
            $replace_message = [$field['name'],$field['comment']];
            //执行调换内容操作
            $label_list .= str_replace($search, $replace, $template);
            $message_list .= str_replace($search_message, $replace_message, $template_message);
        }
        
        //读取模版
        $template = file_get_contents(APP_PATH.'admin/code/validate.php');
        $search = ['%validate%','%validateField%','%validateMessage%'];
        $replace = [$validate,$label_list,$message_list];
        $file = APP_PATH.'admin/validate/'.$validate.'.php';
        $contents = str_replace($search, $replace, $template);
        file_put_contents($file, $contents);
        echo '验证器：', $file, '生成完毕', '<br>';
    }
    
    
    
    

    public function mkModel() {
        //代码复用
        if (!isset($this->label['model'])) {
            //implode()将数组元素组合为字符串
            //array_map()遍历数组
            //ucfirst首字母大写
            //explode以第一个参数为分割 将第二个参数内容分割为数组
            $this->label['model'] = implode(array_map('ucfirst', explode('_', $this->input['table'])));
        }
        return $this->label['model'];
    }

    public function mkController() {
        //代码复用
        if (!isset($this->label['controller'])) {
            //implode()将数组元素组合为字符串
            //array_map()遍历数组
            //ucfirst首字母大写
            //explode以第一个参数为分割 将第二个参数内容分割为数组
            $this->label['controller'] = implode(array_map('ucfirst', explode('_', $this->input['table']))) . 'Controller';
        }
        return $this->label['controller'];
    }

    public function mkValidate() {
        //代码复用
        if (!isset($this->label['validate'])) {
            //implode()将数组元素组合为字符串
            //array_map()遍历数组
            //ucfirst首字母大写
            //explode以第一个参数为分割 将第二个参数内容分割为数组  
            $this->label['validate'] = implode(array_map('ucfirst', explode('_', $this->input['table']))) . 'Validate';
        }
        return $this->label['validate'];
    }

}