<?php

/**
 * 业务员-签到拜访
 */

namespace Home\Model\WorkSummary;

use Think\Model;
use Think\Model\RelationModel;

class SalemansignModel extends RelationModel {

    protected $tableName = "saleman_sign";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
    }

    /*
     *
     * 根据业务员id来获取拜访次数
     *
     * */
    public function getVisitCountBySaleId($id,$year=2016)
    {
        if( !$id )
        {
            return 0;
        }
        $where['saleman_id'] = $id;
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count($clinics) ? count($clinics) : 0;
    }


    /*
     *
     * 返回业务员详情拜访次数
     *
     * */
    public function getVisitDetailBySaleId($id,$year=2016)
    {
        if( !$id )
        {
            return 0;
        }
        $where['saleman_id'] = $id;
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            $month = date('n',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[$month][] = $list;
        }
        for( $i=1;$i<=12;$i++ )
        {
            $res[$i] = empty( $clinics[$i] ) ? 0 : count( $clinics[$i]);
        }
        return $res ? $res : 0;
    }

    /*
     *
     * 返回 地总的业务员拜访详情
     *
     * */
    public function getVisitDetailByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty( $ids ))
        {
            return fasle;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $where['saleman_id'] = array('in',$salemanids);
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            $month = date('n',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[$month][] = $list;
        }
        for( $i=1;$i<=12;$i++ )
        {
            $res[$i] = empty( $clinics[$i] ) ? 0 : count( $clinics[$i]);
        }
        return $res ? $res : 0;
    }



    /*
     *
     * 返回业务员详情拜访次数
     *
     * */
    public function getMonthVisitDetailBySaleId($id,$year=2016,$month='')
    {
        if( !$id )
        {
            return 0;
        }
        $where['saleman_id'] = $id;
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            $add_month = date('n',strtotime($list['sign_date']));
            $add_day = date('j',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            if( $month != $add_month )
            {
                continue;
            }
            $clinics[$add_day] = true;
        }
        return $clinics ? $clinics : 0;
    }
    

    /*
     *
     * 根据地总id返回所有业务员拜访次数详情
     *
     * */
    public function getMonthVisitDetailByDiZong($id,$year=2016,$month='')
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($id,$year);
        if( empty( $ids ))
        {
            return fasle;
        }
        $salemanids = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        if( !$salemanids )
        {
            return 0;
        }
        $where['saleman_id'] = array('in',$salemanids);
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            $add_month = date('n',strtotime($list['sign_date']));
            $add_day = date('j',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            if( $month != $add_month )
            {
                continue;
            }
            $clinics[$add_day] = true;
        }
        return $clinics ? $clinics : 0;
    }


    /*
     * 
     * 
     * 获取县总的下所有的业务员id的所有的拜访次数
     * 
     * */
    public function getVisitCountByXianZong( $sale_id,$year )
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        $ids = array_column($salemans,'id');
        if( !$ids )
        {
            return 0;
        }
        $where['saleman_id'] = array('in',$ids);
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count($clinics) ? count($clinics) : 0;
    }


    /*
     *
     * 根据地总id获取所有县总下的所有业务员的拜访次数 
     *
     * */
    public function getVisitCountByDiZong($sale_id,$year=2016)
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
        $where['saleman_id'] = array('in',$salemanids);
        $lists = $this->field('id,sign_date')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['sign_date']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count($clinics) ? count($clinics) : 0;
    }
}
