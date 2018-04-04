<?php
namespace app\api\controller;

use app\common\controller\Api;
use think\Request;
use think\Session;

class Coupon extends Api{

    /**
     * 获取用户所有有效的优惠券
     */
    public function get_list(){
        $user = Session::get('userInfo');
        $data = model('UserCoupon')->getValidCoupon($user['id']);

        $this->jsonReturn(200,'成功',$data);
    }

    /**
     * 获取指定金额下用户的有效优惠券
     */
    public function get_all(){

        $user = Session::get('userInfo');

        $price = floatval(Request::instance()->post('price'));

        if(empty($price) || $price < 0.01){
            $this->jsonReturn(400,'订单金额错误');
        }

        $data = model('UserCoupon')->getValidCoupon($user['id'],$price);

        $this->jsonReturn(200,'成功',$data);

    }

    /**
     * 获取指定金额下用户优惠折扣最大的一张有效优惠券
     */
    public function get_best_one(){
        $user = Session::get('userInfo');

        $price = floatval(Request::instance()->post('price'));

        if(empty($price) || $price < 0.01){
            $this->jsonReturn(400,'订单金额错误');
        }

        $data = model('UserCoupon')->getValidCoupon($user['id'],$price,false);

        $this->jsonReturn(200,'成功',$data);
    }




}