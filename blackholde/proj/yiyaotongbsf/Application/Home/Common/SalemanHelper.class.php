<?php

namespace Home\Common;

class SalemanHelper {

    /**
     * 获取当前用户的上级（递归循环）
     */
    public static function getSuperiorById($id, $roleId = 0) {
        if ($roleId < 0) {
            return array();
        }
        $saleModel = M('saleman');
        $saleman = $saleModel->where(array('id' => array('eq', $id)))->find();
        
        if (!check_array($saleman) || $saleman['role_id'] < $roleId) {
            return array();
        }

        if ($saleman['role_id'] == $roleId) {
            return $saleman;
        } else {
            return self::getSuperiorById($saleman['superior_id'], $roleId);
        }
    }

    /**
     * 生成订单编号
     */
    public static function makeOrderId($date) {
        //查询当前类型指定日期的的订单最后一位
        $salemanOrderModel = M('saleman_order');
        $where = array(
            'add_date' => array('eq', $date)
        );
        $order = array(
            'add_time' => 'desc'
        );
        $field = array('id');
        $order = $salemanOrderModel->field($field)->where($where)->order($order)->find();

        //组装订单ID
        if (check_array($order)) {

            $orderId = time()+rand(1,1000);
        } else {
            $dateYmdList = explode('-', $date);
            $strNum = 1;
            $str = '';
            for ($i = 0; $i < 7; $i++) {
                $str.="0";
            }

            $orderId = $dateYmdList[0] . $dateYmdList[1] . $dateYmdList[2] . $str . $strNum;
        }
        
        return $orderId;
    }

}
