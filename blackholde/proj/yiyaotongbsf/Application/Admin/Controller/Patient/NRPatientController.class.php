<?php

namespace Admin\Controller\Patient;
use Admin\Model\Patient\PatientModel;
use Think\Controller;

class NRPatientController extends Controller
{
    /**
     * 验证手机号的合法性
     * @param $mobile string 手机号
     * @param int $type int 0添加, 其他id-编辑
     */
    public function checkMobile($mobile,$type=0){
        if(!$mobile) $this->ajaxReturn(false);
        $model = new PatientModel();
        $rst = $model->checkMobile($mobile,$type);
        if($rst!==false){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }
}