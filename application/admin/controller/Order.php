<?php

namespace app\admin\controller;

use app\admin\model\OrderDetail;
use app\common\behavior\Common;
use app\common\controller\Backend;
use app\admin\model\Shelves;
use think\Controller;
use think\Request;

/**
 * 订单管理
 *
 * @icon fa fa-circle-o
 */
class Order extends Backend
{

    /**
     * Order模型对象
     */
    protected $model = null;
    protected $noNeedRight = ['detail'];

    //开启关联查询
    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Order');

    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model->with("Shelves")
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->with("Shelves")
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = array("total" => $total, "rows" => $list,'export_url'=>str_replace('index','csv',$this->request->url()));

            return json($result);
        }

        return $this->view->fetch();
    }


    /**
     * csv导出
     */
    public function csv(){

        list($where, $sort, $order, $offset, $limit) = $this->buildparams();

        $list = $this->model->with("Shelves")
            ->where($where)
            ->order($sort, $order)
            ->select();


        $headList = ['ID','用户id','货架名称','订单号','订单总额','优惠金额','实付金额','订单状态','创建时间'];


        //注意：订单号超过11位变科学计数法，超过15位则其后数字将变为0，解决方案，在数据前加上"\t"将其变成字符串
        foreach ($list as $key=>$row){

            $row['status'] = $row['status'] == 1 ? '支付成功':"取消/支付失败";

            $data[$key] = [
                $row['id'],
                $row['uid'],
                $row['shelves']['name'],
                "\t".$row['o_no'],
                $row['all_total'],
                $row['discount'],
                $row['total'],
                $row['status'],
                "\t".date('Y-m-d H:i:s',$row['create_time'])
            ];
        }

        Common::exportToCsv('订单_'.date('Y-m-d').'.csv',$headList,$data);
    }



    /**
     * 订单商品详情
     */
    public function detail($ids)
    {
        $details = OrderDetail::where('oid',$ids)->select();

        if (!$details)
            $this->error(__('No Results were found'));

        $this->view->assign("data", $details);
        return $this->view->fetch();
    }

}
