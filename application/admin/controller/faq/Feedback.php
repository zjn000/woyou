<?php

namespace app\admin\controller\faq;

use app\common\controller\Backend;

use think\Controller;
use think\Request;

/**
 * 反馈管理
 *
 * @icon fa fa-circle-o
 */
class Feedback extends Backend
{
    
    /**
     * Feedback模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('Feedback');

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

}
