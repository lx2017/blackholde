<?php
/**
 *
 * User: 卖女孩的小火柴
 * Date: 16/11/5
 * Time: 下午4:19
 *
 */

namespace Home\Model\WorkSummary;


use Think\Model;

class ClinicModel extends Model
{
    public function _initialize()
    {
        parent::_initialize();
    }


    /*
     *
     * 根据业务员id获取诊所id数组
     * @return array(1,2,3,4)   or  false
     *
     * */
    public function getClinicidsBySalemanID($sale_id,$year=2016)
    {
        if( !$sale_id )
        {
            return false;
        }
        $where['saleman_id'] = $sale_id;//业务员id
        $where['type'] = 0;//正常诊所
        $where['is_delete'] = 1;//没有删除诊所
        $clinic_ids = $this->field('id,add_time')->where($where)->select();
        foreach ( $clinic_ids as $clinic_id )
        {
            $add_year = date('Y',strtotime( $clinic_id['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            $ids[] = $clinic_id;
        }
        if( $ids )
        {
            $clinic_ids = array_column($ids,'id');
        }else{
            return false;
        }
        return $clinic_ids ? $clinic_ids : false;
    }

    /*
     *
     * 根据县总id获取所有县总下的业务员下的诊所
     *
     * */
    public function getClinicsByXianZong($sale_id,$year)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        $ids = array_column($ids,'id');
        $clinics = array();
        foreach( $ids as $id )
        {
            $clinic = $this->getClinicsBySalemanID($id,$year);
            if( $clinic )
            {
                $clinics[] = $clinic;
            }
        }
        return !empty($clinics) ? $clinics : false;
    }

    /*
     *
     * 根据县总id获取所有县总下的业务员下的诊所
     *
     * */
    public function getClinicsByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);

        $salemans = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        $ids = array_column( $salemans,'id');
        $clinics = array();
        $clinics = $this->getClinicsBySalemanIDs($ids,$year);
        return !empty($clinics) ? $clinics : false;
    }

