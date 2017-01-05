<?php
/**
 *
 * User: 卖女孩的小火柴
 * Date: 16/12/28
 * Time: 下午9:56
 *
 */

namespace Home\Model\WorkSummary;


use Think\Model;

class SalemanorderdetailModel extends Model
{
    protected $tableName = "saleman_order_detail";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
    }


    /*
     *
     *
     * 根据订单id获取订单详情
     *
     * */
    public function getOrderDetailByOrderId($order)
    {
        $where['order_id'] = $order;
        $detail = $this->where($where)->find();
        return $detail ? $detail : false;
    }
}