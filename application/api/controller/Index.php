<?php
namespace app\api\controller;

use app\common\controller\Frontend;

/**
 *
 */
class Index extends Frontend  {

    public function index(){

        return $this->view->fetch();

    }

}