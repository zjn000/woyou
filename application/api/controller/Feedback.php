<?php
namespace app\api\controller;

use app\common\controller\Api;
use think\Db;
use think\Request;
use think\Session;

class Feedback extends Api{

    public function index(){

        //初始化
        $request = Request::instance();

        //参数
        $param = array(
            'uid'   => intval(Session::get('userInfo')['id']),
            'name' => trim($request->post('name')),
            'phone' => trim($request->post('phone')),
            'content' => trim($request->post('content')),
            'create_time'=>time()
        );

        $rs = Db::name('feedback')->insertGetId($param);

        if(!$rs){
            $this->jsonReturn(500,'系统繁忙');
        }

        $this->jsonReturn(200,'成功');
    }


}