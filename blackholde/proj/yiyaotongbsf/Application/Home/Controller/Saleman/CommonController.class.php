<?php

namespace Home\Controller\Saleman;

use Think\Controller;
use Home\Controller\SaleManager\BasicController;
use Home\Model\SaleMan\SalemanModel;

class CommonController extends BasicController {

    public $_salemanInfo;

    public function _initialize() {
        $url = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
        if ($url == 'saleman/upcoordinate') {
            return;
        }
        parent::_initialize();
        $this->_salemanInfo = parent::$user;
//        //判断是否登陆
//        if (!$this->isLogin()) {
////            redirect('/Home/Saleman/Public/login', 0);
//        }
//
//        //查询用户信息
//        $this->_getSaleman();
    }

    public function _getSaleman() {
        $salemanModel = D('Saleman', 'Model');
        $where = array(
            'id' => array('eq', 204)
        );
        $this->_salemanInfo = $salemanModel->where($where)->find();
    }

}
