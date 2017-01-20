<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Controller\Doctor;

use Doctor\Model\Doctor\DoctorModel;
use Doctor\Controller\BaseController;
class DoctorController extends BaseController{

    public $model = null;
    private $size = 10;
    public $userinfo = null;

    public function __construct()
    {
        parent::__construct();
        $this->handleLogin(true);//该控制器所以操作均要求登录情况下
        $this->model = new DoctorModel();
    }

    /**
     * 医生首页
     */
    public function index(){
        //得到预约我的数量
        $appoint_num = M('DoctorAppoint')->where(array('doctor_id'=>UID,'status'=>1))->count();
        //得到咨询我的数量
        $consult_num = M('DoctorConsult')->where(array('doctor_id'=>UID,'status'=>1))->count();
        //渲染视图
        $this->assign('appoint_num',$appoint_num);
        $this->assign('consult_num',$consult_num);
        if($this->userinfo['login_type']==2){
            $info = M('Doctor')->field('image,name')->find(UID);
        }else{
            $info = $this->userinfo;
        }
        $this->assign('userinfo',$info);
        //设置头部
        $my_header = array(
            'back'=>0,
            'body_style'=>'padding-top:0;',
            'header'=>false,
            'footer'=>true,
            'footer1'=>'doctor',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /*-----------------我要看病------------------*/

    /**
     * 我要看病--患者列表(已诊疗)
     */
    public function patientlist(){
        //添加查询条件
        $mobile = I('get.mobile');
        if($mobile!==''){
            $where['b.mobile'] = $mobile;
        }
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->patientList($where,$this->size);
        $this->assign('mobile', $mobile);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的患者',
            'header3'=>'添加患者',
            'header4'=>U('/Doctor/Doctor/Doctor/addpatient'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('looking.patientlist');
    }

    /**
     * 我要看病--患者列表(已诊疗)--更多
     */
    public function patientlist_more(){
        //添加查询条件
        $name = I('get.name');
        if($name!==''){
            $where['b.name'] = array('like','%'.$name.'%');
        }
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->patientList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('looking.patientlist_more');
    }

    /**
     * 添加患者
     */
    public function addpatient(){
        if(IS_POST){
            $params = I('post.');
            $rst = $this->model->addPatient($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$this->model->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('addtreatment',array('id'=>$rst))));//跳转到添加诊疗记录
            }
        }else{
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'添加患者',
                'header3'=>'',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('looking.addpatient');
        }
    }

    /**
     * 查看患者
     * @param $id
     */
    public function viewpatient($id){
        //得到该患者最近的一次的诊疗情况
        $info = M('DoctorTreatment')->where('patient_id='.$id)->order('date desc')->find();
        $this->assign('treatment',$info);
        //得到患者名
        $patient = M('Patient')->where(array('id'=>$id))->getField('name');
        $this->assign('patient',$patient);
        //得到环信名
        $res = getHxUser($id,3);
        $this->assign('hx_name',$res['username']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'已诊疗患者',
            'header3'=>'新病情',
            'header4'=>U('addtreatment',array('id'=>$id)),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('looking.viewpatient');
    }

    /**
     * 添加诊疗记录
     * @param $id int
     */
    public function addtreatment($id=0){
        if(IS_POST){
            $params = I('post.');
            if(empty($params['treat_time'])){ //默认时间为当前时间
                $params['treat_time'] = date('Y-m-d');
            }
            $rst = M('DoctorTreatment')->add($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'添加诊疗信息失败'));
            }else{
                if($this->userinfo['login_type']==2){
                    $myid = $this->userinfo['id'];
                }else{
                    $myid = $this->userinfo['clinic_id'];
                }
                //更新医生表
                D('CommonDoctor')->putToCount(1,UID,0,$myid);
                $this->ajaxReturn(array('code'=>0,'url'=>U('viewpatient',array('id'=>$params['patient_id']))));
            }
        }else{
            //得到患者名
            $patient = M('Patient')->where(array('id'=>$id))->getField('name');
            $this->assign('patient',$patient);
            $this->assign('userinfo',$this->userinfo);
            //得到环信名
            $tt = getHxUser($id,3);
            $this->assign('hx_name',$tt['username']);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'添加诊疗',
                'header3'=>'',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('looking.addtreatment');
        }
    }

