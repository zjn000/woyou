<?php
namespace app\api\controller\order;

use app\common\controller\Api;
use think\Request;
use think\Db;

class Orderdetail extends Api{

    /**
     * 获取订单详情
     * @param  int $oid 订单id
     */
    public function index(){

        $oid = intval(Request::instance()->post('oid'));

        if(empty($oid)){
            $this->jsonReturn(400,'缺少参数订单id');
        }

        $obj = Db::name('order')->field('id,o_no,all_total,total,discount,status,create_time')->where('id',$oid)->find();

        if(empty($obj)){
            $this->jsonReturn(404,'没有此订单');
        }

        $data['o_no'] = $obj['o_no'];
        $data['create_time'] = date('Y-m-d H:i:s',$obj['create_time']);
        $data['status'] = $obj['status'] == 1 ? '支付成功':'支付失败';
        $data['total'] = $obj['total'];
        $data['all_total'] = $obj['all_total'];
        $data['discount'] = $obj['discount'];

        //获取列表订单的所有订单商品集合
        $deatil_list = Db::name('order_detail')->field('oid,name,price,num')->where('oid',$obj['id'])->select();

        if(!empty($deatil_list)){
            //商品集合组
            foreach ($deatil_list as $item){
                $data['goods'][] = $item;
            }
        }

        $this->jsonReturn(200,'成功',$data);

    }




}