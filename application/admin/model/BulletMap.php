<?php

namespace app\admin\model;

use think\Model;

class BulletMap extends Model
{
    // 表名
    protected $name = 'bullet_map';

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



}
