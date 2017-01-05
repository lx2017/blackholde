<?php

/**
 * 业务员-基础模型
 */

namespace Home\Model\SaleMan\Base;

use Think\Model;

class BaseModel extends Model {

    public $_codeList;

    public function _initialize() {
        $this->_set();
        return $this->_get();
    }

    public function _set() {
        $this->_codeList = C('CODE_LIST');
    }

    public function _get() {
        return $this->_codeList;
    }

}
