<?php

/**
 * 业务员-申请支持
 */

namespace Home\Model\WorkSummary;

use Think\Model;
use Think\Model\RelationModel;

class SalemanapplyModel extends RelationModel {

    protected $tableName = "saleman_apply";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
    }

   /*
    *
    * 根据业务员id来获取当前的申请次数
    *
    * */
    public function getApplyCountBySaleId( $id,$year=2016 )
    {
        if( !$id )
        {
            return 0;
        }
        $clinics = array();
        $where['saleman_id'] = $id;
        $lists = $this->field('id,apply_time')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count( $clinics ) ? count( $clinics ) : 0;
    }


    /*
     *
     * 根据业务员id来获取当前的申请详情
     *
     * */
    public function getApplyDetailBySaleId( $id,$type=1,$year=2016)
    {
        if( !$id )
        {
            return false;
        }
        $where['saleman_id'] = $id;
        $where['type'] = $type;
        $datas = array();
        $lists = $this->field('id,type,reply_status,apply_time')->where($where)->select();
        foreach( $lists as $list)
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $datas[] = $list;
        }
        return $datas ? $datas : false;
    }

    /*
     *
     * 获取县总下的业务员的所有拜访次数
     *
     * */
    public function getApplyCountByXianZong($sale_id,$year=2016)
    {
        $clinics = array();
        $where['superior_id'] = $sale_id;
        $lists = $this->field('id,apply_time')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count( $clinics ) ? count( $clinics ) : 0;
    }

    /*
     *
     * 获取地总下的县总的所有申请次数
     *
     * */
    public function getApplyCountByDiZong($sale_id,$year=2016)
    {

        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty($ids))
        {
            return 0;
        }
        $clinics = array();
        $where['superior_id'] = array('in',$ids);
        $lists = $this->field('id,apply_time')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return count( $clinics ) ? count( $clinics ) : 0;
    }

    /*
     *
     * 获取县总下的业务员的所有申请次数
     *
     * */
    public function getApplyDetailByXianZong($sale_id,$type=1,$year=2016)
    {
        $clinics = array();
        $where['superior_id'] = $sale_id;
        $where['type'] = $type;
        $lists = $this->field('id,type,reply_status,apply_time')->where($where)->select();
        //var_dump($lists);
        //echo $this->getLastSql();die;
        foreach( $lists as $list )
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return !empty( $clinics ) ? $clinics : false;
    }

    /*
     *
     * 获取地总下的县总的所有申请次数
     *
     * */
    public function getApplyDetailByDiZong($sale_id,$type=1,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        if( empty($ids))
        {
            return 0;
        }
        $clinics = array();
        $where['superior_id'] = array('in',$ids);
        $where['type'] = $type;
        $lists = $this->field('id,type,reply_status,apply_time')->where($where)->select();
        //var_dump($lists);
        //echo $this->getLastSql();die;
        foreach( $lists as $list )
        {
            $add_year = date('Y',$list['apply_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return !empty( $clinics ) ? $clinics : false;
    }
}
