<?php
namespace app\api\controller;

use app\common\controller\Publicapi;
use wxpay\Notify;

class Wxnotify extends Publicapi{
    // 通知测试
    public function index()
    {
        $notify = new Notify();
        $notify->Handle();
    }

}