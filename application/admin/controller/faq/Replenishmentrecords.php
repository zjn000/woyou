<?php

namespace app\admin\controller\faq;

use app\common\behavior\Common;
use app\common\controller\Backend;

use think\Controller;
use think\Request;
use app\admin\model\Admin;

/**
 * 补货记录管理
 *
 * @icon fa fa-circle-o
 */
class Replenishmentrecords extends Backend
{
    
    /**
     * ReplenishmentRecords模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('ReplenishmentRecords');

    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $adminName = Admin::where('')->column('id,nickname');

            foreach ($list as $k => $v)
            {
                $list[$k]['admin_id'] = isset($adminName[$v['admin_id']]) ? $adminName[$v['admin_id']] : '';
            }
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

        $list = $this->model
            ->where($where)
            ->order($sort, $order)
            ->select();


        $adminName = Admin::where('')->column('id,nickname');

        $headList = ['ID','货架名称','商品名称','商品条形码','剩余数量（补货前）','盘点数量','亏损数量','上架数量','下架数量','剩余数量（补货后）','操作人','操作时间'];

        $data = [];
        //注意：纯数字超过11位变科学计数法，超过15位则其后数字将变为0，解决方案，在数据前加上"\t"将其变成字符串
        foreach ($list as $key=>$row){

            $row['admin_id'] = isset($adminName[$row['admin_id']]) ? $adminName[$row['admin_id']] : '';

            $data[$key] = [
                $row['id'],
                $row['s_name'],
                $row['p_name'],
                "\t".$row['p_barcode'],
                $row['before_remaining'],
                $row['real_amount'],
                $row['loss'],
                $row['add_num'],
                $row['revise'],
                $row['after_remaining'],
                $row['admin_id'],
                "\t".date('Y-m-d H:i:s',$row['create_time'])
            ];
        }

        Common::exportToCsv('补货记录_'.date('Y-m-d').'.csv',$headList,$data);
    }

}
