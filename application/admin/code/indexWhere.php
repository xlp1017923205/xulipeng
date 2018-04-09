

 //判断搜索条件中是否具有%field%条件
        $%field% = input('%field%','');//获取页面传来的值
        if(''!='%$field%'){
            //这里是将用户输入的内容添加到查询中，得要查询后的数据
            $model->where('%field%','like','%'.$%field%.'%');
            //将查询条件放回到分页中，防止点击下一页时，页面内容回到第一页
            $field['%field%']=$%field%;
            //这个操作为了搜索栏的回显
            $this->assign('%field%',$%field%);
        }
