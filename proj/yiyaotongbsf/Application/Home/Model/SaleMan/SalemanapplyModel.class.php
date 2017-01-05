<?php

/**
 * 业务员-申请支持
 */

namespace Home\Model\SaleMan;

use Think\Model;

class SalemanapplyModel extends Base\BaseModel {

    protected $tableName = "saleman_apply";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->_return = array('code' => 2000, 'msg' => $this->_codeList[2000]);
    }

    /**
     * 申请支持
     * @param int $salemanId   业务员、县总等ID
     * @param int $applyCate   申请支持类型【值对应配置文件saleman里的DEF_APPLY_CATE的Key值】
     * @param varchar $content  内容
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function ApplySupport($salemanId, $applyCate, $content) {
        if (empty($salemanId)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }

        $defApplyCateList = C('DEF_APPLY_CATE');
        $defApplyCateIdList = array_keys($defApplyCateList);
        if (empty($applyCate) || !in_array($applyCate, $defApplyCateIdList)) {
            $this->_return = array('code' => 2026, 'msg' => $this->_codeList[2026]);
            return $this->_return;
        }

        //验证业务是否存在
        $salemanModel = D('Saleman', 'Model');
        $resultSaleman = $salemanModel->CheckSalemanUnique($salemanId);
        if ($resultSaleman['code'] == 2015) {
            $this->_return = array('code' => 2015, 'msg' => $this->_codeList[2015]);
            return $this->_return;
        }

        //申请
        $dataSalemanApply = array(
            'saleman_id' => $salemanId,
            'type' => $applyCate,
            'apply_content' => $content,
            'superior_id' => $resultSaleman['data']['superior_id'],
            'reply_status' => 0,
            'reply_content' => '',
            'apply_time' => time(),
            'reply_time' => 0
        );
        $add_num = $this->data($dataSalemanApply)->add();
        if ($add_num <= 0) {
            $this->_return = array('code' => 2027, 'msg' => $this->_codeList[2027]);
            return $this->_return;
        }

        //返回
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200]);
        return $this->_return;
    }

}
