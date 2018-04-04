<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\Shelves;
use app\admin\model\Product;
use think\Controller;
use think\Request;
use Think\Session;

/**
 * 货架商品管理
 *
 * @icon fa fa-circle-o
 */
class Shelvesproduct extends Backend
{

    /**
     * ShelvesProduct模型对象
     */
    protected $model = null;
    //无需要权限判断的方法
    protected $noNeedRight = ['select_product'];


    protected $multiFields = 'status,lv';

    //开启关联查询
    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('ShelvesProduct');
    }

    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $orderby = array(
                $sort => $order,
                config('database.prefix').'shelves_product.lv' => 'asc'
            );

            //获取参数shelves_id：货架的id进行数据的筛选
            $arrWhere = [];
            if($this->request->param('shelves_id') != null){
                $arrWhere['shelves_product.shelves_id'] = $this->request->param('shelves_id');
            }

            $total = $this->model->with("Shelves,Product")
                ->where($where)->where($arrWhere)
                ->order($orderby)
                //->order($sort, $order)
                ->count();

            $list = $this->model->with("Shelves,Product")
                ->where($where)->where($arrWhere)
                ->order($orderby)
                //->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            //层数名称
            $lv_name_list = [
                0=>'',
                1=>'第1层',
                2=>'第2层',
                3=>'第3层',
                4=>'第4层',
                5=>'第5层',
                6=>'冰箱1层',
                7=>'冰箱2层',
                8=>'冰箱3层',
                9=>'冰箱4层'
            ];

            if(!empty($list)){
                foreach ($list as $key=>$item){
                    $list[$key]['should_replenish']= $item['standard_number']-$item['num'];
                    $list[$key]['lv'] = $lv_name_list[$item['lv']];
                }
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }

        return $this->view->fetch();
    }


    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {

                $data = array(
                    'sid' => $row['shelves_id'],
                    's_name' => $params['s_name'],
                    'p_name' => $params['p_name'],
                    'p_barcode' => $params['p_barcode'],
                    'before_remaining' => $params['before_remaining'],
                    'real_amount' => $params['real_amount'],
                    'after_remaining' => $params['add_num']+$params['real_amount']-$params['revise'],
                    'loss' => $params['before_remaining']-$params['real_amount'],
                    'add_num' => $params['add_num'],
                    'revise' => $params['revise'],
                    'admin_id' => $this->auth->id,
                    'create_time' => time()
                );

                $inventory = db('product')->where(array('id'=>$row['product_id']))->value('inventory');

                if($data['add_num']>$inventory){
                    $this->error(__('库存不足'));
                }

                //增加补货记录
                model('ReplenishmentRecords')->create($data);

                //更新商品总库存
                db('product')->where(array('id'=>$row['product_id']))->update(array('inventory'=>$inventory-$data['add_num']+$data['revise']));

                $rs = $row->save(array('id'=>$ids,'num'=>$data['after_remaining'],'standard_number'=>$params['standard_number']));

                if ($rs !== false)
                {
                    $this->success();
                }
                else
                {
                    $this->error($row->getError());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $shelves = Shelves::get($row['shelves_id']);
        $product = Product::get($row['product_id']);


        $this->view->assign("row", $row);
        $this->view->assign("shelves", $shelves);
        $this->view->assign("product", $product);
        return $this->view->fetch();
    }



    /**
     * 删除
     */
    public function del($ids = "")
    {

        if ($ids)
        {
            $deleteIds = explode(',',$ids);
            $rs = $this->model->destroy($deleteIds);

            if($rs){
                $this->success();
            }
        }
        $this->error();
    }




    /**
     * 批量添加选中商品
     */
    public function select_product()
    {
        $sid = $this->request->param("sid");
        $pids = $this->request->param("pids");

        if(empty($sid)){
            $this->error(__('Parameter %s can not be empty', 'sid'));
        }

        if(empty($pids)){
            $this->error(__('Parameter %s can not be empty', 'pids'));
        }

        $arrPids = explode(',',$pids);

        foreach ($arrPids as $key=>$row){
            $data[$key]=array(
                'shelves_id' => intval($sid),
                'product_id' => intval($row),
                'lv'=> 0,
                'status'=>1
            );
        }

        $rs = $this->model->saveAll($data);

        if($rs === false)
        {
            $this->error(__('You have no permission'));
        }

        $this->success();

    }

}
