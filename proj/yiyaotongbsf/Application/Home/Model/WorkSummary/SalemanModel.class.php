<?php

/**
 * 业务员
 */

namespace Home\Model\WorkSummary;

use Think\Model;
use Home\Model\WorkSummary\ClinicModel;
use Home\Model\WorkSummary\DoctorModel;
use Home\Model\WorkSummary\SalemanapplyModel;
use Home\Model\WorkSummary\SalemansignModel;
use Home\Model\WorkSummary\SalemanorderModel;

class SalemanModel extends Model {

    protected $tableName = "saleman";
    private $doctorModel;
    private $clinicModel;
    private $salemanSignModel;
    private $salemanApplyModel;
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->clinicModel = new ClinicModel();
    }
    

    /**
     *
     * 查询业务员详情<通过业务员ID>
     *
     */
    public function getSalemanDetail($sale_id) {
        $where['id'] = $sale_id;
        $saleman = $this->where($where)->find();
        if( !$saleman )
        {
            return false;
        }
        return $saleman;
    }


    /*
     *
     * 根据id来判断角色
     *
     * */
    public function getRoleBySaleId($sale_id)
    {
        $where['id'] = $sale_id;
        $role = $this->field('role_id')->where($where)->find();
        if( $role === NULL)
        {
            return false;
        }else{
            return $role['role_id'];//角色,0：总部，1：大区经理；2：省总；3：地总；4：县总；5：业务员
        }
    }


    /*
     *
     * 获取县总中业务员数量
     *
     * */
    public function getSalemanCountByXianZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;//父id为县总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $saleman[] = $child;
            }
            return count( $saleman );
        }else{
            return 0;
        }
    }

    /*
     *
     * 获取县总中业务员id
     *
     * */
    public function getSalemanIdsByXianZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;//父id为县总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        $saleman = array();
        if( $childs ) {
            foreach ($childs as $child) {
                $add_year = date('Y', $child['add_time']);
                if ($year != $add_year) {
                    continue;
                }
                $saleman[] = $child;
            }
        }
        return !empty($saleman) ? $saleman : false;
    }

    /*
     *
     * 根据县总ids获取县总中业务员id
     *
     * */
    public function getSalemanIdsByXianZongIds($ids,$year=2016)
    {
        $where['superior_id'] = array( 'in',$ids);//父id为县总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        $saleman = array();
        if( $childs ) {
            foreach ($childs as $child) {
                $add_year = date('Y', $child['add_time']);
                if ($year != $add_year) {
                    continue;
                }
                $saleman[] = $child;
            }
        }
        return !empty($saleman) ? $saleman : false;
    }


    /*
      *
      * 获取县总中目标诊所数量
      *
      * */
    public function getPurposeClinicCountByXianZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;//父id为县总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $salemans[] = $child;
            }
            $ids = array_column($salemans,'id');
            $purposeClinicNum = $this->clinicModel->getClinicCountBySaleId($ids);
            return $purposeClinicNum;
        }else{
            return 0;
        }
    }

    /*
     *
     *
     * 获取县总中诊所数量
     *
     * */
    public function getClinicCountByXianZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;//正常的用户
        $childs= $this->field('id,add_time')->where($where)->select();
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $salemans[] = $child;
            }
            $ids = array_column($salemans,'id');
            $clinicNum = $this->clinicModel->getClinicCountBySaleId($ids,0);
            return $clinicNum;
        }

    }

    /*
     *
     * 获取县总下的业务员的详细信息
     *
     *
     * */
    public function getSalemanListsBySaleId($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $salemans = $this->field('id,real_name')->where($where)->select();
        if( $salemans )
        {
            foreach( $salemans as $saleman )
            {
                $this->doctorModel = new DoctorModel();
                $this->clinicModel = new ClinicModel();
                $this->salemanSignModel = new SalemansignModel();
                $this->salemanApplyModel = new SalemanapplyModel();
                $this->salemanOrderModel = new SalemanorderModel();
                $clinic_ids = $this->clinicModel->getClinicidsBySalemanID($saleman['id'],$year);//当年普通诊所数量,返回的是id
                $list['doctorNum'] = $this->doctorModel->getCount($clinic_ids);//医生数量
                $list['clinicNum'] = $this->clinicModel->getCount($saleman['id'],0,$year);//诊所数量
                $list['clinicSumNum'] = $this->clinicModel->getCount($saleman['id'],1,$year);//目标诊所数量
                $list['salmanVisitNum'] = $this->salemanSignModel->getVisitCountBySaleId($saleman['id'],$year);//拜访次数
                $list['salemanApplyNum'] = $this->salemanApplyModel->getApplyCountBySaleId($saleman['id'],$year);
                $list['salemanOrderNum'] = $this->salemanOrderModel->getClinicPriceBySaleId($saleman['id'],$year);//销售是  诊所订的货就是业务员的销售
                $list['salemanPrice'] = $this->salemanOrderModel->getSalePriceBySaleId($saleman['id'],$year);//订货数量 是针对业务员自己的
                $list['quitePrice'] = $this->salemanOrderModel->getQuiteSalePriceBySaleId($saleman['id'],$year);
                $list['real_name'] = $saleman['real_name'];
                $salemanlists[] = $list;
            }
            return $salemanlists;
        }else{
            return false;
        }
    }


    /*
     *
     * 根据地总id获取下一级县总的信息
     *
     * */
    public function getCountryListsByDiZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $childs = $this->field('id,real_name')->where($where)->select();//县总id
        if( $childs )
        {
            $this->doctorModel = new DoctorModel();
            $this->clinicModel = new ClinicModel();
            $this->salemanSignModel = new SalemansignModel();
            $this->salemanApplyModel = new SalemanapplyModel();
            $this->salemanOrderModel = new SalemanorderModel();
            foreach( $childs as $child )
            {
                $saleman['salemanNum'] = $this->salemanModel->getSalemanCountByXianZong($child['id'],$year);
                $saleman['doctorNum'] = $this->doctorModel->getDoctorCountByXianZong($child['id'],$year);
                $saleman['clinicNum'] = $this->salemanModel->getClinicCountByXianZong($child['id'],$year);
                $saleman['clinicSumNum'] = $this->salemanModel->getPurposeClinicCountByXianZong($child['id'],$year);
                $saleman['salmanVisitNum'] = $this->salemanModel->getVisitCountByXianZong($child['id'],$year);
                $saleman['salemanApplyNum'] = $this->salemanApplyModel->getClinicPriceByXianZong($child['id'],$year);
                $saleman['salemanOrderNum'] = $this->salemanOrderModel->getClinicPriceByXianZong($child['id'],$year);
                $saleman['salemanPrice'] = $this->salemanOrderModel->getSalePriceByXianZong($child['id'],$year);
                $saleman['quitePrice'] = $this->salemanOrderModel->getQuitePriceByXianZong($child['id'],$year);
                $saleman['real_name'] = $child['real_name'];
                $countrylists[] = $saleman;
            }
            return $countrylists;
        }else{
            return false;
        }
    }

    /*
     *
     * 根据省总id获取下一级地总的信息
     *
     * */
    public function getAreaListsBySaleId($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $childs = $this->field('id,real_name')->where($where)->select();//地总id
        if( $childs )
        {
            $this->doctorModel = new DoctorModel();
            $this->clinicModel = new ClinicModel();
            $this->salemanSignModel = new SalemansignModel();
            $this->salemanApplyModel = new SalemanapplyModel();
            $this->salemanOrderModel = new SalemanorderModel();
            foreach( $childs as $child )
            {
                $clinic_ids = $this->clinicModel->getClinicidsBySalemanID($child['id'],$year);//当年普通诊所数量
                $saleman['salemanNum'] = $this->getChildCountBySaleId($child['id'],$year);
                $saleman['doctorNum'] = $this->doctorModel->getCount($clinic_ids);
                $saleman['clinicNum'] = $this->clinicModel->getCount($child['id'],0,$year);
                $saleman['clinicSumNum'] = $this->clinicModel->getCount($child['id'],1,$year);
                $saleman['salmanVisitNum'] = $this->salemanSignModel->getVisitCountBySaleId($child['id'],$year);
                $saleman['salemanApplyNum'] = $this->salemanApplyModel->getApplyCountBySaleId($child['id'],$year);
                $saleman['salemanOrderNum'] = $this->salemanOrderModel->getClinicNumBySaleId($child['id'],$year);
                $saleman['salemanPrice'] = $this->salemanOrderModel->getSaleNumBySaleId($child['id'],$year);
                $saleman['quitePrice'] = $this->salemanOrderModel->getQuiteSaleNumBySaleId($child['id'],$year);
                $saleman['real_name'] = $child['real_name'];
                $countrylists[] = $saleman;
            }
            return $countrylists;
        }else{
            return false;
        }
    }

    /*
     *
     * 根据superior_id获取下一级有多少个数
     *
     * */
    public function getChildCountBySaleId($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        //$where['role_id'] = 4;
        $childs = $this->where($where)->select();
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $lists[] = $child;
            }
            return count( $lists );
        }else{
            return 0;
        }

    }


    /*
     *
     * 根据县总的id获取业务员个数
     *
     *
     * */
    public function getSalemanCountBySaleId($sale_id,$year)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $where['role_id'] = 4;
        $childs = $this->field('id')->where($where)->select();//县总id
        if( !$childs )
        {
            return 0;
        }else{
            $ids = array_column($childs,'id');
        }
        $where_s['superior_id'] = array('in',$ids);
        $where_s['status'] = 0;
        $where_s['role_id'] = 5;
        $salemanlists = $this->where($where_s)->select();
        if( !$salemanlists )
        {
            return 0;
        }
        foreach( $salemanlists as $salemanlist )
        {
            $add_year = date('Y',$salemanlist['add_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $lists[] = $salemanlist;
        }
        $count = count($lists);
        return $lists ? $count : 0;
    }

    /*
     *
     * 根据地总的id获取业务员信息列表
     *
     *
     * */
    public function getSalemanByDiZong($sale_id,$year)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $where['role_id'] = 4;//县总
        $childs = $this->field('id,add_time')->where($where)->select();//县总id
        if( !$childs )
        {
            return false;
        }else{
            $ids = array_column($childs,'id');//县总id
        }
        $where_s['superior_id'] = array('in',$ids);
        $where_s['status'] = 0;
        $salemanlists = $this->field('id,real_name,add_time')->where($where_s)->select();
        if( !$salemanlists )
        {
            return false;
        }
        foreach( $salemanlists as $salemanlist )
        {
            $add_year = date('Y',$salemanlist['add_time']);
            if( $year != $add_year )
            {
                continue;
            }
                $this->doctorModel = new DoctorModel();
                $this->clinicModel = new ClinicModel();
                $this->salemanSignModel = new SalemansignModel();
                $this->salemanApplyModel = new SalemanapplyModel();
                $this->salemanOrderModel = new SalemanorderModel();

                $clinic_ids = $this->clinicModel->getClinicidsBySalemanID($salemanlist['id'],$year);//当年普通诊所数量
                $saleman['doctorNum'] = $this->doctorModel->getCount($clinic_ids);
                $saleman['clinicNum'] = $this->clinicModel->getCount($salemanlist['id'],0,$year);
                $saleman['clinicSumNum'] = $this->clinicModel->getCount($salemanlist['id'],1,$year);
                $saleman['salmanVisitNum'] = $this->salemanSignModel->getVisitCountBySaleId($salemanlist['id'],$year);
                $saleman['salemanApplyNum'] = $this->salemanApplyModel->getApplyCountBySaleId($salemanlist['id'],$year);
                $saleman['salemanOrderNum'] = $this->salemanOrderModel->getClinicPriceBySaleId($salemanlist['id'],$year);
                $saleman['salemanPrice'] = $this->salemanOrderModel->getSalePriceBySaleId($salemanlist['id'],$year);
                $saleman['quitePrice'] = $this->salemanOrderModel->getQuiteSalePriceBySaleId($salemanlist['id'],$year);
                $saleman['real_name'] = $salemanlist['real_name'];
                $salelists[] = $saleman;
        }
        return $salelists ? $salelists : false;
    }


    /*
     *
     * 获取地总中的县总详情
     *
     * */
    public function getXianZongByDiZong($sale_id,$year=2016)
    {
        $ids = $this->getXianZongIdsByDiZong($sale_id,$year);
        $where_s['id'] = array('in',$ids);
        $where_s['status'] = 0;
        $salemanlists = $this->field('id,real_name,add_time')->where($where_s)->select();
        if( !$salemanlists )
        {
            return false;
        }
        foreach( $salemanlists as $salemanlist )
        {
            $add_year = date('Y',$salemanlist['add_time']);
            if( $year != $add_year )
            {
                continue;
            }
            $this->doctorModel = new DoctorModel();
            $this->clinicModel = new ClinicModel();
            $this->salemanSignModel = new SalemansignModel();
            $this->salemanApplyModel = new SalemanapplyModel();
            $this->salemanOrderModel = new SalemanorderModel();

            $saleman['countryCount'] = $this->getCountryCountByDiZong($salemanlist['id'],$year);
            $saleman['salemanNum'] = $this->getSalemanCountByDiZong($salemanlist['id'],$year);
            $saleman['purposeClinicNum'] = $this->getPurposeClinicCountByDiZong($salemanlist['id'],$year);//目标诊所数量
            $saleman['clinicNum'] = $this->getClinicCountByDiZong($salemanlist['id'],$year);
            $saleman['doctorNum'] = $this->doctorModel->getDoctorCountByDiZong($salemanlist['id'],$year);
            $saleman['salmanVisitNum'] = $this->salemanSignModel->getVisitCountByDiZong($salemanlist['id'],$year);//拜访次数
            $saleman['salemanApplyNum'] = $this->salemanApplyModel->getApplyCountByDiZong($salemanlist['id'],$year);
            $saleman['salemanOrderNum'] = $this->salemanOrderModel->getClinicPriceByDiZong($salemanlist['id'],$year);//销售
            $saleman['quitePrice'] = $this->salemanOrderModel->getQuitePriceByDiZong($salemanlist['id'],$year);
            $saleman['salemanPrice'] = $this->salemanOrderModel->getSalePriceByDiZong($salemanlist['id'],$year);
            $saleman['real_name'] = $salemanlist['real_name'];
            $salelists[] = $saleman;
        }
        return $salelists ? $salelists : false;
    }

    /*
     *
     * 获取地总中县总数量
     *
     * */
    public function getCountryCountByDiZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;//父id为地总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $saleman[] = $child;
            }
            return count( $saleman );
        }else{
            return 0;
        }
    }


    /*
     *
     * 获取地总中县总数量
     *
     * */
    public function getCountryListByDiZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;//父id为地总id
        $where['status'] = 0;//正常用户
        $childs = $this->field('id,real_name,add_time')->where($where)->select();//所有的子类业务员
        if( $childs )
        {
            foreach( $childs as $child )
            {
                $add_year = date('Y',$child['add_time']);
                if( $year != $add_year )
                {
                    continue;
                }
                $saleman[] = $child;
            }
            return !empty( $saleman ) ? $saleman : false;
        }else{
            return false;
        }
    }


    /*
     *
     * 根据地总id获取县总id
     *
     *
     * */
    public function getXianZongIdsByDiZong($sale_id,$year=2016)
    {
        $where['superior_id'] = $sale_id;
        $where['status'] = 0;
        $childs = $this->field('id')->where($where)->select();
        $ids = array_column($childs,'id');
        if( !empty($ids) )
        {
            return $ids;
        }else{
            return false;
        }
    }

    /*
     *
     * 根据地总id获取业务员id
     *
     * */
    public function getSalemanCountByDiZong($sale_id,$year=2016)
    {
        $ids = $this->getXianZongIdsByDiZong($sale_id,$year);
        $where['superior_id'] = array('in',$ids);//县总ids
        $where['status'] = 0;
        $count = $this->where($where)->count();
        return $count ? $count : 0;
    }

    /*
     *
     * 获取地总下的县总下的业务员的目标诊所
     *
     * */
    public function getPurposeClinicCountByDiZong($sale_id,$year=2016)
    {
        $ids = $this->getXianZongIdsByDiZong($sale_id,$year);//县总ids
        $purposeCount = 0;
        foreach( $ids as $id )
        {
            $count = $this->getPurposeClinicCountByXianZong($id,$year);
            $purposeCount += $count;
        }
        return $purposeCount;
    }

    /*
     *
     * 获取地总下的县总下的业务员的诊所
     *
     * */
    public function getClinicCountByDiZong($sale_id,$year=2016)
    {
        $count = 0;
        $ids = $this->getXianZongIdsByDiZong($sale_id,$year);//县总ids
        if( !empty( $ids ))
        {
            foreach ( $ids as $id )
            {
                $count += $this->getClinicCountByXianZong($id,$year);
            }
            return $count;
        }else{
            return 0;
        }
    }
}



