<?php
namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use think\Db;

/**
 * 商品销售统计
 */
class Productsales extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('order_help');
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

            $total = Db::name('order_help')
                ->field('p_name,p_barcode,sum(p_num) num')->where($where)
                ->group('p_barcode')
                ->count();

            $list = Db::name('order_help')
                ->field('p_name,p_barcode,sum(p_num) num')->where($where)
                ->group('p_barcode')
               // ->order($sort, $order)
                ->order(['num'=>'desc','id'=>'desc'])
                ->limit($offset, $limit)
                ->select();

            

            foreach ($list as $k => $v)
            {
                $list[$k]['create_time'] = '--';
            }

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

}