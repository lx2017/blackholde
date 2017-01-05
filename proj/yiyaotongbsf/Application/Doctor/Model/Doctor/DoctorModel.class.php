<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Model\Doctor;
use Think\Model;
use Think\Page;

class DoctorModel extends Model
{

    /**
     * 得到诊疗分页数据(患者去重)
     * @param array $where
     * @param int $size
     * @return array
     */
    public function patientList($where = array(),$size=10){
        //得到分页信息
        $count = M('DoctorTreatment')->alias('a')->where($where)->join(' JOIN __PATIENT__ b on a.patient_id=b.id')->count('distinct(a.patient_id)');//去重统计
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
        $rows = $this->table($query.' a')->field("a.*,b.name,b.image as p_image")->group('a.patient_id')->where($where)->join(' JOIN __PATIENT__ b on a.patient_id=b.id')->order('CONVERT(b.name USING gbk) asc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 得到诊疗分页数据(患者不去重)
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
        $rows = M('DoctorTreatment')->alias('a')->field("a.*,b.name,b.image as p_image,c.name as doctor")->where($where)->join(' JOIN __PATIENT__ b on a.patient_id=b.id LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id')->order('a.treat_time desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows, 'count'=>$count);
    }

    /**
     * 添加患者
     * @param $requestData
     * @return bool|int false或id
     */
    public function addPatient($requestData){
        //验证该手机号是否存在
        $res = M('Patient')->field('id')->where('mobile='.$requestData['mobile'])->find();
        if($res){
            //更新患者信息
            M('patient')->where('mobile='.$requestData['mobile'])->save($requestData);
            //验证是否有这个诊疗记录
            $count = M('DoctorTreatment')->where(array('doctor_id'=>UID,'patient_id'=>$res['id']))->count();
            if($count){
                $this->error = '该账号已存在';
                return false;
            }
            $id = $res['id'];//直接返回患者id--用于添加诊疗记录
        }else{
            //保存账户信息
            $requestData['family_id'] = get_unique_number();
            $requestData['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);
            $id = M('patient')->add($requestData);
            if($id===false){
                $this->error = '创建账户失败';
                return false;
            }
            //添加环信
            $info = getHxUser($id,3);
            D('Huanxin')->createUser($info['username'],$info['password']);
        }
        //返回结果
        return $id;
    }

    /**
     * 添加诊疗记录
     * @param $requestData
     * @return bool
     */
    public function addTreatment($requestData){
        $requestData['doctor_id'] = UID;
        $rst =  M('DoctorTreatment')->add($requestData);
        if($rst===false){
            $this->error = '添加诊疗失败';
            return false;
        }
        return true;
    }

    /**
     * 得到患者的诊疗列表
     * @param $where
     * @param $size
     * @return array
     */
    public function patientTreatmentList($where,$size=10){
        //得到分页信息
        $count = M('DoctorTreatment')->alias('a')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        $rows = M('DoctorTreatment')->alias('a')->field('a.*,b.clinic_name as clinic,c.name as doctor')->order('a.treat_time desc')->where($where)->join(' LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id LEFT JOIN __CLINIC__ b on c.clinic_id=b.id')->limit($first, $pageTool->listRows)->select();
        //处理结果
        foreach($rows as &$item){
            $temp = explode('-',$item['treat_time']);
            if($temp){
                $item['month'] = $temp[1].'/'.$temp[2];
                $item['year'] = $temp[0];
            }else{
                $item['month'] = 0;
                $item['year'] = 0;
            }
        }
        unset($item);
        //返回结果
        return array('rows' => $rows,'count' => $count);
    }

    /**
     * 得到评价的列表
     * @param array $where
     * @param int $size
     * @return array
     */
    public function assessList($where=array(),$size=10){
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
        $rows = M('DoctorAssess')->alias('a')->field("a.*,b.name,b.image as p_image,c.name as doctor")->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id')->order('a.date desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }

    /**
     * 得到评价分析数据
     * @param array $where
     * @param int $type 1-总数,2-筛选
     * @param string $date 时间-年月
     * @return array
     */
    public function assessData($where=array(),$type=1,$date=''){
        $total = array(0,0,0,0,0,0);
        //得到数据
        if($type==1){
            $rst = M('DoctorAssess')->field('score')->where($where)->select();
        }else{
            $date = $date?:date('Y-m');//默认为当前月
            //解析where
            if(is_array($where['doctor_id'])){
                $tt = implode(',',$where['doctor_id'][1]);
                $mywhere = " `doctor_id` in ({$tt})";
            }else{
                $mywhere = " `doctor_id`={$where['doctor_id']}";
            }
            $sql = "select `score` from __PREFIX__doctor_assess where {$mywhere} and DATE_FORMAT(`date`, '%Y-%m')='{$date}'";
            $rst = M('DoctorAssess')->query($sql);
        }
        //解析数据
        if($rst){
            $total[0] = count($rst);
            foreach ($rst as $item){
                switch (intval($item['score'])){
                    case 1:
                        ++$total[1];break;
                    case 2:
                        ++$total[2];break;
                    case 3:
                        ++$total[3];break;
                    case 4:
                        ++$total[4];break;
                    case 5:
                        ++$total[5];break;
                }
            }
        }
        return $total;
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
        $rows = M('DoctorConsult')->alias('a')->field("a.*,b.name,b.image as p_image")->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id')->order('status,a.date desc')->limit($first, $pageTool->listRows)->select();
        //得到环信名
        if($rows){
            foreach($rows as &$item){
                $temp = getHxUser($item['patient_id'],3);
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
        $rows = M('DoctorAppoint')->alias('a')->field("a.*,b.name,b.image as p_image")->where($where)->join(' LEFT JOIN __PATIENT__ b on a.patient_id=b.id')->order('status,a.time desc')->limit($first, $pageTool->listRows)->select();
        //得到环信名
        if($rows){
            foreach($rows as &$item){
                $temp = getHxUser($item['patient_id'],3);
                $item['hx_name'] = $temp['username'];
            }
            unset($item);
        }
        //返回结果
        return array('rows' => $rows,'count'=>$count);
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

    /**
     * 得到疾病库的列表信息
     * @param $type
     * @return array
     */
    public function getCate($type){
        //得到一级分类信息
        $res = M('DiseaseCategary')->where('category='.$type)->select();
        if(!$res) return array();
        //得到二级元素
        $field = $type==0?'section_office_id':'crowd_id';
        foreach($res as &$item){
            $where[$field] = $item['id'];
            $temp = M('Disease')->field('id,disease_name')->where($where)->select();
            $item['children'] = $temp;
        }
        return $res;
    }

    /**
     * 得到名方的列表
     * @param array $where
     * @param int $size
     * @return array
     */
    public function recipeList($where=array(),$size=10){
        //得到分页信息
        $count = M('Prescription')->where($where)->count();
        $pageTool = new Page($count, $size);
        //得到表信息
        $first = $pageTool->firstRow;
        if ($first >= $count && $count != 0) {
            //超界则总是在最后一页,并且记录不能为0
            $first = $count - $size;
        }
        //查询数据
        $rows = M('Prescription')->where($where)->order('create_time desc')->limit($first, $pageTool->listRows)->select();
        //返回结果
        return array('rows' => $rows,'count'=>$count);
    }
}