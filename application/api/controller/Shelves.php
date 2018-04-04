<?php
namespace app\api\controller;

use app\common\controller\Api;
use fast\Http;
use think\Db;
use think\Request;

class Shelves extends Api{

    /**
     * 根据货架id获取货架信息
     */
    public function detail(){

        $id = Request::instance()->post('id');

        if(empty($id)){
            $this->jsonReturn(400,'缺少参数id');
        }


        $obj = Db::name('shelves')->where(array('id'=>intval($id),'status'=>1))->field('id,name')->find();

        if(empty($obj)){
            $this->jsonReturn(404,'当前货架不存在');
        }

        $this->jsonReturn(200,'成功',$obj);

    }






}