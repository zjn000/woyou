<?php
namespace app\api\controller\product;

use app\common\controller\Api;
use think\Request;

class Productdetail extends Api{

    /**
     * 根据商品条形码获取商品信息
     * @param int $sid 货架id
     * @param string $barcode 商品条形码
     * @return
     */
    public function index(){


        $request = Request::instance();

        $sid = intval($request->post('sid'));

        if(empty($sid)){
            $this->jsonReturn(400,'请先扫描货架');
        }

        $code = trim($request->post('barcode'));

        if(empty($code)){
            $this->jsonReturn(400,'请先扫描商品条形码');
        }

        //总库商品信息
        $p_obj = db('product')->where('barcode',$code)->field('id,name,barcode,price,member_price,image')->find();

        if(empty($p_obj)){
            $this->jsonReturn(404,'非本公司商品');
        }

        //当前货架商品的信息
        $obj_d = db('shelves_product')->where(array('product_id'=>$p_obj['id'],'shelves_id'=>$sid))->field('num,status')->find();

        if(empty($obj_d)){
            $this->jsonReturn(404,'非当前货架商品');
        }

        if($obj_d['num']<1){
            $this->jsonReturn(404,'当前货架的此商品已经没有了');
        }

        if($obj_d['status'] == 2){
            $this->jsonReturn(404,'商品已下架');
        }

        $p_obj['num'] = $obj_d['num'];

        $this->jsonReturn(200,'成功',$p_obj);

    }


}