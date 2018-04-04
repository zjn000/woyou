<?php

namespace app\admin\model;

use think\Model;

class ProductCategory extends Model
{
    // 表名
    protected $name = 'product_category';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    protected $insert = ['create_id'];
    protected $update = ['update_id'];

    protected function setCreateIdAttr()
    {
        $admin = \think\Session::get('admin');
        $admin_id = $admin ? $admin->id : 0;
        return $admin_id;

    }

    protected function setUpdateIdAttr()
    {
        $admin = \think\Session::get('admin');
        $admin_id = $admin ? $admin->id : 0;
        return $admin_id;
    }

    // 追加属性
    protected $append = [
        'create_time_text',
        'update_time_text'
    ];
    
    public function getCreatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['create_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getUpdatetimeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['update_time'];
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setCreatetimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setUpdatetimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


}
