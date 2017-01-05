<?php
/**
 * 患者端相关接口
 * Created by PhpStorm.
 * User: jiaolele
 * Date: 2016/11/26 0026
 * Time: 下午 15:31
 */
namespace Patient\Controller\Patient;

use Think\Controller;
use Patient\Model\Patient\PatientModel;

class ApiController extends Controller{

    /**
     * 得到环信用户的用户名和密码
     * @param $id int 用户id
     * @param int $type int 用户类型
     */
    public function getHxUser($UID,$type=3){
        if(empty($UID)) responseJson("1",'非法用户id');
        //得到用户类型
        $pre = '';
        switch(intval($type)){
            case 1:
                $pre = 'doctor';break;
            case 2:
                $pre = 'clinic';break;
            case 3:
                $pre = 'patient';break;
        }
        if(empty($pre)) responseJson("2",'非法用户类型');
        //得到用户密码
        $rst = getHxUser($UID,$type);
        //检测用户是否存在
        $flag = D('Huanxin')->getUser($rst['username']);
        if($flag==false){
            //注册该用户
            $mflag = D('Huanxin')->createUser($rst['username'],$rst['password']);
            if($mflag==false){
                responseJson("3",'用户不存在',$rst);
            }
        }
        //返回结果
        responseJson("0",'获取成功',$rst);
    }


    /**
     * 患者的经度、纬度
     */
  public  function  user_lat_lng(){
        //接受客户端传来的json数据
        $json_string = I('get.');
        if($json_string['Latitude'] && $json_string['Longitude']){
            $patientmodel=new PatientModel();
            $patientmodel->setLngAndLat($json_string['Longitude'],$json_string['Latitude']);//保存定位信息
            $this->ajaxReturn(array('result'=>"0"));
        }else{
            $this->ajaxReturn(array('result'=>"1"));
        }
    }

    /**
     * 保存患者极光RegID
     * @param $UID
     * @param string $RegId
     */
    public function getRegID($UID,$RegId){

        if(!$UID&& !$RegId) responseJson('1','参数错误');
        $data=M('PatientPush')->where(array('patient_id'=>$UID))->find();
        if($data){
            //修改
            $d = array(
                'register_id'=>$RegId,
            );
            $res = M('PatientPush')->where(array('patient_id'=>$UID))->save($d);

            if($res!==false){
                responseJson('0','操作成功');
            }else{
                responseJson('2','操作失败');
            }

        }else{
            //新增
            $d = array(
                'patient_id'=>$UID,
                'register_id'=>$RegId,
            );
            $res = M('PatientPush')->add($d);
            if($res===false){
                responseJson('2','操作失败');
            }else{
                responseJson('0','操作成功');
            }
        }
    }






}