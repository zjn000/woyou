<?php
namespace app\admin\controller\statistics;

use app\common\controller\Backend;

use think\Db;

/**
 * 商品销售统计
 */
class Orderstatistics extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('order');
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

            $month = trim($this->request->request('month'));

            $firstday = date('Y-m-01', strtotime($month));
            $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));


            $list = Db::name('order')
                ->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as time")
                ->field("COUNT(id) as num")
                ->field("SUM(total) as total")
                ->field("SUM(discount) as discount")
                ->where('status=1')->where("FROM_UNIXTIME(create_time,'%Y-%m-%d') BETWEEN '$firstday' AND '$lastday'")
                ->group('time')
                ->select();

            $ddd = $this->getMonthDays($month);

            $item = array_fill_keys($ddd, 0);

            $series = [
                'num'      => $item,
                'total'            => $item,
                'discount'            => $item
            ];
            foreach ($list as $k => $v)
            {
                $series['num'][$v['time']] = $v['num'];
                $series['total'][$v['time']] = $v['total'];
                $series['discount'][$v['time']] = $v['discount'];
            }
            foreach ($series as $k => &$v)
            {
                $v = array_values($v);
            }
            unset($v);

            foreach ($ddd as $k=>$value){
                $all_row[$k] = [
                    'time' => $value,
                    'num' => $series['num'][$k],
                    'total' => $series['total'][$k],
                    'discount' => $series['discount'][$k]
                ];
            }

            $summary = [
                'mouth' => $month,
                'all_num' => array_sum($series['num']),
                'all_total' => array_sum($series['total']),
                'all_discount' => array_sum($series['discount'])
            ];

            $result = array( "rows" => $series,'ddd'=>$ddd,'all_rows'=>$all_row,'summary'=>$summary);

            $this->success('', null, $result);
        }
        return $this->view->fetch();
    }

    /**
     * 获取某个月所有日期
     * @param string $month     输入月份
     * @param string $format    日期格式
     * @param bool $dateTimeZone    时间区
     * @return array    输入月份的所有日期
     */
    function getMonthDays($month = "this month", $format = "Y-m-d", $dateTimeZone = false) {
        if(!$dateTimeZone) $dateTimeZone = new \DateTimeZone("Asia/Shanghai");
        $start = new \DateTime("first day of $month", $dateTimeZone);
        $end = new \DateTime("last day of $month", $dateTimeZone);
        $days = array();
        for($time = $start; $time <= $end; $time = $time->modify("+1 day")) {
            $days[] = $time->format($format);
        }
        return $days;
    }


}