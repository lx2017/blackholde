<?php

/**
 * 业务员-我的行程
 */

namespace Home\Model\SaleMan;

use Think\Model;

class SalemantripModel extends Base\BaseModel {

    protected $tableName = "saleman_trip";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->_return = array('code' => 2000, 'msg' => $this->_codeList[2000]);
    }

}
