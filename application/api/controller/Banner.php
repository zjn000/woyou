<?php
namespace app\api\controller;

use app\common\controller\Publicapi;
use think\Db;

class Banner extends Publicapi{

    public function index(){

        $rs = Db::name('banner')->where(['status'=>1])->order(['sorting'=>'desc'])->select();
        if(!$rs){
            $this->jsonReturn(500,'系统繁忙');
        }

        $this->jsonReturn(200,'成功',$rs);
    }


}