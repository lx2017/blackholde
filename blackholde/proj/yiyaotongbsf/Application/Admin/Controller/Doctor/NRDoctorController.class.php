<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:12
 */
namespace Admin\Controller\Doctor;
use Admin\Model\Doctor\DoctorModel;
use Think\Controller;

class NRDoctorController extends Controller
{
    /**
     * 验证手机号的合法性
     * @param $mobile string 手机号
     * @param int $type int 0添加, 其他id-编辑
     */
    public function checkMobile($mobile,$type=0){
        if(!$mobile) $this->ajaxReturn(false);
        $model = new DoctorModel();
        $rst = $model->checkMobile($mobile,$type);
        if($rst!==false){
            $this->ajaxReturn(true);
        }else{
            $this->ajaxReturn(false);
        }
    }

    /**
     * 查询id
     */
    public function search(){
        if(IS_POST){
            $type = I('post.type');
            $name = I('post.name');
            $res = array();
            switch (intval($type)){
                case 1:
                    //医生
                    $res = M('Doctor')->where(array('name'=>array('like',"%$name%")))->field('id,name')->select();
                    break;
                case 2:
                    //诊所
                    $res = M('Clinic')->where(array('clinic_name'=>array('like',"%$name%")))->field('id,clinic_name as name')->select();
                    break;
                case 3:
                    //患者
                    $res = M('Patient')->where(array('name'=>array('like',"%$name%")))->field('id,name')->select();
                    break;
            }
            if($res===false){
                $result = array('code'=>1,'msg'=>'查询错误');
            }else{
                $result = array('code'=>0,'data'=>$res);
            }
            $this->ajaxReturn($result);
        }else{
            $this->display();
        }
    }
}