<?php

namespace app\admin\controller\coupon;

use app\common\controller\Backend;
use app\admin\model\Admin;
use think\Db;
use think\Exception;

/**
 * 优惠券信息管理
 *
 * @icon fa fa-circle-o
 */
class Coupon extends Backend
{
    
    /**
     * Coupon模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Coupon');

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
                $list[$k]['admin_name'] = isset($adminName[$v['update_id']]) ? $adminName[$v['update_id']] : '';

                if($v['type'] == 1){
                    $list[$k]['effective_time'] = $v['valid_date'];
                    $list[$k]['type_name'] = __('Fixed_days');
                }

                if($v['type'] == 2){
                    $list[$k]['effective_time'] = date('Y-m-d H:i:s',$v['start_time']).'~'.date('Y-m-d H:i:s',$v['end_time']);
                    $list[$k]['type_name'] = __('Time_period');
                }

                $list[$k]['face_value'] = '满'.$v['activation_amount'].'-'.$v['discount_amount'];
            }
            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->view->fetch();
    }


    /**
     * 批量更新
     */
    public function multi($ids = "")
    {
        $ids = $ids ? $ids : $this->request->param("ids");
        if ($ids)
        {
            if ($this->request->has('params'))
            {
                parse_str($this->request->post("params"), $values);
                $values = array_intersect_key($values, array_flip(is_array($this->multiFields) ? $this->multiFields : explode(',', $this->multiFields)));
                $values['update_id'] = $this->auth->id;
                $values['update_time'] = time();
                if ($values)
                {
                    $adminIds = $this->getDataLimitAdminIds();
                    if (is_array($adminIds))
                    {
                        $this->model->where($this->dataLimitField, 'in', $adminIds);
                    }
                    $count = $this->model->where($this->model->getPk(), 'in', $ids)->update($values);
                    if ($count)
                    {
                        $this->success();
                    }
                }
                else
                {
                    $this->error(__('You have no permission'));
                }
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 推送券
     */
    public function push($ids = NULL){
        $row = $this->model->get($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        if (!$row['status'])
            $this->error('无效优惠券不能推送','');

        if ($this->request->isPost())
        {
            $params = $this->request->post("row/a");

            if ($params)
            {
                //相当于循环次数
                $zhang = intval($params['push_zhang']);
                if($zhang < 1 || $zhang > 10 ){
                    $this->error('推送张数应在1-10张', '');
                }


                //得到手机号码数组（未过滤，可能有空格）
                $content = explode("\r\n", $params['content']);

                //去除空格
                foreach ($content as $key=>$item){
                    $temp=trim($item);
                    if(!empty($temp)){
                        $arrPhone[$key]=$temp;
                    }
                }

                $arrUid = Db::name('user')->where('phone','IN',$arrPhone)->column('id,phone');

                if(empty($arrUid)){
                    $this->error('用户不存在');
                }
                $time = time();
                $adminId = $this->auth->id;
                foreach ($arrUid as $k=>$value){
                    $data[$k] = array(
                        'uid' => $k,
                        'coupon_id' => intval($ids),
                        'is_use' => 0,
                        'create_time' => $time
                    );

                    $arrParam[$k] = array(
                        'uid' => $k,
                        'phone' => $value,
                        'coupon_id' => intval($ids),
                        'name' => trim($row['name']),
                        'admin_id' => $adminId,
                        'create_time' => $time
                    );

                }


                try{

                    for($i = 0;$i<$zhang;$i++){
                        //批量添加用户优惠券
                        Db::name('user_coupon')->insertAll($data);
                        //批量添加推送记录
                        Db::name('coupon_push')->insertAll($arrParam);
                    }

                }catch (Exception $e){
                    $this->error($e);return;
                }



                $this->success();


            }
            $this->error(__('Parameter %s can not be empty', ''));
        }

        $this->view->assign("row", $row);
        return $this->view->fetch();
    }


}
