<?php
namespace app\api\controller\order;

use app\common\controller\Api;
use think\Db;
use think\Session;
use think\Request;

class Orderlist extends Api{

    /**
     * 获取订单列表
     * @param  int $oid 订单id
     */
    public function index(){

        $user = Session::get('userInfo');

        if(!isset($user['id'])){
            $this->jsonReturn(401,'用户id参数错误');
        }

        //页码
        $p = intval(Request::instance()->post('p'));

        $list = Db::name('order')->field('id,uid,sid,o_no,total,status,create_time')->where('uid',$user['id'])->order('id desc')->page($p,10)->select();

        $data = [];
        if(!empty($list)){

            //获取返回数据中sid列的值
            $sids_list = array_column($list,'sid');
            //去重
            $sids = array_unique($sids_list);

            //获取列表货架集合
            $shelves_list = Db::name('shelves')->field('id,name')->where('id','IN',$sids)->select();

            //货架集合组
            foreach ($shelves_list as $value){
                $shelves[$value['id']]=$value['name'];
            }

            //组合数据
            foreach ($list as $key=>$rows){
                $data[$key]['oid'] = $rows['id'];
                $data[$key]['s_name'] = $shelves[$rows['sid']];
                $data[$key]['status'] = $rows['status'] == 1 ? '支付成功':'支付失败';
                $data[$key]['create_time'] = date('Y-m-d H:i:s',$rows['create_time']);
                $data[$key]['total'] = $rows['total'];
            }

            //获取列表订单id集合
            $oids = array_column($data,'oid');

            //获取列表订单的所有订单商品集合
            $deatil_list = Db::name('order_detail')->field('oid,name,num')->where('oid','IN',$oids)->select();

            //商品集合组
            foreach ($deatil_list as $item){
                $goods[$item['oid']][] = $item;
            }

            //组合数据
            foreach ($list as $key=>$rows){
                $data[$key]['goods'] = $goods[$rows['id']];
            }

        }

        $this->jsonReturn(200,'成功',$data);

    }




}