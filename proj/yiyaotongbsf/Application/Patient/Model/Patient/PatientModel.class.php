<?php
/**
 * 患者端管理
 * Created by PhpStorm.
 * User: jiaolele
 * Date: 2016/11/26 0026
 * Time: 下午 15:31
 */
namespace Patient\Model\Patient;
use Think\Model;
use Think\Page;

class PatientModel extends Model
{

    public $clinic_num = 1000;
    /**
     * 得到评价的列表
     * @param array $where
     * @param int $size
     * @return array
     */
    public function assessList($where=array(),$size=5){
        //得到分页信息
        $count = M('DoctorAssess')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('DoctorAssess')->alias('a')->field("a.*,b.name")->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id')->order('a.date desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }

    /**
     * 得到诊疗分页数据
     * @param array $where
     * @param int $size
     * @return array
     */
    public function treatmentlist($where = array(),$size=10){
        //得到分页信息
        $count = M('DoctorTreatment')->alias('a')->where($where)->join(' JOIN __PATIENT__ b on a.patient_id=b.id')->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('DoctorTreatment')->alias('a')->field("a.*,b.name,b.image as p_image,c.name as doctor,d.clinic_name as clinic")->where($where)->join(' JOIN __PATIENT__ b on a.patient_id=b.id LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id LEFT JOIN __CLINIC__ d on c.clinic_id=d.id')->order('a.treat_time desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 得到疾病
     * @param int $size
     * @param string $name
     * @return array
     */
    public function diseaseList($size=10,$name=''){
        $where = array();
        if($name){
            $where['disease_name'] = array('like',"%{$name}%");
        }
        //得到分页信息
        $count = M('Disease')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Disease')->field('id,disease_name')->where($where)->order(' create_time desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 诊所列表
     * @param int $size
     * @param string $name
     * @return array
     */
    public function nearClinicList($size=10,$name=''){
        list($lng,$lat) = $this->getLngAndLat();
        $res = $this->getAddition($lng,$lat);
        $where = $res['where'];
        if($name){
            $where['clinic_name'] = array('like',"%{$name}%");
        }
        $field = $res['field'];
        //得到分页信息
        $count = M('Clinic')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Clinic')->field('*,'.$field.' as distance')->where($where)->order('distance')->limit($first, $pageTool->listRows)->select();
        if($rows){
            foreach ($rows as &$item){
                $item['distance'] = round($item['distance'],3);
                if($item['clinic_pic']){
                    $item['clinic_pic'] = current(explode(',',$item['clinic_pic']));
                }
            }
            unset($item);
        }
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }
    /**
     * 医生列表
     * @param int $size
     * @param string $name
     * @return array
     */
    public function nearDoctorList($size=10,$name=''){
        $res = $this->getClinic();
        $ids = $res?array_column($res,'id'):'';
        $where = '';
        if($ids){
            $my_clinic = $res?array_column($res,'distance','id'):'';
            $where['a.clinic_id'] = array('in',$ids);
            $ids = implode(',',$ids);
        }
        if($name){
            $where['name'] = array('like',"%{$name}%");
        }
        //得到分页信息
        $count = M('Doctor')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Doctor')->alias('a')->field('a.*,b.clinic_name as clinic,b.clinic_address,b.clinic_score,b.clinic_treatment_volume,b.clinic_pic')->where($where)->join('LEFT JOIN __CLINIC__ b on b.id=a.clinic_id')->order(' field(a.clinic_id,'.$ids.'), a.score desc')->limit($first, $pageTool->listRows)->select();
        if($rows){
            foreach ($rows as &$item){
                $item['distance'] = round($my_clinic[$item['clinic_id']],3);
            }
            unset($item);
        }
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }



    /**
     * 得到咨询的列表
     * @param array $where
     * @param int $size
     * @return array
     */
    public function consultList($where=array(),$size=10){
        //得到分页信息
        $count = M('DoctorConsult')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('DoctorConsult')->alias('a')->field("a.*,b.name")->where($where)->join(' JOIN __DOCTOR__ b on a.doctor_id=b.id')->order('a.date2 desc')->limit($first, $pageTool->listRows)->select();
        //得到环信名
        if($rows){
            foreach($rows as &$item){
                $temp = getHxUser($item['doctor_id'],1);
                $item['hx_name'] = $temp['username'];
            }
            unset($item);
        }
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 得到预约的列表
     * @param array $where
     * @param int $size
     * @return array
     */
    public function appointList($where=array(),$size=10){
        //得到分页信息
        $count = M('DoctorAppoint')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('DoctorAppoint')->alias('a')->field("a.*,b.name")->where($where)->join(' LEFT JOIN __DOCTOR__ b on a.patient_id=b.id')->order('status,a.date desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }

    /**
     * 我关注的诊所
     * @return array
     */
    public function clinicList(){
        //得到所有关注的医生
        $res = M('Attention')->where('patient_id='.UID)->field('doctor_id')->select();
        if($res){
            $ids = array_column($res,'doctor_id');
            $ids = array_unique($ids);
            //得到医生对应的诊所
            $where = array('b.id'=>array('in',$ids));
            $rows = M('Clinic')->distinct(true)->alias('a')->field('a.id,a.clinic_name as clinic,a.clinic_address,a.clinic_score,a.clinic_treatment_volume,a.clinic_pic')->where($where)->join(' LEFT JOIN __DOCTOR__ b on b.clinic_id=a.id')->select();
            if($rows){
                foreach ($rows as &$item){
                    if($item['clinic_pic']){
                        $item['clinic_pic'] = current(explode(',',$item['clinic_pic']));
                    }

                }
                unset($item);
            }
            return $rows;
        }else{
            return array();
        }
    }
    /**
     * 我关注的医生
     * @param array $where
     * @param int $size
     * @return array
     */
    public function doctorsList($where = array(),$size=10){
        //得到分页信息
        $count = M('Attention')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        $rows = M('Attention')->alias('a')->field('a.*,c.name,c.image,c.good,c.score,c.theat_num,c.image,b.clinic_name as clinic')->where($where)->join(' LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id  LEFT JOIN __CLINIC__ b on c.clinic_id=b.id ')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count' => $count);
    }



    /**
     * 我的医生
     * @param int $type 1得到列表,2得到数量
     * @return array
     */
    public function doctorList($type=1){
        $ids = array();
        $ids2 = array();
        //得到预约医生的id
        $apps = M('DoctorAppoint')->distinct(true)->where('patient_id='.UID)->field('doctor_id')->select();
        if($apps){
            $ids = array_column($apps,'doctor_id');
        }
        //得到咨询医生的id
        $cons = M('DoctorConsult')->distinct(true)->where('patient_id='.UID)->field('doctor_id')->select();
        if($cons){
            $ids2 = array_column($cons,'doctor_id');
        }
        //得到医生的ids
        $doctor_ids = $ids+$ids2;
        //得到医生信息
        $list = array();
        $res = array();
        if($doctor_ids){
            $doctor_ids = array_unique($doctor_ids);
            $res = M('Doctor')->alias('a')->field('a.id,a.image,a.name,a.good,a.score,b.clinic_name')->where(array('a.id'=>array('in',$doctor_ids)))->join(' JOIN __CLINIC__ b on a.clinic_id=b.id')->select();
            //得到列表信息
            if($res){
                foreach($res as $item){
                    $temp = array();
                    //预约
                    if($ids){
                        if(array_search($item['id'],$ids)!==false){
                            $temp = $item;
                            $temp['is_a'] = 1;
                        }
                    }
                    //咨询
                    if($ids2){
                        if(array_search($item['id'],$ids2)!==false){
                            if(!$temp) $temp = $item;
                            $temp['is_c'] = 1;
                        }
                    }
                    if($temp) $list[] = $temp;
                }
            }
        }
        if($type!=1){
            return count($res);
        }
        return $list;
    }


    /**
     * 得到活动的列表
     * @param int $size
     * @return array
     */
    public function ActivityList($size=10){
        $res = $this->getClinic();
        $ids = $res?array_column($res,'id'):'';
        $where = '';
        if($ids){
            $my_clinic = $res?array_column($res,'distance','id'):'';
            $where['clinic_id'] = array('in',$ids);
        }
        //得到分页信息
        $count = M('Activity')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Activity')->where($where)->order('create_time desc')->limit($first, $pageTool->listRows)->select();
        if($rows){
            foreach ($rows as &$item){
                $item['distance'] = round($my_clinic[$item['clinic_id']],3);
            }
            unset($item);
        }
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }
    /**
     * 得到资讯的列表
     * @param int $size
     * @return array
     */
    public function InformationList($size=10){
        //得到分页信息
        $count = M('Information')->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Information')->field("id,title,image,content")->order('id desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }

    /**
     * 得到疾病库的列表信息
     * @param $type
     * @return array
     */
    public function getCate($type){
        $all = array();
        //得到一级分类信息
        $res = M('DiseaseCategary')->where('category='.$type)->select();
        if(!$res) return array();
        //得到二级元素
        $field = $type==0?'section_office_id':'crowd_id';
        foreach($res as &$item){
            $where[$field] = $item['id'];
            $temp = M('Disease')->field('id,disease_name')->where($where)->select();
            $item['children'] = $temp;
            if($temp){
                foreach($temp as $v){
                    $all[] = $v;
                }
            }
        }
        return array('list'=>$res,'all'=>$all);
    }

    /**
     * 得到明方验方名的列表信息
     * @param $name
     * @return array
     */
    public function getPreCate($name=''){
        $where = '';
        if($name){
            $where['prescription_name'] = array('like',"%$name%");
        }
        //得到一级分类信息
        $res = M('PrescriptionCategary')->select();
        if(!$res) return array();
        foreach($res as &$item){
            $where['prescription_type_id'] = $item['id'];
            $temp = M('Prescription')->where($where)->field('id,prescription_name')->where($where)->select();
            $item['children'] = $temp;
        }
        return $res;
    }

    /**
     * 得到附近的诊所的条件与距离--200公里内
     * @param $lng int 经度
     * @param $lat int 纬度
     * @return array
     */
    public function getAddition($lng=116,$lat=40){
        //得到患者的经纬度
        $where = array(
            'clinic_lng'=>array('between',array($lng-1,$lng+1)),
            'clinic_lat'=>array('between',array($lat-1,$lat+1)),
        );
        $field = ' ACOS(SIN(('.$lat.' * 3.1415) / 180 ) *SIN((clinic_lat * 3.1415) / 180 ) +COS(('.$lat.' * 3.1415) / 180 ) * COS((clinic_lat * 3.1415) / 180 ) *COS(('.$lng.'* 3.1415) / 180 - (clinic_lng * 3.1415) / 180 ) ) * 6380';
        return array('where'=>$where,'field'=>$field);
    }

    /**
     * 得到附近指定数量的诊所
     * @param int $num 数量, 默认100
     * @param int $type 类型, 默认0--得到id数组,1得到原始信息
     * @return mixed
     */
    public function getClinic($num=0,$type=1){
        if($num==0){
            $num = $this->clinic_num;
        }
        //得到经度纬度
        list($lng,$lat) = $this->getLngAndLat();
        $str = $num.'_'.$lng.'_'.$lat;
        //获取缓存数据
        $clinics = S('my_clinics'.$str);
        if(empty($clinics)){
            $res = $this->getAddition($lng,$lat);
            $where = $res['where'];
            $field = $res['field'];
            $clinics = M('Clinic')->field('*,'.$field.' as distance')->where($where)->order('distance')->limit($num)->select();
            if(!$clinics) $clinics = array();
            S('my_clinics'.$num,$clinics,C('MY_CACHE_TIME'));//缓存结果
        }
        if($type==0){
            $ids = array_column($clinics,'id');
            return $ids;
        }else{
            return $clinics;
        }
    }

    /**
     * 返回当前位置的经纬度
     * @return array
     */
    public function getLngAndLat(){
        //获得经纬度
        $arr = cookie('mylocate');
        if($arr){
            $arr = unserialize($arr);
        }else{
            $arr = array(116,40);
        }
        return $arr;
    }

    /**
     * 返回当前位置的经纬度
     * @param $lng
     * @param $lat
     * @return array
     */
    public function setLngAndLat($lng,$lat){
        if(empty($lng)) $lng = 116;
        if(empty($lat)) $lat = 40;
        //设置cookie
        cookie('mylocate',serialize(array($lng,$lat)));
    }

    /**
     * 处理图片
     * @param $data array 图片数组
     * @param $file string 二级目录
     * @return array|bool false|array
     */
    public function handleImage($data,$file){
        $data = array_filter($data);//去空
        if(empty($data)) return array();
        $uploader = new \Doctor\Controller\Doctor\UploadController();
        $temp = array();
        foreach ($data as $img){
            //判断是否新上传的
            if(stripos($img,$file)===false){
                $rst = $uploader->moveUpload($img,$file);
                if($rst===false){
                    $this->error = '保存图片失败';
                    return false;
                }else{
                    $temp[] = $rst;
                }
            }else{
                $temp[] = $img;
            }
        }
        return $temp;
    }
}