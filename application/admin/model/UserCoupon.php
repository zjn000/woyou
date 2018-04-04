<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class UserCoupon extends Model
{
    // 表名
    protected $name = 'user_coupon';

    public function coupon(){
        return $this->hasOne('Coupon', 'id','coupon_id')->setEagerlyType(0);
    }

    public function user(){
        return $this->hasOne('User', 'id','uid')->setEagerlyType(0);
    }

}
