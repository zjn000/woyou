<?php
namespace app\api\controller;

use app\common\controller\Publicapi;
use think\Db;

class Bulletmap extends Publicapi{

    public function index(){

        $rs = Db::name('bullet_map')->field('id,image')->where(['status'=>1])->order(['id'=>'desc'])->limit(1)->select();

        if(!empty($rs)){
            $rs = $rs[0];
        }

        $this->jsonReturn(200,'成功',$rs);
    }


}