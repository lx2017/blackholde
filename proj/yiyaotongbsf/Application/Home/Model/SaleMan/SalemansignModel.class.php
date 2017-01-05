<?php

/**
 * 业务员-签到拜访
 */

namespace Home\Model\SaleMan;

use Think\Model;

class SalemansignModel extends Base\BaseModel {

    protected $tableName = "saleman_sign";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->_return = array('code' => 2000, 'msg' => $this->_codeList[2000]);
    }

    /**
     * 获取拜访签到列表
     * @param int $salemanId   业务员、县总ID
     * @param double $longitude  精读
     * @param double $latitude    维度
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function GetCateSignClinic($salemanId, $longitude, $latitude) {
        if (empty($salemanId)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }
        if (empty($longitude) || empty($latitude)) {
            $this->_return = array('code' => 2020, 'msg' => $this->_codeList[2020]);
            return $this->_return;
        }

        //验证业务是否存在
        $salemanModel = D('Saleman', 'Model');
        $resultSaleman = $salemanModel->CheckSalemanUnique($salemanId);
        if ($resultSaleman['code'] == 2015) {
            $this->_return = array('code' => 2015, 'msg' => $this->_codeList[2015]);
            return $this->_return;
        }

        //业务员集合
        $salemanIdList = array();
        switch ($resultSaleman['data']['role_id']) {
            //业务员
            case 5:
                $salemanIdList[] = $resultSaleman['data']['id'];
                break;
            //县总
            case 4:
                //查询县总下级业务员集合
                $salemanModel = D('Saleman', 'Model');
                $salemanList = $salemanModel->field('id')->where(array('superior_id' => array('eq', $resultSaleman['data']['id'])))->select();
                if (!check_array($salemanList)) {
                    $this->_return = array('code' => 2022, 'msg' => $this->_codeList[2022]);
                    return $this->_return;
                }
                $salemanIdList = array_keys(set_array_key($salemanList, 'id'));
                break;

            default:
                $this->_return = array('code' => 2021, 'msg' => $this->_codeList[2021]);
                return $this->_return;
                break;
        }

        //查询诊所集合
        $clinicModel = M('clinic');
        $whereClinic = array(
            'saleman_id' => array('in', $salemanIdList)
        );
        $fieldClinic = array('id', 'name', 'pic', 'specialty', 'score', 'treatment_volume', 'lng', 'lat');
        $clinicList = $clinicModel->where($whereClinic)->field($fieldClinic)->select();
        if (!check_array($clinicList)) {
            $this->_return = array('code' => 2010, 'msg' => $this->_codeList[2010]);
            return $this->_return;
        }

        //查询今天已签到诊所集合
        $whereSignClinic = array(
            'saleman_id' => array('in', $salemanIdList),
            'sign_date' => array('eq', date('Y-m-d'))
        );
        $fieldSignClinic = array('clinic_id');
        $signClinicList = $this->where($whereSignClinic)->field($fieldSignClinic)->select();
        $signClinicIdList = array();
        if (check_array($signClinicList)) {
            $signClinicIdList = array_keys(set_array_key($signClinicList, 'clinic_id'));
        }


        //组装诊所
        $listClinic = array();
        foreach ($clinicList as $key => $value) {
            //已签到诊所
            if (in_array($value['id'], $signClinicIdList)) {
                $listClinic['already_sign'][] = $value;
                continue;
            }

            //计算业务员离诊所距离
            $distance = get_distance($value['lat'], $value['lng'], $latitude, $longitude);
            if ($distance <= 100) {
                $listClinic['can_sign'][] = $value;
                continue;
            } elseif ($distance <= 1000 && $distance > 100) {
                $listClinic['nearby'][] = $value;
                continue;
            }
        }

        //返回
        $data = $listClinic;
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200], 'data' => $data);
        return $this->_return;
    }

    /**
     * 诊所签到
     * @param int $salemanId   业务员、县总ID
     * @param double $longitude  精读
     * @param double $latitude    维度
     * @param int $clinicId    诊所ID
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function SignClinic($salemanId, $longitude, $latitude, $clinicId) {
        if (empty($salemanId)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }
        if (empty($longitude) || empty($latitude)) {
            $this->_return = array('code' => 2020, 'msg' => $this->_codeList[2020]);
            return $this->_return;
        }
        if (empty($clinicId)) {
            $this->_return = array('code' => 2009, 'msg' => $this->_codeList[2009]);
            return $this->_return;
        }

        //验证业务员是否存在
        $salemanModel = D('Saleman', 'Model');
        $resultSaleman = $salemanModel->CheckSalemanUnique($salemanId);
        if ($resultSaleman['code'] == 2015) {
            $this->_return = array('code' => 2015, 'msg' => $this->_codeList[2015]);
            return $this->_return;
        }

        //业务员集合
        $salemanIdList = array();
        switch ($resultSaleman['data']['role_id']) {
            //业务员
            case 5:
                $salemanIdList[] = $resultSaleman['data']['id'];
                break;
            //县总
            case 4:
                //查询县总下级业务员集合
                $salemanModel = D('Saleman', 'Model');
                $salemanList = $salemanModel->field('id')->where(array('superior_id' => array('eq', $resultSaleman['data']['id'])))->select();
                if (!check_array($salemanList)) {
                    $this->_return = array('code' => 2022, 'msg' => $this->_codeList[2022]);
                    return $this->_return;
                }
                $salemanIdList = array_keys(set_array_key($salemanList, 'id'));
                break;

            default:
                $this->_return = array('code' => 2021, 'msg' => $this->_codeList[2021]);
                return $this->_return;
                break;
        }

        //查询当前诊所是否是当前业务员或者县总的
        $clinicModel = M('clinic');
        $whereClinic = array(
            'saleman_id' => array('in', $salemanIdList),
            'id' => array('eq', $clinicId)
        );
        $fieldClinic = array('id', 'name', 'lng', 'lat');
        $infoClinic = $clinicModel->where($whereClinic)->field($fieldClinic)->find();
        if (!check_array($infoClinic)) {
            $this->_return = array('code' => 2023, 'msg' => $this->_codeList[2023]);
            return $this->_return;
        }

        //查询当前业务员或县总今天对当前诊所是否已签到
        $whereSignClinic = array(
            'saleman_id' => array('eq', $salemanId),
            'clinic_id' => array('eq', $clinicId),
            'sign_date' => array('eq', date('Y-m-d'))
        );
        $fieldSignClinic = array('clinic_id');
        $signClinicList = $this->where($whereSignClinic)->field($fieldSignClinic)->select();
        if (check_array($signClinicList)) {
            $this->_return = array('code' => 2024, 'msg' => $this->_codeList[2024]);
            return $this->_return;
        }

        //签到
        $dataSalemanSign = array(
            'clinic_id' => $clinicId,
            'clinic_name' => $infoClinic['name'],
            'saleman_id' => $salemanId,
            'sign_time' => time(),
            'sign_date' => date('Y-m-d', time())
        );
        $add_num = $this->data($dataSalemanSign)->add();
        if($add_num<=0){
            $this->_return = array('code' => 2025, 'msg' => $this->_codeList[2025]);
            return $this->_return;
        }

        //返回
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200]);
        return $this->_return;
    }

}
