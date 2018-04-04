<?php
namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use think\Db;

/**
 * 货架-商品销售统计
 */
class Shelvestogoods extends Backend
{
    protected $model = null;

    //开启关联查询
    protected $relationSearch = true;

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

            $total = $this->model->with("Shelves")
                ->where($where)->group('sid,p_barcode')
                ->order($sort, $order)
                ->count();

            $list = $this->model->with("Shelves")
                ->field('order_help.id,sid,p_name,p_barcode,sum(p_num) num,sum(p_total) total')
                ->where($where)->group('sid,p_barcode')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $p_total = array_column($list,'total');
            $p_num = array_column($list,'num');
            //当前页面商品销售总额
            $current_page_total = array_sum($p_total);
            //当前页面商品销售总数
            $current_page_num = array_sum($p_num);

            foreach ($list as $k => $v)
            {
                $list[$k]['create_time'] = '--';
            }

            $result = array("total" => $total, "rows" => $list,'pageNum'=>$current_page_num,'pageTotal'=>$current_page_total);

            return json($result);
        }
        return $this->view->fetch();
    }

}