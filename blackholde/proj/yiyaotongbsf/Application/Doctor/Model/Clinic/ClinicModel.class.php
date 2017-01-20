<?php
/**
 * 诊所管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Model\Clinic;
use Think\Model;
use Think\Page;

class ClinicModel extends Model
{

    /**
     * 得到诊疗分页数据(患者去重)
     * @param array $where
     * @param int $size
     * @return array
     */
    public function patientList($where = array(),$size=10){
        //得到分页信息
        $count = M('DoctorTreatment')->alias('a')->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id')->count('distinct(a.patient_id)');//去重统计
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据(同时去重患者id)--分组内排序
        //得到子查询对象--先排序
        $query = M('DoctorTreatment')->order('treat_time desc')->select(false);
        //执行分组
        $rows = $this->table($query.' a')->field("a.*,b.name,b.image as p_image,c.name as doctor")->group('a.patient_id')->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id')->order('treat_time desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 添加医生
     * @param $requestData
     * @return bool
     */
    public function add_doctor($requestData){
        //验证数据完整性
        if(empty($requestData['mobile'])){
            $this->error = '请输入手机号';
            return false;
        }
        if(empty($requestData['name'])){
            $this->error = '请输入医生名';
            return false;
        }
        //验证手机号码
        $count = M('Doctor')->where(array('mobile'=>$requestData['mobile'],'status'=>array('neq',2)))->count();
        if($count){
            $this->error = '手机号已经存在';
            return false;
        }
        //添加医生
        $d = array(
            'clinic_id'=>UID,
            'name'=>$requestData['name'],
            'mobile'=>$requestData['mobile'],
            'password'=>think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY),
        );
        $res = M('Doctor')->add($d);
        if($res===false){
            $this->error = '添加医生失败';
            return false;
        }
        //添加环信
        $info = getHxUser($res,1);
        D('Huanxin')->createUser($info['username'],$info['password']);
        return $res;
    }

    /**
     * 删除医生
     * @param $id
     * @return bool
     */
    public function delete_doctor($id){
        if(empty($id)){
            return false;
        }
        return D('CommonDoctor')->deleteDoctor($id);
    }

}