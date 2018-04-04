<?php

namespace app\admin\controller\goods;

use app\admin\model\Supplier;
use app\common\controller\Backend;
use think\Request;
use app\admin\model\ShelvesProduct;
use app\admin\model\ProductCategory;

/**
 * 商品总库管理
 *
 * @icon fa fa-circle-o
 */
class Product extends Backend
{
    
    /**
     * Product模型对象
     */
    protected $model = null;

    protected $modelValidate = true;

    //无需要权限判断的方法
    protected $noNeedRight = ['getCategoryList'];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Product');

        $ProductCategoryList = ProductCategory::where('status=1')->column('id,name');
        $this->view->assign('ProductCategoryList',$ProductCategoryList);
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
                ->order('status desc,id desc')
                //->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->order('status desc,id desc')
                //->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $pcList = $this->view->ProductCategoryList;

            foreach ($list as $k => &$v)
            {
                $v['fullurl'] = $v['image'];
                $list[$k]['type'] = isset($pcList[$v['type']]) ? $pcList[$v['type']] : '';
            }
            unset($v);

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }

    /**
     * 选择
     */
    public function select()
    {
        if ($this->request->isAjax())
        {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $arrWhere['status'] = 1;
            if(Request::instance()->param('shelves_id') != null){
                $productIds = ShelvesProduct::where('shelves_id', '=',Request::instance()->param('shelves_id'))
                    ->column('product_id');

                if(!empty($productIds)){
                    $arrWhere['id'] = array('NOT IN',$productIds);
                }
            }


            $total = $this->model
                ->where($where)->where($arrWhere)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)->where($arrWhere)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $pcList = $this->view->ProductCategoryList;
            foreach ($list as $k => &$v)
            {
                $v['fullurl'] = $v['image'];
                $list[$k]['type'] = isset($pcList[$v['type']]) ? $pcList[$v['type']] : '';
            }
            unset($v);

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 采购入库
     */
    public function purchase($ids = null){
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));

        $supplierList = Supplier::where('status=1')->order('id desc')->column('id,name');

        if (!$supplierList)
            $this->error(__('没有可选供应商'));

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {
                $data = array(
                    'name' => $row['name'],
                    'barcode' => $row['barcode'],
                    'sup_id' => $params['sup_id'],
                    'sup_name' => $supplierList[$params['sup_id']],
                    'num' => $params['purchase_num'],
                    'price' => $params['purchase_price'],
                    'total' => $params['purchase_price']*$params['purchase_num'],
                    'purchase_time' => strtotime($params['purchase_time']),
                    'create_id' => $this->auth->id,
                    'create_time' => time(),
                    'inventory' => $row['inventory']+$params['purchase_num']
                );


                model('PurchaseGoods')->create($data);

                $rs = $row->save(array('id'=>$ids,'inventory'=>$data['inventory']));

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

        $this->view->assign("supplierList", $supplierList);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 退货出库
     */
    public function returns($ids = null){
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));

        $supplierList = Supplier::where('status=1')->order('id desc')->column('id,name');

        if (!$supplierList)
            $this->error(__('No Results were found'));

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {
                $data = array(
                    'name' => $row['name'],
                    'barcode' => $row['barcode'],
                    'sup_id' => $params['sup_id'],
                    'sup_name' => $supplierList[$params['sup_id']],
                    'num' => $params['returned_num'],
                    'price' => $params['returned_price'],
                    'total' => $params['returned_price']*$params['returned_num'],
                    'returned_time' => strtotime($params['returned_time']),
                    'create_id' => $this->auth->id,
                    'create_time' => time(),
                    'inventory' => $row['inventory']-$params['returned_num']
                );


                model('ReturnGoods')->create($data);

                $rs = $row->save(array('id'=>$ids,'inventory'=>$data['inventory']));

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

        $this->view->assign("supplierList", $supplierList);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


    /**
     * 获取商品分类
     */
    public function getCategoryList(){
        return ProductCategory::where('status=1')->column('id,name');
    }


}