    /**
     * 查看诊疗详情
     * @param $id int 诊疗id
     */
    public function viewtreatment($id){
        //查询详情
        $treatment = M('DoctorTreatment')->alias('a')->field('a.*,b.clinic_name as clinic,c.name as doctor')->where(array('a.id='.$id))->join(' LEFT JOIN __DOCTOR__ c on a.doctor_id=c.id LEFT JOIN __CLINIC__ b on c.clinic_id=b.id')->find();
        $this->assign($treatment);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'诊疗详情',
            'header3'=>'',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.viewtreatment');
    }

    /**
     * 诊疗详情的详情--字多的
     * @param $id
     * @param $type
     */
    public function todetail($id,$type){
        //标题
        $name = '病情症状';
        $field = 'symptom';
        switch(intval($type)){
            case 1:
                $name = '病情症状';
                $field = 'symptom';
                break;
            case 2:
                $name = '医嘱';
                $field = 'symptom';
                break;
            case 3:
                $name = '医生诊断';
                $field = 'treatment';
                break;
            case 4:
                $name = '用药及处方';
                $field = 'recipe';
                break;
        }
        //得到内容
        $id = intval($id);
        $content = M('DoctorTreatment')->where(array('id'=>$id))->getField($field);
        //渲染页面
        $this->assign('name',$name);
        $this->assign('content',$content);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'诊疗详情',
            'header3'=>'',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.todetail');
    }

    /*-----------------我的患者------------------*/

