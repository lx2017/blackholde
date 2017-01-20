<?php
/**
 * 诊所负责人管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Model\Clinic;
use Think\Model;
use Admin\Model\Doctor\DoctorModel;

class ClinicDoctorModel extends Model
{
    public $tableName = 'doctor';

    /**
     * 处理excel信息
     * @param $file
     * @return bool
     */
    public function excel($file){
        $excel = new \Admin\Service\ExcelService();
        $res = $excel->read($file);
        if($res){
            $error = $temp = array();
            foreach($res as $item){
                $temp = array(
                    'name'=>$item[0],
                    'clinic_id'=>$item[1],
                    'mobile'=>$item[2],
                );
                //监测电话号码是否存在
                $model = new DoctorModel();
                if($model->checkMobile($temp['mobile'])==false){
                    $temp['note'] = '电话号码已存在';
                    $error[] = $temp;
                    continue;
                }
                //添加到数据库中
                $mytemp = $temp;
                $mytemp['is_manager'] = 1;
                $mytemp['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);
                if(($id=$this->add($mytemp))===false){
                    $temp['note'] = '保存数据失败';
                    $error[] = $temp;
                    continue;
                }else{
                    //创建环信账户
                    $info = getHxUser($id,1);
                    D('Huanxin')->createUser($info['username'],$info['password']);
                }
            }
            if(count($error)>0){
                //写入到错误excel报表中
                $conf = array(
                    'name'=>'负责人名字',
                    'clinic_id'=>'诊所id',
                    'mobile'=>'电话号码',
                    'note'=>'失败原因',
                );
                $file = $excel->push($error,$conf);
                $this->error = '处理表格信息失败';
                return array('code'=>3,'msg'=>'处理表格信息失败','count'=>count($error),'path'=>$file);
            }else{
                return array('code'=>0);
            }
        }else{
            return array('code'=>0);
        }
    }

    /**
     * 检测mobile的唯一性
     * @param $mobile
     * @param $type int 非0为id,0则表示添加
     * @return bool
     */
    public function checkMobile($mobile,$type=0){
        $where = array('mobile'=>$mobile);
        if($type){
            $where['id'] = array('neq',$type);
        }
        $count = $this->where($where)->count();
        return $count?false:true;
    }

}