<?php
/**
 * 公共医生模型
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/8 0008
 * Time: 下午 14:18
 */

namespace Common\Model;

class CommonDoctorModel
{
    public $error = '';

    /**
     * 更新医生表/诊所表相关统计
     * @param $type 1-诊断量 2-预约量 3-咨询量 4-评分
     * @param $doctor_id int 医生id
     * @param $other int 评分
     * @param $clinic_id int 诊所id
     * @return bool
     */
    public function putToCount($type,$doctor_id,$other=0,$clinic_id=0){
        if(!$type || !$doctor_id) return false;
        if(in_array($type,array(1,4)) && $clinic_id==0){
            //得到诊所id
            $clinic_id = M('Doctor')->getFieldById($doctor_id,'clinic_id');
        }
        switch ($type){
            case 1:
                M('Doctor')->where(array('id'=>$doctor_id))->setInc('theat_num');//诊断量
                M('Clinic')->where(array('id'=>$clinic_id))->setInc('clinic_treatment_volume');
                break;
            case 2:
                M('Doctor')->where(array('id'=>$doctor_id))->setInc('appoint_num');//预约量
                break;
            case 3:
                M('Doctor')->where(array('id'=>$doctor_id))->setInc('consult_num');//咨询量
                break;
            case 4:
                //评分量
                //更新医生评分
                $sum_count = M('Doctor')->getFieldById($doctor_id,'sum_count');
                if(!$sum_count) $sum_count = "0+0";
                $res = explode('+',$sum_count);
                $sum = (int)$res[0]+$other*2;
                $count = (int)$res[1]+1;
                $score = round($sum/$count ,2);
                $sum_count = $sum.'+'.$count;
                //保存到doctor表中
                $data = array(
                    'score'=>$score,
                    'sum_count'=>$sum_count,
                );
                M('Doctor')->where(array('id'=>$doctor_id))->save($data);
                //更新诊所评分
                $sum_count = M('Clinic')->getFieldById($clinic_id,'sum_count');
                if(!$sum_count) $sum_count = "0+0";
                $res = explode('+',$sum_count);
                $sum = (int)$res[0]+$other*2;
                $count = (int)$res[1]+1;
                $score = round($sum/$count ,2);
                $sum_count = $sum.'+'.$count;
                //保存到诊所表中
                $data = array(
                    'clinic_score'=>$score,
                    'sum_count'=>$sum_count,
                );
                M('Clinic')->where(array('id'=>$clinic_id))->save($data);
                break;
        }
        return true;
    }

    /**
     * 创建负责人信息
     * @param $data
     * @return  false
     */
    public function addManager($data){
        $data['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);
        $res = M('Doctor')->add($data);
        if($res===false){
            $this->error = '添加失败';
            return false;
        }
        //添加环信账户
        $info = getHxUser($res,1);
        D('Huanxin')->createUser($info['username'],$info['password']);
        return true;
    }

    /**
     * 删除负责人信息
     * @param $id
     * @return  false
     */
    public function deleteManager($id){
        $res = M('Doctor')->delete($id);
        if($res===false){
            $this->error = '删除失败';
            return false;
        }
        //删除环信账户
        $info = getHxUser($id,1);
        D('Huanxin')->deleteUser($info['username']);
        return true;
    }

    /**
     * 删除患者
     * @param $id
     * @return bool
     */
    public function deletePatient($id){
        $model = M('Patient');
        $model->startTrans();
        //删除患者
        $res = $model->delete($id);
        if($res===false){
            $this->error = '删除患者失败';
            $model->rollback();
            return false;
        }
        //删除咨询
        $res = M('DoctorConsult')->where('patient_id='.$id)->delete();
        if($res===false){
            $this->error = '删除咨询失败';
            $model->rollback();
            return false;
        }
        //删除关注
        $res = M('Attention')->where('patient_id='.$id)->delete();
        if($res===false){
            $this->error = '删除关注失败';
            $model->rollback();
            return false;
        }
        //删除预约
        $res = M('DoctorAppoint')->where('patient_id='.$id)->delete();
        if($res===false){
            $this->error = '删除预约失败';
            $model->rollback();
            return false;
        }
        //删除诊疗
        $res = M('DoctorTreatment')->where('patient_id='.$id)->delete();
        if($res===false){
            $this->error = '删除诊疗失败';
            $model->rollback();
            return false;
        }
        //删除评分
        $res = M('DoctorAssess')->where('patient_id='.$id)->delete();
        if($res===false){
            $this->error = '删除评价失败';
            $model->rollback();
            return false;
        }
        //删除环信
        $info = getHxUser($id,3);
        D('Huanxin')->deleteUser($info['username']);
        return $model->commit();
    }

    /**
     * 删除医生
     * @param $id
     * @return bool
     */
    public function deleteDoctor($id){
        $model = M('Doctor');
        $model->startTrans();
        //删除医生
        $data = array(
            'id'=>$id,
            'status'=>2,
        );
        $res = $model->save($data);
        if($res===false){
            $this->error = '删除医生失败';
            $model->rollback();
            return false;
        }
        //删除咨询
        $res = M('DoctorConsult')->where('doctor_id='.$id)->delete();
        if($res===false){
            $this->error = '删除咨询失败';
            $model->rollback();
            return false;
        }
        //删除关注
        $res = M('Attention')->where('doctor_id='.$id)->delete();
        if($res===false){
            $this->error = '删除关注失败';
            $model->rollback();
            return false;
        }
        //删除预约
        $res = M('DoctorAppoint')->where('doctor_id='.$id)->delete();
        if($res===false){
            $this->error = '删除预约失败';
            $model->rollback();
            return false;
        }
        //删除诊疗
        $res = M('DoctorTreatment')->where('doctor_id='.$id)->delete();
        if($res===false){
            $this->error = '删除诊疗失败';
            $model->rollback();
            return false;
        }
        //删除评分
        $res = M('DoctorAssess')->where('doctor_id='.$id)->delete();
        if($res===false){
            $this->error = '删除评价失败';
            $model->rollback();
            return false;
        }
        //删除环信
        $info = getHxUser($id,1);
        D('Huanxin')->deleteUser($info['username']);
        return $model->commit();
    }

    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError(){
        return $this->error;
    }
}