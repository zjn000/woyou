<?php
namespace app\tools\controller;

use app\common\controller\Toolsapi;
use think\Db;
use think\Request;
use think\Session;

class Product extends Toolsapi{


    /**
     * 获取货架下商品列表
     * GET
     * @param integer   p    页码
     * @param integer   sid  货架Id
     * @param string    sign 签名
     */
    public function get_list(){

        $param = Request::instance()->route();

        if(empty($param['sign']) || empty($param['p']) || empty($param['sid'])){
            send_json(0,'参数异常');
        }

        $page = intval($param['p']) ? intval($param['p']) : 1;
        $sid = intval($param['sid']);

        if($this->check_sign($param['sign'],$param) === false){
            send_json(0,'数据包异常');
        }

        if($this->check_time('tools_product_get_list',2) === false){
            send_json(0,'操作太频繁');
        }


        $data = Db::name('shelves_product')->alias('a')
            ->field('a.id,a.shelves_id,a.product_id,a.standard_number,a.lv,a.num,b.name,b.image')
            ->where("a.shelves_id={$sid} and a.status=1")
            ->order("a.lv asc")
            ->join('__PRODUCT__ b','a.product_id=b.id')->page($page,10)
            ->select();


        $this->set_check_time('tools_product_get_list');

        send_json(1,'成功',$data);

    }

    /**
     * 搜索商品
     * GET
     * @param integer   p    页码
     * @param integer   sid  货架Id
     * @param string    s    关键字
     * @param string    sign 签名
     */
    public function search(){

        $param = Request::instance()->route();

        if(empty($param['sign']) || empty($param['p']) || empty($param['sid']) || empty($param['s'])){
            send_json(0,'参数异常');
        }

        $page = intval($param['p']) ? intval($param['p']) : 1;
        $sid = intval($param['sid']);
        $name = trim($param['s']);

        if($this->check_sign($param['sign'],$param) === false){
            send_json(0,'数据包异常');
        }

        if($this->check_time('tools_product_search',2) === false){
            send_json(0,'操作太频繁');
        }

        $data = Db::name('shelves_product')->alias('a')
            ->field('a.id,a.shelves_id,a.product_id,a.standard_number,a.lv,a.num,b.name,b.image')
            ->where("a.shelves_id={$sid} and a.status=1")->where("b.name like '%{$name}%'")
            ->order("a.lv asc")
            ->join('__PRODUCT__ b','a.product_id=b.id')->page($page,10)
            ->select();


        $this->set_check_time('tools_product_search');

        send_json(1,'成功',$data);
    }

    /**
     * 盘点保存
     * POST
     * @param array 参数
     * array = [
     *      id           货架商品ID
     *      product_id      商品ID
     *      shelves_id      货架ID
     *      real_amount     盘点数量
     *      add_num         上架数量
     *      revise          下架数量
     *      standard_number 标准数量
     * ];
     * GET
     * @param string sign 签名
     */
    public function save(){
        $request = Request::instance();

        $params = $request->post();
        if ($params)
        {
            $param = $request->route();

            if(empty($param['sign'])){
                send_json(0,'参数异常');
            }

            if($this->check_sign($param['sign'],$params) === false){
                send_json(0,'数据包异常');
            }

            if($this->check_time('tools_product_save',2) === false){
                send_json(0,'操作太频繁');
            }

            $where = [
                'id'    =>  intval($params['id']),
                'product_id'    =>  intval($params['product_id']),
                'shelves_id'    =>  intval($params['shelves_id']),
                'status'    =>  1
            ];

            $row = Db::name('shelves_product')->field('id,num,standard_number')->where($where)->find();

            if(empty($row)){
                send_json(0,'参数错误');
            }

            //货架名称
            $s_name = Db::name('shelves')->where(['id'=>$where['shelves_id']])->value('name');

            //商品信息
            $product = Db::name('product')->field('id,name,barcode,inventory')->where(['id'=>$where['product_id']])->find();


            $real_amount    = intval($params['real_amount']);
            $add_num        = intval($params['add_num']);
            $revise         = intval($params['revise']);
            $loss           = $row['num']-$real_amount;
            $after_remaining = $add_num+$real_amount-$revise;

            $data = array(
                'sid' => $where['shelves_id'],
                's_name' => $s_name,
                'p_name' => $product['name'],
                'p_barcode' => $product['barcode'],
                'before_remaining' => $row['num'],
                'real_amount' => $real_amount,
                'after_remaining' => $after_remaining,
                'loss' => $loss,
                'add_num' => $add_num,
                'revise' => $revise,
                'admin_id' => Session::get('admin')['id'],
                'create_time' => time()
            );

            if($add_num>$product['inventory']){
                send_json(0,'库存不足');
            }

            //增加补货记录
            Db::name('replenishment_records')->insert($data);


            //更新商品总库存
            $update_product_data = [
                'id'        =>  $product['id'],
                'inventory' =>  $product['inventory']-$add_num+$revise,
                'update_id' =>  Session::get('admin')['id'],
                'update_time' =>  time()
            ];
            Db::name('product')->update($update_product_data);


            //更新货架下商品标准数量以及剩余数量
            $update_shelves_product_data = [
                'id'    =>  $row['id'],
                'num'   =>  $after_remaining,
                'standard_number'   =>  intval($params['standard_number']),
                'update_id' =>  Session::get('admin')['id'],
                'update_time' =>  time()
            ];
            Db::name('shelves_product')->update($update_shelves_product_data);


            $this->set_check_time('tools_product_save');

            send_json(1,'成功',$update_shelves_product_data);

        }

        send_json(0,'缺少参数');

    }

}