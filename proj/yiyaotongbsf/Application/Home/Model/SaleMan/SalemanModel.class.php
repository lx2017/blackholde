<?php

/**
 * 业务员
 */

namespace Home\Model\SaleMan;

use Think\Model;

class SalemanModel extends Base\BaseModel {

    protected $tableName = "saleman";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->_return = array('code' => 2000, 'msg' => $this->_codeList[2000]);
    }
    
    public function test(){
        print_r('sasasa_test');
    }

    /**
     * 验证业务员是否存在<通过业务员ID>
     * @param int $saleman_id   业务员ID
     * @param array $field 查询字段数组
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function CheckSalemanUnique($saleman_id, $field = array()) {

        if (empty($saleman_id)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }

        //组装查询条件
        $whereSaleman = array(
            'id' => array('eq', $saleman_id)
        );

        //查询
        $infoSaleman = $this->where($whereSaleman)->field($field)->find();
        if (check_array($infoSaleman)) {
            $this->_return = array('code' => 2014, 'msg' => $this->_codeList[2014], 'data' => $infoSaleman);
            return $this->_return;
        }

        $this->_return = array('code' => 2015, 'msg' => $this->_codeList[2015]);
        return $this->_return;
    }

    /**
     * 查询业务员详情<通过业务员ID>
     * @param int $salemanId   业务员、县总等ID
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function GetSalemanDetail($salemanId) {
        if (empty($salemanId)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }

        //查询
        $fieldSaleman = array(
            'id', 'phone', 'head_img', 'real_name', 'card_number', 'sex', 'age', 'card_front_img', 'card_back_img', 'graduate_img',
            'diploma_img', 'superior_id', 'superior_path', 'role_id', 'manage_locations', 'add_time'
        );
        return $this->CheckSalemanUnique($salemanId, $fieldSaleman);
    }

    /**
     * 修改业务员（县总等）详情信息<不能修改密码>
     * @param int $salemanId    业务员、县总ID等
     * @param array $data   数据数组<exp：array('head_img'=>'图片地址','sex'=>'性别',.....)>
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function EditSaleman($salemanId, $data = array()) {
        if (empty($salemanId)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }

        if (!check_array($data)) {
            $this->_return = array('code' => 2028, 'msg' => $this->_codeList[2028]);
            return $this->_return;
        }

        if ($data['password']) {
            unset($data['password']);
        }

        //修改
        $where = array(
            'id' => array('eq', $salemanId)
        );
        $exc_num = $this->data($data)->where($where)->save();
        if ($exc_num <= 0) {
            $this->_return = array('code' => 2029, 'msg' => $this->_codeList[2029]);
            return $this->_return;
        }

        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200]);
        return $this->_return;
    }

}
