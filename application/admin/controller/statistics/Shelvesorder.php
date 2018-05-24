<?php
namespace app\admin\controller\statistics;

use app\common\controller\Backend;
use think\Db;

/**
 * 货架订单统计
 */
class Shelvesorder extends Backend
{
    protected $model = null;

    //开启关联查询
    protected $relationSearch = true;

    public function _initialize()
    {
        parent::_initialize();
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

            //取出总数
            $total = Db::name('shelves')->count('id');

            //取出货架信息
            $shelvesList = Db::name('shelves')->field('id,name,status')->select();;

            //根据时间段取出订单统计数据
            $orderList = Db::name('order')
                ->field('sid,count(id) num,sum(total) total,sum(discount) discount')->where(['status'=>1])
                ->where($where)->group('sid')
                ->select();

            //将货架ID作为键名
            $key_sid = array_column($orderList,'sid');
            $order_param = array_combine($key_sid,$orderList);


            //循环判断，赋值
            foreach ($shelvesList as $key=>$item){

                if(isset($order_param[$item['id']])){
                    $shelvesList[$key]['num'] = $order_param[$item['id']]['num'];
                    $shelvesList[$key]['total'] = $order_param[$item['id']]['total'];
                    $shelvesList[$key]['discount'] = $order_param[$item['id']]['discount'];
                }else{
                    $shelvesList[$key]['num'] = 0;
                    $shelvesList[$key]['total'] = '0.00';
                    $shelvesList[$key]['discount'] = '0.00';
                }

            }

            //取出排序值列
            $sort_arr = array_column($shelvesList,$sort);

            //排序
            if($order === 'asc'){
                array_multisort($sort_arr,SORT_ASC,$shelvesList);
            }else{
                array_multisort($sort_arr,SORT_DESC,$shelvesList);
            }

            //取出数组中部分元素
            $list = array_slice($shelvesList,$offset,$limit);

            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

}