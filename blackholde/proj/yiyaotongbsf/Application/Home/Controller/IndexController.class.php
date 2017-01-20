<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午2:08
 */

namespace Home\Controller;

use Home\Controller\BaseController;

class IndexController extends BaseController {
    /* 前台入口文件 */

    public function index() {

        //$clinicModel = new \Home\Model\SaleMan\ClinicModel();
        $clinicModel = D('SaleMan\Clinic');

//         $result = $clinicModel->AddClinicAccount('183', '', '', '');
//         dump($result);
//         die();

        $data = array('x' => 'secret', 'f' => 'json');
        $this->assign('data', $data);
        $this->display('index');
    }

}
