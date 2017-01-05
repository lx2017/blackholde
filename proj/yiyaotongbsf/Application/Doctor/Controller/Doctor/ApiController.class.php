<?php
/**
 * 医生相关接口
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Controller\Doctor;

use Think\Controller;

class ApiController extends Controller{

    /**
     * 添加咨询信息
     * @param $pid
     * @param $did
     */
    public function addConsult($pid,$did){
        $pid = intval($pid);
        if(!$pid || !$did){
            responseJson('1','参数错误');
        }
        //得到医生id
        $did = getHxUser($did,1,true);
        if(!$did){
            responseJson('3','医生环信名错误');
        }
        //保存数据
        $where = array(
            'patient_id'=>$pid,
            'doctor_id'=>$did,
        );
        //检查是否存在
        $res = M('DoctorConsult')->where($where)->field('id,now,no_read_num')->find();
        if($res){
            //上次发送方
            $data = array(
                'id'=>$res['id'],
                'now'=>1,
                'no_read_num'=>0,
                'date'=>date('Y-m-d H:i:s'),
                'status'=>1,
            );
            //更新
            $rst = M('DoctorConsult')->save($data);
        }else{
            //添加
            $data = array(
                'patient_id'=>$pid,
                'doctor_id'=>$did,
                'date2'=>date('Y-m-d H:i:s'),
                'date'=>date('Y-m-d H:i:s'),
            );
            $rst = M('DoctorConsult')->add($data);
            if($rst){
                D('CommonDoctor')->putToCount(3,$did);
            }
        }
        if($rst===false){
            responseJson('2','保存信息失败');
        }else{
            responseJson('0','保存成功');
        }
    }

    /**
     * 医生端更新咨询
     * @param $did
     * @param $pid
     */
    public function addDoctorConsult($did,$pid){
        $did = intval($did);
        if(!$pid || !$did){
            responseJson('1','参数错误');
        }
        //得到患者id
        $pid = getHxUser($pid,3,true);
        if(!$pid){
            responseJson('3','患者环信名错误');
        }
        //检查是否存在
        $where = array(
            'patient_id'=>$pid,
            'doctor_id'=>$did,
        );
        $res = M('DoctorConsult')->where($where)->field('id,now,no_read_num')->find();
        if($res){
            //上次发送方
            $data = array(
                'id'=>$res['id'],
                'date2'=>date('Y-m-d H:i:s'),
                'status'=>2,
            );
            if($res['now']==2){
                $data['no_read_num'] = $res['no_read_num']+1;//更新未读消息数
            }else{
                $data['now'] = 2;
                $data['no_read_num'] = 1;//重置未读消息
            }
            //更新
            $rst = M('DoctorConsult')->save($data);
        }else{ // 没有咨询消息时, 此处的处理
            //添加普通消息
            $data = array(
                'patient_id'=>$pid,
                'doctor_id'=>$did,
                'date2'=>date('Y-m-d H:i:s'),
                'date'=>date('Y-m-d H:i:s'),
                'type'=>1,//医生发起的
                'status'=>2,//已读
            );
            $rst = M('DoctorConsult')->add($data);
        }
        if($rst===false){
            responseJson('2','保存信息失败');
        }else{
            responseJson('0','保存成功');
        }
    }

    /**
     * 清空未读消息
     * @param $pid
     * @param $did
     */
    public function clearNonRead($pid,$did){
        $pid = intval($pid);
        if(!$pid || !$did){
            responseJson('1','参数错误');
        }
        //得到医生id
        $did = getHxUser($did,1,true);
        if(!$did){
            responseJson('3','医生环信名错误');
        }
        //清空消息
        $where = array(
            'patient_id'=>$pid,
            'doctor_id'=>$did,
        );
        $res = M('DoctorConsult')->field('id,now')->where($where)->find();
        if($res===false){
            responseJson('0','操作成功');
        }
        if($res['now']==2){//上次发送方为医生
            //清空数据
            $d = array(
                'id'=>$res['id'],
                'now'=>1,
                'no_read_num'=>0,
            );
            $res = M('DoctorConsult')->save($d);
            if($res===false){
                responseJson('2','操作失败');
            }
        }
        responseJson('0','操作成功');
    }

    /**
     * 保存定位信息
     * @param $id
     * @param int $lng
     * @param int $lat
     * @param string $address
     */
    public function getLocate($id,$lng=0,$lat=0,$address=''){
        $id = intval($id);
        $lng = floatval($lng);
        $lat = floatval($lat);
        if(!$id || !$address) responseJson('1','参数错误');
        //修改诊所定位
        $d = array(
            'id'=>$id,
            'clinic_lng'=>$lng,
            'clinic_lat'=>$lat,
            'clinic_address'=>$address,
        );
        $res = M('Clinic')->save($d);
        if($res===false){
            responseJson('2','操作失败');
        }else{
            responseJson('0','操作成功');
        }
    }
    /**
     * 保存医生极光RegID
     * @param $UID
     * @param string $RegId
     */
    public function getRegID($UID,$RegId){

        if(!$UID&& !$RegId) responseJson('1','参数错误');
        $data=M('DoctorPush')->where(array('doctor_id'=>$UID))->find();
        if($data){
        //修改
        $d = array(
            'register_id'=>$RegId,
        );
        $res = M('DoctorPush')->where(array('doctor_id'=>$UID))->save($d);

            if($res!==false){
                responseJson('0','操作成功');
            }else{
                responseJson('2','操作失败');
            }
        }else{
            //新增
            $d = array(
                'doctor_id'=>$UID,
                'register_id'=>$RegId,
            );
            $res = M('DoctorPush')->add($d);
            if($res===false){
                responseJson('2','操作失败');
            }else{
                responseJson('0','操作成功');
            }
        }


    }


}