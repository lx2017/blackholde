<?php

/**
 * 业务员管理
 */

namespace Admin\Controller\Saleman;

use Admin\Controller\AdminController;

class BaseController extends AdminController {

    function _initialize() {
        parent::_initialize();
        $REQUEST = (array) I('request.');
        if (!empty($REQUEST['key'])) {
            $actions = $this->getMenuActions($REQUEST['key']);
            if(check_array($actions)){
                $actions=  set_array_key($actions, 'key');
            }
            $this->assign('actions', $actions);
        }
    }

}
