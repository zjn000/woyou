<?php

namespace app\admin\model;

use think\Model;

class Order extends Model
{
    // 表名
    protected $name = 'order';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';


    public function shelves(){
        return $this->belongsTo('Shelves', 'sid','id')->setEagerlyType(0);
    }



}
