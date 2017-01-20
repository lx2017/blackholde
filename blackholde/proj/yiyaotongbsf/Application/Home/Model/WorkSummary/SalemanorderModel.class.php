<?php

/**
 * 业务员-订单
 */

namespace Home\Model\WorkSummary;

use Home\Model\WorkSummary\SalemanModel;
use Think\Model\RelationModel;

class SalemanorderModel extends RelationModel {


    protected $tableName = "saleman_order";
    protected $_return;

    protected $_link = array(

        'order_clinic' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_fields' => 'clinic_name,clinic_pic',
            'class_name' => 'clinic',
            'foreign_key' => 'send_id',
            'mapping_name' => 'clinic',
        ),

    );
    public function _initialize() {
        parent::_initialize();
    }

    /*
     *
     * 根据业务员id来获取总金额
     *
     * */
    public function getSalePriceBySaleId($sale_id,$year=2016)
    {
        $where['status'] = 1;//已完成
        $where['type'] = 1;//当type为1时, send_id为业务员id
        $where['send_id'] = $sale_id;//业务员自己的
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $num = 0;
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $num += $order['total_price'];
        }
        return $num ? $num : 0;
    }


    /*
     *
     * 根据诊所id来获取总金额
     * 诊所
     *
     * */
    public function getClinicPriceBySaleId($sale_id,$year=2016)
    {
        //业务员下的所有的诊所的销售
        $where['status'] = 1;//已完成
        $where['type'] = 0;//已完成 0为诊所 1为业务员
        $where['saleman_id'] = $sale_id;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $num = 0;
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $num += $order['total_price'];
        }
        return $num ? $num : 0;
    }
    /*
     *
     * 退货数量是针对下一级的
     *
     * */
    public function getQuiteSalePriceBySaleId($sale_id,$year=2016)
    {
        //退货数量
        $where['status'] = 2;//退货
        $where['type'] = 0;//已完成  0为诊所 1为业务员
        $where['saleman_id'] = $sale_id;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $num = 0;
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $num += $order['total_price'];
        }
        return $num ? $num : 0;
    }
    /*
     *
     * 根据业务员id获取诊所的销售列表
     *
     * */
    public function getClinicListBySaleId($sale_id,$year=2016)
    {
        $where['status'] = 1;
        $where['saleman_id'] = $sale_id;
        $where['type'] = 0;
        $orders = $this->relation(true)->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $lists[$order['send_id']]['name'] = $order['clinic']['clinic_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['clinic_id'] = $order['send_id'];
        }
        return $lists;
    }

    /*
     *
     * 根据clinic_id来获取order列表
     *
     * */
    public function getOrdersListsBySendId($send_id,$year=2016)
    {
        $where['type'] = 1;
        $where['status'] = 1;
        $where['send_id'] = $send_id;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $saleModel = new SalemanModel();
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $saleModel->getSalemanDetail($order['saleman_id']);
            $order['saleman_name'] = $saleman['real_name'];
            $order['status'] = '已完成';
            $lists[] = $order;
        }
        return $lists;
    }


    /*
     *
     * 根据业务员id获取业务员的订货列表
     *
     * */
    public function getSalemanOrderListBySaleId($sale_id,$year=2016)
    {
        $where['status'] = 1;
        $where['saleman_id'] = $sale_id;
        $where['type'] = 1;
        $orders = $this->relation(true)->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $saleModel = new SalemanModel();
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $saleModel->getSalemanDetail($order['saleman_id']);
            $order['saleman_name'] = $saleman['real_name'];
            $order['status'] = '已完成';
            $lists[] = $order;
            $lists['num'] += 1;
            $lists['price'] += $order['total_price'];;
        }
        return $lists;
    }

    /*
     *
     * 根据业务员id获取诊所的退货列表
     *
     * */
    public function getQuiteSaleClinicListBySaleId($sale_id,$year=2016)
    {
        $where['status'] = 2;
        $where['saleman_id'] = $sale_id;
        $where['type'] = 0;
        $orders = $this->relation(true)->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $lists[$order['send_id']]['name'] = $order['clinic']['clinic_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['clinic_id'] = $order['send_id'];
        }
        return $lists;
    }


    /*
     *
     * 根据clinic_id来获取业务员退货列表
     *
     * */
    public function getQuiteClinicListsByClinicId($clinic_id,$year=2016)
    {
        $where['type'] = 0;
        $where['status'] = 2;
        $where['send_id'] = $clinic_id;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $saleModel = new SalemanModel();
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $saleModel->getSalemanDetail($order['saleman_id']);
            $order['saleman_name'] = $saleman['real_name'];
            $order['status'] = '退货';
            $lists[] = $order;
        }
        return $lists;
    }

    /*
     *
     * 获取县总下的业务员所有的定订货,即县总的销售
     *
     * */
    public function getClinicPriceByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $where['status'] = 1;
        $orders = $this->where( $where )->select();
        $price = 0;
        foreach ( $orders as $order )
        {
            $price += $order['total_price'];
        }
        return $price ? $price : 0;
    }

    /*
     *
     * 获取地总下的业务员所有的定订货,即地总的销售
     *
     * */
    public function getClinicPriceByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return false;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $salemanids = array_column($salemanids,'id');
        //var_dump($salemanids);
        $where['send_id'] = array('in',$salemanids);
        $where['type'] = 1;
        $where['status'] = 1;
        $orders = $this->where( $where )->select();
        //echo $this->getLastSql();die;
        $price = 0;
        foreach ( $orders as $order )
        {
            $price += $order['total_price'];
        }
        return $price ? $price : 0;
    }

    /*
      *
      * 获取县总下的业务员所有的退货,即县总的退货
      *
      * */
    public function getQuitePriceByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        $where['status'] = 2;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $orders = $this->where( $where )->select();
        $price = 0;
        foreach ( $orders as $order )
        {
            $price += $order['total_price'];
        }
        return $price ? $price : 0;
    }

    /*
     *
     * 获取地总下的业务员所有的退货,即地总的退货
     *
     * */
    public function getQuitePriceByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return 0;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $ids = array_column($salemanids,'id');
        $where['status'] = 2;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $orders = $this->where( $where )->select();
        $price = 0;
        foreach ( $orders as $order )
        {
            $price += $order['total_price'];
        }
        return $price ? $price : 0;
    }
    /*
     *
     * 根据县总所有的业务员来获取总金额
     *
     * */
    public function getSalePriceByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;//已完成
        $where['type'] = 1;//当type为1时, send_id为业务员id
        $where['send_id'] = array('in',$ids);//业务员自己的
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $num = 0;
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $num += $order['total_price'];
        }
        return $num ? $num : 0;
    }

    /*
     *
     * 根据地总所有的业务员来获取总金额
     *
     * */
    public function getSalePriceByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return 0;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $ids = array_column($salemanids,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;//已完成
        $where['type'] = 1;//当type为1时, send_id为业务员id
        $where['send_id'] = array('in',$ids);//业务员自己的
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return 0;
        }
        $num = 0;
        foreach( $orders as $order )
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $num += $order['total_price'];
        }
        return $num ? $num : 0;
    }

    /*
     *
     * 根据业务员id获取诊所的销售列表
     *
     * */
    public function getClinicListByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;//已完成
        $where['type'] = 1;//当type为1时, send_id为业务员id
        $where['send_id'] = array('in',$ids);//业务员自己的

        $orders = $this->where($where)->select();
        //var_dump($orders);
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $salemanModel->getSalemanDetail($order['send_id']);
            $lists[$order['send_id']]['name'] = $saleman['real_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['send_id'] = $order['send_id'];
        }
        return $lists;
    }

    /*
     *
     * 根据业务员id获取诊所的销售列表
     *
     * */
    public function getClinicListByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return false;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $ids = array_column($salemanids,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;//已完成
        $where['type'] = 1;//当type为1时, send_id为业务员id
        $where['send_id'] = array('in',$ids);//业务员自己的

        $orders = $this->where($where)->select();
        //var_dump($orders);
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $salemanModel->getSalemanDetail($order['send_id']);
            $lists[$order['send_id']]['name'] = $saleman['real_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['send_id'] = $order['send_id'];
        }
        return $lists;
    }

    /*
     *
     * 根据业务员id获取诊所的退货列表
     *
     * */
    public function getQuiteSaleClinicListByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 2;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }

            $saleman = $salemanModel->getSalemanDetail($order['send_id']);
            $lists[$order['send_id']]['name'] = $saleman['real_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['send_id'] = $order['send_id'];
        }
        return $lists;
    }

    /*
     *
     * 根据地总id,获取地总下的业务员id获取诊所的退货列表
     *
     * */
    public function getQuiteSaleClinicListByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return false;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $ids = array_column($salemanids,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 2;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }

            $saleman = $salemanModel->getSalemanDetail($order['send_id']);
            $lists[$order['send_id']]['name'] = $saleman['real_name'];
            $lists[$order['send_id']]['total_price'] += $order['total_price'];
            $lists[$order['send_id']]['num'] += 1;
            $lists[$order['send_id']]['pic'] = $order['clinic']['clinic_pic'];
            $lists[$order['send_id']]['send_id'] = $order['send_id'];
        }
        return $lists;
    }

    /*
     *
     * 根据业务员id获取业务员的订货列表
     *
     * */
    public function getSalemanOrderListByXianZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        if( !$salemans )
        {
            return 0;
        }
        $ids = array_column($salemans,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $saleModel = new SalemanModel();
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $saleModel->getSalemanDetail($order['saleman_id']);
            $order['saleman_name'] = $saleman['real_name'];
            $order['status'] = '已完成';
            $lists[] = $order;
            $lists['num'] += 1;
            $lists['price'] += $order['total_price'];;
        }
        return $lists;
    }

    /*
     *
     * 根据业务员id获取业务员的订货列表
     *
     * */
    public function getSalemanOrderListByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return false;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $ids = array_column($salemanids,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['status'] = 1;
        $where['send_id'] = array('in',$ids);
        $where['type'] = 1;
        $orders = $this->where($where)->select();
        if( !$orders )
        {
            return false;
        }
        $saleModel = new SalemanModel();
        foreach( $orders as $order)
        {
            $add_year = date('Y',strtotime($order['add_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $saleman = $saleModel->getSalemanDetail($order['saleman_id']);
            $order['saleman_name'] = $saleman['real_name'];
            $order['status'] = '已完成';
            $lists[] = $order;
            $lists['num'] += 1;
            $lists['price'] += $order['total_price'];;
        }
        return $lists;
    }
}
