<?php

namespace app\admin\model;

use think\Model;

class OrderHelp extends Model
{
    // 表名
    protected $name = 'order_help';

    public function shelves(){
        return $this->belongsTo('Shelves', 'sid','id')->setEagerlyType(0);
    }

}
