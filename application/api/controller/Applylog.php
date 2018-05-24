<?php
namespace app\api\controller;

use app\common\controller\Publicapi;
use think\Db;
use think\Request;


class Applylog extends Publicapi{

    public function index(){

        $result = $this->check_frequency('api_applylog_index',10,3);

        if($result === false){
            $this->jsonReturn(500,'操作太频繁，请10秒后再进行操作');
        }

        //初始化
        $request = Request::instance();

        //参数
        $param = array(
            'contact'   => trim($request->post('contact')),
            'phone' => trim($request->post('phone')),
            'company' => trim($request->post('company')),
            'num' => intval($request->post('num')),
            'address' => trim($request->post('address')),
            'create_time'=>time()
        );

        $rs = Db::name('apply_log')->insertGetId($param);

        if(!$rs){
            $this->jsonReturn(500,'系统繁忙');
        }

        $this->jsonReturn(200,'成功');
    }


}