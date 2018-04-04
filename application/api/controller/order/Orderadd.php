<?php
namespace app\api\controller\order;

use app\common\controller\Api;
use think\Db;
use think\Request;
use app\common\behavior\Common;
use think\Session;
use think\Exception;

class Orderadd extends Api{

    public function index(){

        //初始化
        $request = Request::instance();

        //获取用户id
        $userid = Session::get('userInfo')['id'];
        //货架id
        $sid = intval($request->post('sid'));

        //判断参数
        if(empty($sid)){
            $this->jsonReturn(400,'货架参数错误');
        }
        //获取货架下有效的（即上架）商品信息
        $shelves_product_list = db('shelves_product')->where(array('shelves_id'=>$sid,'status'=>1))->field('product_id')->select();

        //判断货架是否有商品
        if(empty($shelves_product_list)){
            $this->jsonReturn(404,'没有此货架或商品已下架');
        }

        //获得货架下有效的商品id集合
        foreach ($shelves_product_list as $shelves_product){
            $s_product_ids[] = $shelves_product['product_id'];
        }

        //商品集合
        $goods = $request->post('goods/a'); //接收类型为数组，需要附加/a指定类型

        //判断商品参数有效性
        if(empty($goods) || !is_array($goods)){
            $this->jsonReturn(400,'商品参数错误');
        }

        foreach ($goods as $key=>$row){
            $good_ids[] = $row['id'];
            $good[$row['id']] = $row['num'];
        }

        //去重，获得购买品的id集合
        $pids = array_unique($good_ids);

        $rs = array_diff($pids,$s_product_ids);

        //若购买品id集合与货架商品id集合有差集，则购买品中有非法id
        if(!empty($rs)){
            $this->jsonReturn(400,'非法商品id');
        }

        //获得购物品相应信息
        $product_list = db('product')->where('id','IN',$pids)->field('id,name,barcode,price,image')->select();

        foreach ($product_list as $key=>$product){
            $total_list[$key] = $product['price']*$good[$product['id']];

            //订单商品详情参数
            $arrParam[$key]=array(
                'goodid' => $product['id'],
                'name' => $product['name'],
                'barcode' => $product['barcode'],
                'image' => $product['image'],
                'price' => $product['price'],
                'num' => $good[$product['id']]
            );


        }
        //订单总额
        $all_total = array_sum($total_list);


        //用户优惠券id
        $c_id = intval($request->post('c'));
        //优惠金额
        $discount = 0;

        if($c_id<0){
            $this->jsonReturn(400,'非法优惠券');
        }

        //判断参数
        if($c_id > 0){

            $coupon_list = model('UserCoupon')->getValidCoupon(intval($userid),$all_total);

            if(empty($coupon_list)){
                $this->jsonReturn(400,'非法优惠券');
            }

            //先array_column取出id列做键名数组，然后用array_combine合并,id必须唯一
            $new_coupon_list = array_combine(array_column($coupon_list, 'id'), $coupon_list);

            if (isset($new_coupon_list[$c_id])){
                //使用优惠券的优惠金额
                $discount = floatval($new_coupon_list[$c_id]['num2']);
            }else{
                $this->jsonReturn(400,'没有该优惠券');
            }

        }


        //订单参数
        $param = array(
            'uid'   => intval($userid),
            'sid'   => intval($sid),
            'o_no'  => Common::build_order_no(),
            'all_total' => $all_total,
            'uc_id' => $c_id,
            'discount' => $discount,
            'total' => $all_total-$discount,
            'status'=>  0,
            'create_time'=>time()
        );

        // 启动事务
        Db::startTrans();
        try{
            //添加订单，返回订单id
            $oid = Db::name('order')->insertGetId($param);

            foreach ($arrParam as $key=>$item){
                $arrParam[$key]['oid'] = $oid;
            }

            //添加订单商品详情
            Db::name('order_detail')->insertAll($arrParam);

            // 提交事务
            Db::commit();
        } catch (Exception $e) {
            // 回滚事务
            Db::rollback();
            $this->jsonReturn(500,'系统繁忙',$oid);
        }

        $this->jsonReturn(200,'成功',$oid);
    }


}