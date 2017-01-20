<?php
/**
 *
 * User: 卖女孩的小火柴
 * Date: 16/11/3
 * Time: 下午3:04
 *
 */

namespace Home\Model\WorkSummary;


use Think\Model;
use Think\Model\RelationModel;

class DoctorModel extends RelationModel
{
    protected $_link = array(

        'doctor_clinic' => array(
            'mapping_type' => self::BELONGS_TO,
            'mapping_fields' => 'name',
            'class_name' => 'clinic',
            'foreign_key' => 'clinic_id',
//            'as_fields' => 'clinic_name',
            'mapping_name' => 'clinic_name',
        ),

    );
    public function _initialize()
    {
        parent::_initialize();
    }


    /*
     *
     * 获取所有业务员下的诊所里的医生
     * @return $doctors or false
     *
     * */
    public function getAllDoctorsByIds($clinic_ids)
    {
        $where['ymt_doctor.status'] = 0;
        $where['clinic_id'] = array('in',$clinic_ids);
        $doctors = $this->relation(true)->field('name,good,score,clinic_id')->where($where)->select();
        //echo $this->getLastSql();die;
        return $doctors ? $doctors : false;
    }


    /*
     *
     * 获取已经删除的医生
     * @return $doctors or false;
     *
     * */
    public function getRemovedDoctors()
    {
        $where['ymt_doctor.status'] = 2;
//        $doctors = $this->field('ymt_doctor.name as doctorname,good,ymt_doctor.score,ymt_clinic.name as clinicname')
//                        ->join('ymt_clinic on ymt_clinic.id = clinic_id')
//                        ->where($where)->select();
        $doctors = $this->relation(true)->field('name,good,score,clinic_id')->where($where)->select();
        echo $this->getLastSql();
        return $doctors ? $doctors : false;
    }

    /*
     *
     * 获取医生的个数
     * @return $count
     *
     * */
    public function getCount($clinic_ids)
    {
        $where['ymt_doctor.status'] = 0;
        $where['type'] = 1;
        $where['clinic_id'] = array('in',$clinic_ids);
        $count = $this->where($where)->count();
        return $count ? $count : 0;
    }


    /*
     *
     * 根据诊所id获取医生数量
     *
     * */
    public function getDoctorCountByClinicIds($ids)
    {
        if( empty($ids))
        {
            return 0;
        }
        $where['status'] = 0;
        $where['type'] = 1;
        $where['clinic_id'] = array('in',$ids);
        $count = $this->where($where)->count();
        return $count;
    }

    /*
     *
     * 根据县总id获取县总下的业务员下的诊所下的医生数量
     *
     * */
    public function getDoctorCountByXianZong($sale_id,$year)
    {
        $salemanModel = new SalemanModel();
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        $salemanIds = array_column($salemans,'id');
        $clinicModel = new ClinicModel();
        $clinics = $clinicModel->getClinicsBySaleId($salemanIds,0);
        $clinicIds = array_column($clinics,'id');
        if( !$clinicIds )
        {
            return 0;
        }
        $count = $this->getDoctorCountByClinicIds( $clinicIds );
        return $count ? $count : 0;
    }

    /*
     *
     *
     * 根据地总id获取县总下的业务员下的诊所下的医生的数量
     *
     * */
    public function getDoctorCountByDiZong($sale_id,$year=2016)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);//县总id
        $salemans = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        $salemanIds = array_column($salemans,'id');
        $clinicModel = new ClinicModel();
        $clinics = $clinicModel->getClinicsBySaleId($salemanIds,0);
        $clinicIds = array_column($clinics,'id');
        if( !$clinicIds )
        {
            return 0;
        }
        $doctors = $this->getAllDoctorsByIds( $clinicIds );
        return count( $doctors );
    }


    /*
     *
     * 根据县总id获取县总下的业务员下的诊所下的医生
     *
     * */
    public function getAllDoctorsByXianZong($sale_id,$year)
    {
        $salemanModel = new SalemanModel();
        //var_dump($sale_id);die;
        $salemans = $salemanModel->getSalemanIdsByXianZong($sale_id,$year);
        $salemanIds = array_column($salemans,'id');
        //var_dump($salemanIds);die;
        $clinicModel = new ClinicModel();
        $clinics = $clinicModel->getClinicsBySaleId($salemanIds,0);
        $clinicIds = array_column($clinics,'id');
        //var_dump($clinicIds);die;
        if( !$clinicIds )
        {
            return 0;
        }
        $doctors = $this->getAllDoctorsByIds( $clinicIds );
        return $doctors ? $doctors : false;
    }


    /*
     *
     * 根据地总id获取县总下的业务员下的诊所下的所有医生
     *
     *
     * */
    public function getAllDoctorsByDiZong($sale_id,$year)
    {
        $salemanModel = new SalemanModel();
        $ids = $salemanModel->getXianZongIdsByDiZong($sale_id,$year);
        $salemans = $salemanModel->getSalemanIdsByXianZongIds($ids,$year);
        $salemanIds = array_column($salemans,'id');
        $clinicModel = new ClinicModel();
        $clinics = $clinicModel->getClinicsBySaleId($salemanIds,0);
        $clinicIds = array_column($clinics,'id');
        if( !$clinicIds )
        {
            return 0;
        }
        $doctors = $this->getAllDoctorsByIds( $clinicIds );
        return $doctors ? $doctors : false;
    }

}