    /*
     *
     * 获取全部诊所
     *
     * */
    public function getClinicsBySalemanID($sale_id,$year)
    {
        if( !$sale_id )
        {
            return false;
        }
        $where['saleman_id'] = $sale_id;
        $where['is_delete'] = 1;
        $where['type'] = 0;
        $lists = $this->where($where)->select();
        //var_dump($lists);die;
        $clinics = array();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime( $list['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return $clinics ? $clinics : false;
    }

    /*
     *
     * 获取全部诊所
     *
     * */
    public function getClinicsBySalemanIDs($sale_ids,$year)
    {
        if( !$sale_ids )
        {
            return false;
        }
        $where['saleman_id'] = array('in',$sale_ids);
        $where['is_delete'] = 1;
        $where['type'] = 0;
        $lists = $this->where($where)->select();
        //var_dump($lists);die;
        $clinics = array();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime( $list['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            $clinics[] = $list;
        }
        return $clinics ? $clinics : false;
    }
    /*
     统计业务员工作年份
    */
    public function getYear($sale_id)
    {
          if(!$sale_id)
          {
             return 0;
          }
         
          $result = $this->where("saleman_id=".$sale_id." and (type=0 or type =1)")->distinct(true)->field("add_time")->select();
          foreach($result as $key=>$val)
          {
              $result[$key] = explode("-",$val['add_time'])[0];
          }
          return array_unique($result);
    }
    /*
     *
     * 获取业务员下的所有诊所
     * $type = 0 正常诊所, 1为 目标诊所
     *
     * */
    public function getCount($sale_id,$type=0,$year=2016)
    {
        if( !$sale_id )
        {
            return 0;
        }
        $clinics = array();
        $where['saleman_id'] = $sale_id;
        $where['type'] = $type;
        $where['is_delete'] = 1;
        $lists = $this->field('id,add_time')->where($where)->select();
        foreach( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['add_time']));
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
     * 根据id获取诊所名称
     *
     * */
    public function getClinicNameById($id)
    {
        $where['id'] = $id;
        $name = $this->field('clinic_name')->where($where)->find();
        return $name ? $name['clinic_name'] : false;
    }
    
    
    
    /*
     * 
     * 根据月份来显示业务员目标诊所
     * 
     * */
    public function getClinicByMonth($sale_id,$year=2016,$type=1)
    {
        $where['type'] = $type;
        $where['saleman_id'] = $sale_id;
        $where['is_delete'] = 1;
        $lists = $this->where($where)->select();
        $clinics = array();
        foreach ( $lists as $list )
        {
            $add_year = date('Y',strtotime($list['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            $month = date('m',strtotime($list['add_time']));
            $clinics[$month][] = $list;
        }
        for( $i=1;$i<=12;$i++ )
        {
            $res[$i] = empty( $clinics[$i] ) ? 0 : count( $clinics[$i]);
        }
        return $res;
    }


    /*
     *
     * 根据月份显示县总下业务员目标诊所
     *
     * */
    public function getClinicByXianZong($sale_id,$year=2016,$type=1)
    {
        $saleModel = new SalemanModel();
        $ids = $saleModel->getSalemanIdsByXianZong($sale_id,$year);
        foreach( $ids as $id )
        {
            $res[] = $this->getClinicByMonth( $id['id'], $year,$type=1 );
        }
        if( !empty($res) )
        {
            $month = array();
            for( $i=0;$i<count($res);$i++)
            {
                foreach ( $res[$i] as $key => $v )
                {
                    $month[$key] += $v;
                }
            }
        }
        return !empty($month) ? $month : false;
    }

    /*
     *
     * 根据月份显示地总下业务员的目标诊所
     *
     * */
    public function getClinicByDiZong($sale_id,$year=2016,$type=1)
    {
        $saleModel = new SalemanModel();
        $ids = $saleModel->getXianZongIdsByDiZong($sale_id,$year);
        $month = array();
        foreach( $ids as $id )
        {
            $res[] = $this->getClinicByXianZong($id,$year,$type);
        }
        if( !empty($res) )
        {
            foreach( $res as $v )
            {
                if( $v )
                {
                    foreach( $v as $key=>$vv )
                    {
                        $month[$key] += $vv;
                    }
                }
            }
            return $month;
        }else{
            return fasle;
        }
    }



    /*
     *
     * 根据业务员id获取诊所经纬度数组
     *
     * */
    public function getPositionBySalemanID($sale_id,$year=2016)
    {
        if( !$sale_id )
        {
            return false;
        }
        $where['saleman_id'] = $sale_id;
        $where['type'] = 0;
        $where['is_delete'] = 1;
        $clinics = $this->field('id,clinic_lng,clinic_lat,add_time')->where($where)->select();
        foreach ( $clinics as $clinic )
        {
            $add_year = date('Y',strtotime( $clinic['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            if( $clinic['clinic_lng'] && $clinic['clinic_lat'] )
            {
                $position['lng'] = $clinic['clinic_lng'];
                $position['lat'] = $clinic['clinic_lat'];
                $result[] = $position;
            }
        }
        return $result ? $result : false;
    }


    /*
     *
     * 根据业务员id获取<b>业务员</b>对应的诊所的数量
     *
     *
     * */
    public function getClinicCountBySaleId($ids,$type=1)
    {
        if( empty( $ids ) )
        {
            return 0;
        }
        $where['saleman_id'] = array('in',$ids);
        $where['is_delete'] = 1;//未删除
        $where['type'] = $type;//目标诊所
        $count = $this->where($where)->count();
        return $count ? $count : 0;
    }

    /*
     *
     * 根据业务员id获取<b>业务员</b>对应的诊所
     *
     *
     * */
    public function getClinicsBySaleId($ids,$type=1)
    {
        if( empty( $ids ) )
        {
            return 0;
        }
        $where['saleman_id'] = array('in',$ids);
        $where['is_delete'] = 1;//未删除
        $where['type'] = $type;//目标诊所
        $clinics = $this->where($where)->select();
        return $clinics ? $clinics : false;
    }

    /*
    *
    * 根据业务员id获取诊所经纬度数组
    *
    * */
    public function getPositionByXianZong($sale_id,$year=2016)
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
        $where['saleman_id'] = array('in',$ids);
        $where['type'] = 0;
        $where['is_delete'] = 1;
        $clinics = $this->field('id,clinic_lng,clinic_lat,add_time')->where($where)->select();
        foreach ( $clinics as $clinic )
        {
            $add_year = date('Y',strtotime( $clinic['add_time']));
            if( $year != $add_year )
            {
                continue;
            }
            if( $clinic['clinic_lng'] && $clinic['clinic_lat'] )
            {
                $position['lng'] = $clinic['clinic_lng'];
                $position['lat'] = $clinic['clinic_lat'];
                $result[] = $position;
            }
        }
        return $result ? $result : false;
    }


}