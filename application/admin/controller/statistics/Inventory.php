<?php
namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use think\Db;

/**
 * 库存统计
 */
class Inventory extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Product');
    }

    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();

            $total = $this->model->where(['status'=>1])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model->field('id,name,barcode,type,inventory')->where(['status'=>1])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $key_ids = array_column($list,'id');


            //统计数据
            $countList = Db::name('shelves_product')
                ->field('product_id,sum(num) num,sum(standard_number) standard_number')
                ->where(['status'=>1,'product_id'=>['IN',$key_ids]])
                ->group('product_id')
                ->select();

            //将商品ID作为键名
            $key_pids = array_column($countList,'product_id');
            $count_list = array_combine($key_pids,$countList);

            $ProductCategoryList = Db::name('product_category')->where(['status'=>1])->column('id,name');

            foreach($list as $key=>$row){
                if(isset($count_list[$row['id']])){
                    $list[$key]['num'] = $count_list[$row['id']]['num'];
                    $list[$key]['standard_number'] = $count_list[$row['id']]['standard_number'];
                }else{
                    $list[$key]['num'] = 0;
                    $list[$key]['standard_number'] = 0;
                }
                $list[$key]['type'] = isset($ProductCategoryList[$row['type']]) ? $ProductCategoryList[$row['type']] : '';
                $list[$key]['total'] = $list[$key]['num']+$row['inventory'];
            }



            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

}