    /**
     * 我的患者
     */
    public function patients(){
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->patientList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的患者',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.patients');
    }

    /**
     * 我的患者--更多
     */
    public function patients_more(){
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->patientList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('patient.patients_more');
    }

    /**
     * 查看患者详情
     * @param $id
     */
    public function patient($id){
        $patient = M('Patient')->where(array('id'=>$id))->find();
        $this->assign('patient',$patient);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'患者详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.patient');
    }

    /**
     * 诊疗详情的详情--字多的
     * @param $id
     * @param $type
     */
    public function todetail_p($id,$type){
        //标题
        $name = '';
        //得到内容
        $id = intval($id);
        $content = M('Patient')->where(array('id'=>$id))->getField($type);
        //渲染页面
        $this->assign('name',$name);
        $this->assign('content',$content);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'查看详情',
            'header3'=>'',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.todetail_p');
    }

    /**
     * 患者诊疗记录
     * @param $id int
     */
    public function patient_treatmentlist($id){
        $where['a.patient_id'] = $id;
        //得到列表
        $list = $this->model->patientTreatmentList($where,$this->size);
        //处理信息
        $this->assign('size', $this->size);
        $this->assign('count', $list['count']);
        $this->assign('list', $list['rows']);
        //得到患者信息
        $patient = M('Patient')->field('name,age,sex,habit')->where(array('id'=>$id))->find();
        $this->assign('patient',$patient);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'患者诊疗记录',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.treatmentlist');
    }

    /**
     * 患者诊疗记录--更多
     * @param $id int
     */
    public function patient_treatmentlist_more($id){
        $where['a.patient_id'] = $id;
        //得到列表
        $list = $this->model->patientTreatmentList($where,$this->size);
        //处理信息
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('patient.treatmentlist_more');
    }

    /*-----------------我的诊疗记录------------------*/

    /**
     * 我的诊疗
     */
    public function treatmentlist(){
        //添加查询条件
        $name = I('get.name');
        if($name!==''){
            $where['b.name'] = array('like','%'.$name.'%');
        }
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->treatmentlist($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('count', $list['count']);
        $this->assign('list', $list['rows']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'诊疗记录',
            'header3'=>'诊疗数据',
            'header4'=>U('treatmentcount'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        $this->assign('name',$name);
        //加载视图
        $this->display('treatment.list');
    }

    /**
     * 我的诊疗--更多
     */
    public function treatmentlist_more(){
        //添加查询条件
        $name = I('get.name');
        if($name!==''){
            $where['b.name'] = array('like','%'.$name.'%');
        }
        $where['a.doctor_id'] = UID;
        //得到列表
        $list = $this->model->treatmentlist($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('treatment.list_more');
    }

    /**
     * 诊疗数据
     */
    public function treatmentcount(){
        //查询总量
        $where['doctor_id'] = UID;
        $total = M('DoctorTreatment')->where($where)->count();
        //添加查询条件
        $date = I('get.date');
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_treatment where `doctor_id`=".UID." and DATE_FORMAT(`treat_time`, '%Y-%m')='{$date}'";
        $rst = M('DoctorTreatment')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->assign('date',$date);
        $this->assign('total',$total);
        $this->assign('count',$count);
        //设置头部
        $my_header = array(
            'h_title'=>'诊疗数据',
            'header'=>true,
            'header1'=>'诊疗数据',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('treatment.count');
    }

    /**
     * ajax获得月份数量
     * @param $date
     */
    public function getTreatmentCount($date){
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_treatment where `doctor_id`=".UID." and DATE_FORMAT(`treat_time`, '%Y-%m')='{$date}'";
        $rst = M('DoctorTreatment')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->ajaxReturn(array('code'=>0,'data'=>$count));
    }

    /*-----------------患者评价------------------*/

    /**
     * 患者评价
     */
    public function assesslist(){
        $where = array('a.doctor_id'=>UID);
        //筛选条件
        $score = I('get.score',0);
        if(!empty($score)) $where['a.score'] = $score;
        //得到列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        $this->assign('score',$score);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'患者评价',
            'header3'=>'评价数据',
            'header4'=>U('assesscount'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('assess.list');
    }

    /**
     * 患者评价--更多
     */
    public function assesslist_more(){
        $where = array('a.doctor_id'=>UID);
        //筛选条件
        $score = I('get.score',0);
        if(!empty($score)) $where['a.score'] = $score;
        //得到列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('assess.list_more');
    }

    /**
     * 评价数据
     */
    public function assesscount(){
        //查询总量
        $where['doctor_id'] = UID;
        //得到总数
        $total = $this->model->assessData($where);
        //得到每月的分析数据
        $date = I('get.date');
        $date = $date?:date('Y-m');//默认为当前月
        $count = $this->model->assessData($where,2,$date);
        $this->assign('total',$total);
        $this->assign('count',$count);
        $this->assign('date',$date);
        //设置头部
        $my_header = array(
            'h_title'=>'诊疗数据',
            'header'=>true,
            'header1'=>'诊疗数据',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('assess.count');
    }

    /**
     * ajax获得月份数量
     * @param $date
     */
    public function getAssessCount($date){
        //查询总量
        $where['doctor_id'] = UID;
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $count = $this->model->assessData($where,2,$date);
        $this->ajaxReturn(array('code'=>1,'data'=>$count));
    }

    /*-----------------咨询我的------------------*/

    /**
     * 咨询列表
     */
    public function consultlist(){
        $where = array('a.doctor_id'=>UID);
        //得到列表
        $list = $this->model->consultList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的咨询',
            'header3'=>'咨询数据',
            'header4'=>U('consultcount'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('consult.list');
    }

    /**
     * 咨询列表--更多
     */
    public function consultlist_more(){
        $where = array('a.doctor_id'=>UID);
        //得到列表
        $list = $this->model->consultList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('consult.list_more');
    }

    /**
     * 咨询统计
     */
    public function consultcount(){
        //查询总量
        $where['doctor_id'] = UID;
        $total = M('DoctorConsult')->where($where)->count();
        //添加查询条件
        $date = I('get.date');
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_consult where `doctor_id`=".UID." and DATE_FORMAT(`date`, '%Y-%m')='{$date}'";
        $rst = M('DoctorConsult')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->assign('date',$date);
        $this->assign('total',$total);
        $this->assign('count',$count);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'咨询数据',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('consult.count');
    }

    /**
     * ajax获得月份数量
     * @param $date
     */
    public function getConsultCount($date){
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_consult where `doctor_id`=".UID." and DATE_FORMAT(`date`, '%Y-%m')='{$date}'";
        $rst = M('DoctorConsult')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->ajaxReturn(array('code'=>0,'data'=>$count));
    }

    /*-----------------预约我的------------------*/

    /**
     * 预约列表
     */
    public function appointlist(){
        $where = array('a.doctor_id'=>UID);
        //得到列表
        $list = $this->model->appointList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置医生名
        if($this->userinfo['login_type']==2){
            $name = $this->userinfo['manager_name'];
        }else{
            $name = $this->userinfo['name'];
        }
        $this->assign('doctor',$name);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的预约',
            'header3'=>'预约数据',
            'header4'=>U('appointcount'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('appoint.list');
    }

    /**
     * 预约列表--更多
     */
    public function appointlist_more(){
        $where = array('a.doctor_id'=>UID);
        //得到列表
        $list = $this->model->appointList($where,$this->size);
        $this->assign('list', $list['rows']);
        //设置医生名
        if($this->userinfo['login_type']==2){
            $name = $this->userinfo['manager_name'];
        }else{
            $name = $this->userinfo['name'];
        }
        $this->assign('doctor',$name);
        //加载视图
        $this->display('appoint.list_more');
    }

    /**
     * 预约统计
     */
    public function appointcount(){
        //查询总量
        $where['doctor_id'] = UID;
        $total = M('DoctorAppoint')->where($where)->count();
        //添加查询条件
        $date = I('get.date');
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_appoint where `doctor_id`=".UID." and DATE_FORMAT(`time`, '%Y-%m')='{$date}'";
        $rst = M('DoctorAppoint')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->assign('date',$date);
        $this->assign('total',$total);
        $this->assign('count',$count);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'预约数据',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('appoint.count');
    }

    /**
     * ajax获得月份数量
     * @param $date
     */
    public function getAppointCount($date){
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_appoint where `doctor_id`=".UID." and DATE_FORMAT(`time`, '%Y-%m')='{$date}'";
        $rst = M('DoctorAppoint')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->ajaxReturn(array('code'=>0,'data'=>$count));
    }

    /**
     * 疾病库
     * @param int $type
     */
    public function disease($type=1){
        if($type==1){ //科室分类
            $category = 0;
        }else{ //分群分类
            $category = 1;
        }
        //得到相关信息
        $list = $this->model->getCate($category);
        //设置头部
        $my_header = array(
            'back'=>U('index'),
            'header'=>false,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //分配其他信息
        $this->assign('list',$list);
        $this->assign('type',$type);
        //加载视图
        $this->display('doc.cate');
    }

    /**
     * 得到疾病的详情
     * @param $id
     */
    public function diseasedetail($id){
        //得到疾病具体信息
        $info = M('Disease')->find($id);
        $title = $info['disease_name']?$info['disease_name']:'疾病详情';
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>$title,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //分配其他信息
        $this->assign('info',$info);
        //加载视图
        $this->display('doc.catedetail');
    }

    /**
     * 名方验方
     */
    public function recipe(){
        //获得信息
        $name = I('get.name');
        $where = array();
        if($name){ //搜索时才获取数据
            $where['prescription_name'] = array('like',"%{$name}%");
        }
        //查询数据
        $list = $this->model->recipeList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'名方验方',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //分配其他信息
        $this->assign('name',$name);
        //加载视图
        $this->display('doc.recipe');
    }

    /**
     * 名方验方-更多
     */
    public function recipe_more(){
        //获得信息
        $name = I('get.name');
        $where = array();
        if($name){ //搜索时才获取数据
            $where['prescription_name'] = array('like',"%{$name}%");
        }
        //查询数据
        $list = $this->model->recipeList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('doc.recipe_more');
    }

    /**
     * 名方验方详情
     * @param $id
     */
    public function recipedetail($id){
        //得到疾病具体信息
        $info = M('Prescription')->find($id);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'名方验方详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //分配其他信息
        $this->assign('info',$info);
        //加载视图
        $this->display('doc.recipedetail');
    }

}