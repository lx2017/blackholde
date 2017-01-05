<?php
/**
 * 诊所管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Controller\Clinic;

use Doctor\Model\Clinic\ClinicModel;
use Doctor\Model\Doctor\DoctorModel;
use Doctor\Controller\BaseController;
class ClinicController extends BaseController{

    public $model = null;
    public $model2 = null;
    private $size = 10;
    public $userinfo = null;

    public function __construct()
    {
        parent::__construct();
        $this->handleLogin(true);//该控制器所以操作均要求登录情况下
        $this->model = new ClinicModel();
        $this->model2 = new DoctorModel();
    }

    /**
     * 诊所首页
     */
    public function index(){
        if($this->userinfo['login_type']==1){//非管理员
            $clinic_id = $this->userinfo['clinic_id'];
            //得到诊所信息
            $clinic = M('Clinic')->find($clinic_id);
            //诊所头像
            $clinic['image'] = array_shift(explode(',',$clinic['clinic_pic']));
            //执业资质图片集
            $clinic['imgs'] = explode(',',$clinic['clinic_licence']);
            //得到所有的医生信息
            $doctors = M('Doctor')->field('id,name,good,score')->where(array('clinic_id'=>$clinic_id,'status'=>0))->order('score desc')->select();
            $this->assign('doctors', $doctors);
            $doctor_ids = array_column($doctors,'id');
            $this->assign('ids', implode(',',$doctor_ids));
            //得到诊断量
            $clinic['treat_num'] = 0;
            if($doctors){
                $clinic['treat_num'] = M('DoctorTreatment')->where(array('doctor_id'=>array('in',$doctor_ids)))->count();
            }
            $this->assign('clinic', $clinic);
            //得到诊所的评论信息
            $assess = array('count'=>0,'rows'=>array());
            if($doctors){
                $where = array(
                    'a.doctor_id'=>array('in',$doctor_ids)
                );
                $assess = $this->model2->assessList($where,$this->size);
            }
            $this->assign('size', $this->size);
            $this->assign('list', $assess['rows']);
            $this->assign('count',$assess['count']);
            //设置头部
            $my_header = array(
                'back'=>0,
                'header'=>true,
                'header1'=>'诊所',
                'header2'=>false,
                'footer'=>true,
                'footer1'=>'clinic',
            );
            $display = 'no_clinic';
        }else{//管理员
            $this->assign('userinfo', $this->userinfo);
            //设置头部
            $my_header = array(
                'back'=>0,
                'header'=>true,
                'header1'=>$this->userinfo['clinic_name'],
                'header2'=>false,
                'footer'=>true,
                'footer1'=>'clinic',
            );
            $display = 'clinic';
        }
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display($display);
    }

    /**
     * 得到诊所的更多评价
     */
    public function no_clinic_more(){
        //添加查询条件
        $ids = I('get.ids');
        $where = array();
        if($ids){
            $where['a.doctor_id'] =array('in',$ids);
        }
        //得到列表
        $list = $this->model2->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('no_clinic_more');
    }

    /**
     * 医生管理
     */
    public function doctor(){
        //得到医生信息
        $doctors = M('Doctor')->field('id,name,good,mobile,score')->where(array('clinic_id'=>UID,'status'=>0))->order('CONVERT(name USING gbk) asc')->select();//按姓名排序
        $this->assign('doctors', $doctors);
        //渲染页面
        $this->assign('doctors',$doctors);
        $this->assign('clinic_name',$this->userinfo['clinic_name']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'医生管理',
            'header3'=>'添加医生',
            'header4'=>U('add_doctor'),
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('doctor.index');
    }

    /**
     * 添加医生
     */
    public function add_doctor(){
        if(IS_POST){
            $requestData = I('post.');
            $res = $this->model->add_doctor($requestData);
            if($res){
                $this->ajaxReturn(array('code'=>0,'url'=>U('doctor')));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>$this->model->getError()));
            }
        }else{
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'添加医生',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('doctor.add');
        }
    }

    /**
     * 医生详情
     * @param $id int 医生id
     */
    public function view_doctor($id){
        //得到医生信息
        $doctor = M('Doctor')->find($id);
        //身份证图
        if(empty($doctor['id_image'])==false){
            $doctor['imgs'] = explode(',',$doctor['id_image']);
        }
        $this->assign('doctor',$doctor);
        $this->assign('userinfo',$this->userinfo);
        //得到医生评价数据
        $where = array(
            'a.doctor_id'=>$id
        );
        $assess = $this->model2->assessList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $assess['rows']);
        $this->assign('count',$assess['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'医生主页',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('doctor.view');
    }

    /**
     * 得到医生的更多评价
     * @param $id int 医生id
     */
    public function view_doctor_more($id){
        //添加查询条件
        $where['a.doctor_id'] =$id;
        //得到列表
        $list = $this->model2->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('doctor.view_more');
    }

    /**
     * 删除医生
     * @param $id
     */
    public function delete_doctor($id){
        $res = $this->model->delete_doctor($id);
        if($res){
            $this->ajaxReturn(array('code'=>0,'url'=>U('doctor')));
        }else{
            $this->ajaxReturn(array('code'=>1,'msg'=>$this->model->getError()));
        }
    }

    /**
     * 患者管理
     */
    public function patient(){
        //得到患者列表
        //得到诊所的医生id
        $ids = $this->getDoctorIds();
        $where = array(
            'a.doctor_id'=>array('in',$ids)
        );
        $res = $this->model->patientList($where);
        $this->assign('size', $this->size);
        $this->assign('list', $res['rows']);
        $this->assign('count',$res['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的患者',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.index');
    }

    /**
     * 患者管理-更多
     */
    public function patient_more(){
        //得到患者列表
        //得到诊所的医生id
        $ids = $this->getDoctorIds();
        $where = array(
            'a.doctor_id'=>array('in',$ids)
        );
        $res = $this->model->patientList($where);
        $this->assign('list', $res['rows']);
        $this->display('patient.index_more');
    }

    /**
     * 患者详情
     * @param $id
     */
    public function view_patient($id){
        //得到患者信息
        $patient = M('Patient')->find($id);
        $this->assign('patient',$patient);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'患者详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('patient.view');
    }

    /**
     * 患者详情--字多的
     * @param $id
     * @param $type
     */
    public function todetail($id,$type){
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
        $this->display('patient.todetail');
    }

    /**
     * 资料管理
     */
    public function info(){
        if(IS_POST){
            //保存信息
            $data = I('post.');
            //处理诊所图片
            $rst = $this->model2->handleImage($data['imgs'],'clinic');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['clinic_pic'] = implode(',',$rst);
            unset($data['imgs']);
            //处理资质图片
            $rst = $this->model2->handleImage($data['licences'],'clinic');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['clinic_licence'] = implode(',',$rst);
            unset($data['licences']);
            //保存信息
            $res = M('Clinic')->where('id='.UID)->save($data);
            if($res!==false){
                //信息修改,情况userinfo
                session('userinfo', null);
                $this->ajaxReturn(array('code'=>0,'url'=>U('info')));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //设置诊所信息
            $userinfo = $this->userinfo;
            if($userinfo['clinic_pic']){
                $userinfo['imgs'] = explode(',',$userinfo['clinic_pic']);
            }
            if($userinfo['clinic_licence']){
                $userinfo['licence'] = explode(',',$userinfo['clinic_licence']);
            }
            $this->assign('userinfo',$userinfo);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'诊所详情',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('info.index');
        }
    }

    /**
     * 诊所负责人资料
     */
    public function manager(){
        if(IS_POST){
            //保存负责人信息
            $data = I('post.');
            //处理身份证图片
            $rst = $this->model2->handleImage($data['imgs'],'dcotor');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['id_image'] = implode(',',$rst);
            unset($data['imgs']);
            //保存信息
            $res = M('Doctor')->where('id='.$this->userinfo['did'])->save($data);
            if($res!==false){
                //信息修改,情况userinfo
                session('userinfo', null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            $info = M('Doctor')->find($this->userinfo['did']);
            //处理身份证图片
            if(empty($info['id_image'])==false){
                $info['imgs'] = explode(',',$info['id_image']);
            }
            $this->assign('info',$info);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'诊所负责人资料',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('info.doctor');
        }
    }

    /**
     * 患者评价
     */
    public function assess(){
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        $where = array('a.doctor_id'=>array('in',$doctor_ids));
        //筛选分数
        $score = I('get.score',0);
        if(!empty($score)) $where['a.score'] = $score;
        //得到列表
        $list = $this->model2->assessList($where,$this->size);
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
     * 患者评价-更多
     */
    public function assess_more(){
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        $where = array('a.doctor_id'=>array('in',$doctor_ids));
        //筛选分数
        $score = I('get.score',0);
        if(!empty($score)) $where['a.score'] = $score;
        //得到列表
        $list = $this->model2->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('assess.list_more');
    }

    /**
     * 评价数据
     */
    public function assesscount(){
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        $where = array('doctor_id'=>array('in',$doctor_ids));
        //得到总数
        $total = $this->model2->assessData($where);
        //得到每月的分析数据
        $date = I('get.date');
        $date = $date?:date('Y-m');//默认为当前月
        $count = $this->model2->assessData($where,2,$date);
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
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        //筛选条件
        $where = array('doctor_id'=>array('in',$doctor_ids));
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $count = $this->model2->assessData($where,2,$date);
        $this->ajaxReturn(array('code'=>1,'data'=>$count));
    }

    /**
     * 诊疗记录
     */
    public function treatment(){
        //添加查询条件
        $name = I('get.name');
        if($name!==''){
            $where['b.name'] = array('like','%'.$name.'%');
        }
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        //筛选条件
        $where['a.doctor_id'] = array('in',$doctor_ids);
        //得到列表
        $list = $this->model2->treatmentlist($where,$this->size);
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
        //加载视图
        $this->display('treatment.list');
    }

    /**
     * 诊疗记录-更多
     */
    public function treatment_more(){
        //添加查询条件
        $name = I('get.name');
        if($name!==''){
            $where['b.name'] = array('like','%'.$name.'%');
        }
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        //筛选条件
        $where['a.doctor_id'] = array('in',$doctor_ids);
        //得到列表
        $list = $this->model2->treatmentlist($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('treatment.list_more');
    }

    /**
     * 诊疗数据
     */
    public function treatmentcount(){
        //查询总量
        //得到所有的医生id
        $doctor_ids = $this->getDoctorIds();
        //筛选条件
        $where['doctor_id'] = array('in',$doctor_ids);
        $total = M('DoctorTreatment')->where($where)->count();
        //添加查询条件
        $date = I('get.date');
        $ids = implode(',',$doctor_ids);
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_treatment where `doctor_id` in (".$ids.") and DATE_FORMAT(`treat_time`, '%Y-%m')='{$date}'";
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
        $doctor_ids = $this->getDoctorIds();
        $ids = implode(',',$doctor_ids);
        //查询数量
        $date = $date?:date('Y-m');//默认为当前月
        $sql = "select count(*) as count from __PREFIX__doctor_treatment where `doctor_id` in (".$ids.") and DATE_FORMAT(`treat_time`, '%Y-%m')='{$date}'";
        $rst = M('DoctorTreatment')->query($sql);
        $count = 0;
        if($rst){
            $count = $rst[0]['count'];
        }
        $this->ajaxReturn(array('code'=>0,'data'=>$count));
    }

    /**
     * 得到诊所的医生id数组
     * @return array
     */
    private function getDoctorIds(){
        $doctor_ids = S('my_doctor_ids');
        if(empty($doctor_ids)){
            $doctors = M('Doctor')->field('id')->where(array('clinic_id'=>UID,'status'=>0))->select();
            if($doctors){
                $doctor_ids = array_column($doctors,'id');
            }else{
                $doctor_ids = array(0);
            }
            S('my_doctor_ids',$doctor_ids,C('MY_CACHE_TIME'));//缓存结果
        }
        return $doctor_ids;
    }

}