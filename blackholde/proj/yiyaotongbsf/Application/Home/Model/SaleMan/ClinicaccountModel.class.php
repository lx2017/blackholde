<?php

/**
 * 业务员-我的诊所
 */

namespace Home\Model\SaleMan;

use Think\Model;

class ClinicaccountModel extends Base\BaseModel {

    protected $tableName = "clinic_account";
    protected $_return;

    public function _initialize() {
        parent::_initialize();
        $this->_return = array('code' => 2000, 'msg' => $this->_codeList[2000]);
    }

    /**
     * 业务员添加我的诊所
     * @param int   $saleman_id 业务员id
     * @param int $account_phone 诊所账号手机号
     * @param varchar $clinic_name  诊所名称
     * @param varchar $clinic_manager 诊所负责人
     * @param varchar $password 诊所账号密码【默认配置文件saleman里的DEF_ACCOUNT_PWD】
     * @return array code:200:success【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function AddClinicAccount($saleman_id, $account_phone, $clinic_name, $clinic_manager, $password = '') {
        $s1 = array('s1', 's2', 's3', 's4', 's5');
        $s2 = array('s1', 's2', 's3');
        dump(array_diff($s1, $s2));
        die();

        //简单判断传入参数是否为空
        if (empty($account_phone)) {
            $this->_return = array('code' => 2001, 'msg' => $this->_codeList[2001]);
            return $this->_return;
        }
        if (empty($clinic_name)) {
            $this->_return = array('code' => 2002, 'msg' => $this->_codeList[2002]);
            return $this->_return;
        }
        if (empty($clinic_manager)) {
            $this->_return = array('code' => 2003, 'msg' => $this->_codeList[2003]);
            return $this->_return;
        }

        $saleman_id = empty($saleman_id) ? '' : $saleman_id;

        //验证诊所账号唯一性
        $resultClinicAccount = $this->CheckAccountUnique($account_phone);
        if ($resultClinicAccount['code'] == 2004) { //账号已存在
            //获取诊所账号信息
            $clinicAccountInfo = $resultClinicAccount['data'];

            //开启事务
            $this->startTrans();

            //添加诊所负责人
            $clinicManagerModel = M('clinic_manager');
            $dataClinicManager = array(
                'name' => $clinic_manager
            );
            $clinicManagerId = $clinicManagerModel->data($dataClinicManager)->add();
            if ($clinicManagerId <= 0) {
                $this->rollback();
                $this->_return = array('code' => 2007, 'msg' => $this->_codeList[2007]);
                return $this->_return;
            }

            //添加诊所信息
            $clinicModel = M('clinic');
            $dataClinic = array(
                'name' => $clinic_name,
                'clinic_manager_id' => $clinicManagerId,
                'clinic_account_id' => $clinicAccountInfo['id'],
                'saleman_id' => $saleman_id
            );
            $clinic_id = $clinicModel->data($dataClinic)->add();
            if ($clinic_id <= 0) {
                $this->rollback();
                $this->_return = array('code' => 2008, 'msg' => $this->_codeList[2008]);
                return $this->_return;
            }

            //提交事务
            $this->commit();
        } elseif ($resultClinicAccount['code'] == 2005) {//账号不存在
            //验证密码
            $password = empty($password) ? C('DEF_ACCOUNT_PWD') : $password;

            //密码加密
            $encryptPassWord = think_ucenter_md5($password, C('ACCOUNT_KEY'));

            //开启事务
            $this->startTrans();

            //添加诊所账号信息
            $dataClinicAccount = array(
                'account_phone' => $account_phone,
                'password' => $encryptPassWord,
                'clinic_num' => 1,
                'add_time' => time()
            );
            $clinicAccountId = $this->data($dataClinicAccount)->add();
            if ($clinicAccountId <= 0) {
                $this->rollback();
                $this->_return = array('code' => 2006, 'msg' => $this->_codeList[2006]);
                return $this->_return;
            }

            //添加诊所负责人
            $clinicManagerModel = M('clinic_manager');
            $dataClinicManager = array(
                'name' => $clinic_manager
            );
            $clinicManagerId = $clinicManagerModel->data($dataClinicManager)->add();
            if ($clinicManagerId <= 0) {
                $this->rollback();
                $this->_return = array('code' => 2007, 'msg' => $this->_codeList[2007]);
                return $this->_return;
            }

            //添加诊所信息
            $clinicModel = M('clinic');
            $dataClinic = array(
                'name' => $clinic_name,
                'clinic_manager_id' => $clinicManagerId,
                'clinic_account_id' => $clinicAccountId
            );
            $clinic_id = $clinicModel->data($dataClinic)->add();
            if ($clinic_id <= 0) {
                $this->rollback();
                $this->_return = array('code' => 2008, 'msg' => $this->_codeList[2008]);
                return $this->_return;
            }

            //提交事务
            $this->commit();
        } else {
            return $this->_return;
        }

        //返回信息
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200]);
        return $this->_return;
    }

    /**
     * 验证诊所账号唯一性
     * @param int $account_phone 诊所账号手机
     * @return array code:2004:诊所账户已存在;code:2005:诊所账户不存在【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function CheckAccountUnique($account_phone) {
        if (empty($account_phone)) {
            $this->_return = array('code' => 2001, 'msg' => $this->_codeList[2001]);
            return $this->_return;
        }

        //组装查询条件
        $where = array(
            'account_phone' => array('eq', $account_phone)
        );

        //查询
        $clinicAccount = $this->where($where)->find();
        if (check_array($clinicAccount)) {
            $this->_return = array('code' => 2004, 'msg' => $this->_codeList[2004], 'data' => $clinicAccount);
            return $this->_return;
        }

        $this->_return = array('code' => 2005, 'msg' => $this->_codeList[2005]);
        return $this->_return;
    }

    /**
     * 删除诊所
     * @param int $clinic_id    诊所id
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function delClinic($clinic_id) {
        if (empty($clinic_id)) {
            $this->_return = array('code' => 2009, 'msg' => $this->_codeList[2009]);
            return $this->_return;
        }

        //查询诊所是否存在
        $clinicModel = M('clinic');
        $whereClinic = array(
            'id' => array('eq', $clinic_id)
        );
        $infoClinic = $clinicModel->where($whereClinic)->find();
        if (!check_array($infoClinic)) {
            $this->_return = array('code' => 2010, 'msg' => $this->_codeList[2010]);
            return $this->_return;
        }

        //开启事务
        $clinicModel->startTrans();

        //删除诊所
        $delNum = $clinicModel->where($whereClinic)->delete();
        if ($delNum <= 0) {
            $clinicModel->rollback();
            $this->_return = array('code' => 2008, 'msg' => $this->_codeList[2008]);
            return $this->_return;
        }

        //删除诊所负责人
        $clinicManagerModel = M('clinic_manager');
        $whereClinicManager = array(
            'id' => array('eq', $infoClinic['clinic_manager_id'])
        );
        $delNum = $clinicManagerModel->where($whereClinicManager)->delete();
        if ($delNum <= 0) {
            $clinicModel->rollback();
            $this->_return = array('code' => 2007, 'msg' => $this->_codeList[2007]);
            return $this->_return;
        }

        //减少诊所账号下诊所数量
        $excNum = $this->where('id=' . $infoClinic['clinic_account_id'])->setDec('clinic_num');
        if ($excNum <= 0) {
            $clinicModel->rollback();
            $this->_return = array('code' => 2006, 'msg' => $this->_codeList[2006]);
            return $this->_return;
        }

        //提交事务
        $clinicModel->commit();

        //返回
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200]);
        return $this->_return;
    }

    /**
     * 获取诊所信息<分页>
     * @param int $saleman_id 业务员ID
     * @param int $page 页码<默认：0：第一页>
     * @param int $limit    每页显示条数<默认配置文件saleman里的DEF_LIMIT>
     * @return array    code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function getPageClinicList($saleman_id, $page = 0, $limit = 0) {
        if (empty($saleman_id)) {
            $this->_return = array('code' => 2011, 'msg' => $this->_codeList[2011]);
            return $this->_return;
        }
        if (!is_numeric($page) || $page < 0) {
            $page = 0;
        }
        if (!is_numeric($limit) || $limit < 0) {
            $limit = C('DEF_LIMIT');
        }

        //分页查询
        $fieldClinic = array('id', 'name', 'pic', 'specialty', 'score', 'treatment_volume');
        $whereClinic = array(
            'saleman_id' => array('eq', $saleman_id)
        );
        $orderClinic = array('id desc');
        $count = $this->field($fieldClinic)->where($whereClinic)->count();
        $Page = new \Think\Page($count, $limit); //实例化分页类
        $show = $Page->show(); // 分页显示输出
        $listClinic = $this->field($fieldClinic)->where($whereClinic)->order($orderClinic)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        //返回

        $data = array(
            'list' => $listClinic,
            'page' => $show
        );
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200], 'data' => $data);
        return $this->_return;
    }

    /**
     * 查询诊所详细信息
     * @param int $clinic_id    诊所ID
     * @param int $saleman_id   业务员ID
     * @return array code值【code值对应配置文件saleman里的CODE_LIST】
     * @author lcr<592799421@qq.com>
     */
    public function getClinicDetail($clinic_id, $saleman_id = 0) {
        if (empty($clinic_id)) {
            $this->_return = array('code' => 2009, 'msg' => $this->_codeList[2009]);
            return $this->_return;
        }

        //查询诊所
        $clinicModel = M('clinic');
        $whereClinic = array(
            'id' => array('eq', $clinic_id)
        );
        if (is_numeric($saleman_id) && $saleman_id > 0) {
            $whereClinic['saleman_id'] = array('eq', $saleman_id);
        }
        $filedClinic = array(
            'id', 'name', 'licence', 'phone', 'address', 'introduction', 'pic', 'is_delete', 'specialty', 'clinic_manager_id', 'lat', 'lng'
        );
        $infoClinic = $clinicModel->where($whereClinic)->field($filedClinic)->find();
        if (!check_array($infoClinic)) {
            $this->_return = array('code' => 2012, 'msg' => $this->_codeList[2012]);
            return $this->_return;
        }

        //查询诊所负责人
        $clinicManagerModel = M('clinic_manager');
        $whereClinicManager = array(
            'id' => array('eq', $infoClinic['clinic_manager_id'])
        );
        $filedClinicManager = array(
            'id', 'name', 'mobile', 'sex', 'diploma', 'idcard', 'idcard_pic', 'degree', 'age'
        );
        $infoClinicManager = $clinicManagerModel->where($whereClinicManager)->field($filedClinicManager)->find();
        if (!check_array($infoClinicManager)) {
            $this->_return = array('code' => 2013, 'msg' => $this->_codeList[2013]);
            return $this->_return;
        }

        //查询诊所医生
        $doctorModel = M('doctor');
        $whereDoctor = array(
            'clinic_id' => array('eq', $infoClinic['id'])
        );
        $filedDoctor = array(
            'id', 'name', 'image', 'good', 'sum', 'num'
        );
        $listDoctor = $doctorModel->where($whereDoctor)->field($filedDoctor)->select();
        if (!check_array($listDoctor)) {
            $listDoctor = array();
        }

        //计算每个医生的平均评分
        foreach ($listDoctor as $key => $value) {
            $listDoctor[$key]['avg_score'] = round($value['sum'] / $value['num'], 2);
        }

        //组装个data值
        $infoClinic['clinic_manager'] = $infoClinicManager;
        $infoClinic['doctor_list'] = $listDoctor;

        //返回值
        $this->_return = array('code' => 200, 'msg' => $this->_codeList[200], 'data' => $infoClinic);
        return $this->_return;
    }

}
