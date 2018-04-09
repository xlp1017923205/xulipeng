<?php

namespace app\Admin\model;
use think\Model;

class Category extends Model{
    
      public function getTree(){
        $rows = $this->select();
        $tree = $this->tree($rows,0,0);
        return $tree;
    }
    //level当前树的级别
    public function tree($rows,$id=0,$level=0){
        //存放结果集
        static $tree = [];
        //遍历结果
        foreach($rows as $row){
            if($id == $row['parent_id']){
                $row['level'] = $level;
                $tree[] = $row;
                //递归，在方法中自己调用自己
                $this->tree($rows,$row['id'],$level+1);
            }
        }
        return $tree;
    }
    
    
    
    
}

