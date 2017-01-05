<?php
/**
 * 患者端
 * Created by PhpStorm.
 * User: jiaolele
 * Date: 2016/11/26 0026
 * Time: 下午 15:31
 */
namespace Patient\Controller\Patient;

use Patient\Model\Patient\PatientModel;
use Patient\Controller\BaseController;
class PatientController extends BaseController{

    public $model = null;
    private $size = 10;
    private $size2 = 80;

    public function __construct()
    {
        parent::__construct();

        $this->model = new PatientModel();
    }


    /**
     * 患者首页
     */
    public function index(){
        $res = $this->model->getClinic();
        $ids = $res?array_column($res,'id'):'';
        $where = '';
        if($ids){
            $my_clinic = $res?array_column($res,'distance','id'):'';
            $where['clinic_id'] = array('in',$ids);
        }
        //得到附近的活动
        $activity=M('Activity')->where($where)->order('create_time desc')->limit(3)->select();
        if($activity){
            foreach ($activity as &$item){
                $item['distance'] = round($my_clinic[$item['clinic_id']],3);
            }
            unset($item);
        }
        //得到常见疾病
        $disease=M('Disease')->field('id,disease_name')->order('id desc')->limit(3)->select();
        //健康资讯
        $information=M('Information')->field('id,title,image,content')->order('id desc')->limit(3)->select();
        $this->assign('activity',$activity);
        $this->assign('disease',$disease);
        $this->assign('information',$information);
        $this->assign ('uid',UID);
        //设置头部
        $my_header = array(
            'back'=>0,
            'header'=>false,
            'footer'=>true,
            'footer1'=>'patient',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();

    }


    /*-----------------首页搜索------------------*/
    public function search($name=''){
        //得到诊所信息
        $clinic = $this->model->nearClinicList(3,$name);
        $clinic = $clinic['rows'];
        //得到疾病信息
        $disease = $this->model->diseaseList(6,$name);
        $disease = $disease['rows'];
        //得到医生信息
        $doctor = $this->model->nearDoctorList(3,$name);
        $doctor = $doctor['rows'];
        //渲染页面
        $this->assign('name',$name);
        if($disease||$doctor||$clinic){
            $this->assign('disease',$disease);
            $this->assign('doctor',$doctor);
            $this->assign('clinic',$clinic);
            //设置头部
            $my_header = array(
                'body_style'=>'background:#fff;',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }else{
            //设置头部
            $my_header = array(
                'body_style'=>'background:#fff;',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('no_consolut');
        }
    }

    /**
     * 更多诊所
     * @param $name
     */
    public function more_clinic($name=''){
        $list = $this->model->nearClinicList($this->size,$name);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        $this->assign('name',$name);
        //设置头部
        $my_header = array(
            'header'=>false,
            'body_style'=>'background:#fff;',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 更多诊所-更多
     * @param $name
     */
    public function more_clinic_more($name=''){
        $list = $this->model->nearClinicList($this->size,$name);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 更多医生
     * @param $name
     */
    public function more_doctor($name=''){
        $list = $this->model->nearDoctorList($this->size,$name);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        $this->assign('name',$name);
        //设置头部
        $my_header = array(
            'header'=>false,
            'body_style'=>'background:#fff;',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 更多医生-更多
     * @param $name
     */
    public function more_doctor_more($name=''){
        $list = $this->model->nearDoctorList($this->size,$name);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 更多疾病
     * @param $name
     */
    public function more_disease($name=''){
        $list = $this->model->diseaseList($this->size2,$name);
        $this->assign('size', $this->size2);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        $this->assign('name',$name);
        //设置头部
        $my_header = array(
            'header'=>false,
            'body_style'=>'background:#fff;',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 更多疾病-更多
     * @param $name
     */
    public function more_disease_more($name=''){
        $list = $this->model->diseaseList($this->size2,$name);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /*-----------------附近诊所------------------*/
    public function near_clinic(){
        //得到列表
        $list = $this->model->nearClinicList($this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'body_style'=>'background:#fff;',
            'header1'=>'附近诊所',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }
    /*-----------------附近诊所--分页----------------*/
    public function near_clinic_more(){
        //得到列表
        $list = $this->model->nearClinicList($this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }
    /*-----------------诊所主页----------------*/
    public function clinic_detail($id){
        //得到诊所具体信息
        $info = M('Clinic')->find($id);
        //执业资质图片集
        if($info['clinic_licence']) {
            $info['imgs'] = explode(',', $info['clinic_licence']);
        }
        //根据诊所id查询出诊所对应的医生
        $doctor=M('Doctor')->where(array('clinic_id'=>$id,'status'=>0))->select();
        $var = array();
        foreach ($doctor as $key=>$value){
            $var[]= $value['id'];
        }
        unset($value);
        //诊所评价
        $doctor_id= implode(',', $var) ;
        $where['doctor_id']=array('in',$doctor_id);
        $list = $this->model->assessList($where,$this->size);
        //查询当前患者是否关注
        $where1= array();
        $where1['patient_id'] = UID;
        $where1['doctor_id']=$id;
        $attention=M('Attention')->where($where1)->find();
        //分配其他信息
        $this->assign('doctor',$doctor);
        $this->assign('info',$info);
        $this->assign('ids',$doctor_id);
        $this->assign('attention',$attention);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'诊所主页',
            'header8'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 得到诊所的更多评价
     */
    public function clinic_detail_more(){
        //添加查询条件
        $ids = I('get.ids');
        $where = array();
        if($ids){
            $where['a.doctor_id'] =array('in',$ids);
        }
        //得到列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /*-----------------诊所简介------------------*/
    public function dignoise_brief($id){
        //得到疾病具体信息
        $info = M('Clinic')->find($id);
        //设置头部
        $my_header = array(
            'header'=>true,
            'body_style'=>'background:#fff;',
            'header1'=>'诊所简介',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign('info',$info);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /*-----------------医生关注------------------*/
    public function attention(){
        $this->handleLogin(true);//登录
        if(IS_POST){
            if(I('post.id')){ //取消关注
                $res = M('Attention')->where(array('id'=>I('post.id'),'patient_id'=>UID))->delete();
                if($res!==false){
                    $this->ajaxReturn(array('code'=>0));
                }else{
                    $this->ajaxReturn(array('code'=>1));
                }
            }else{ //关注
                $data = array(
                    'doctor_id'=>I('post.doctor_id'),
                    'patient_id'=>UID,
                );
                $res = M('Attention')->add($data);
                if($res!==false){
                    $this->ajaxReturn(array('code'=>0));
                }else{
                    $this->ajaxReturn(array('code'=>1));
                }
            }
        }
    }

    /*-----------------附近医生------------------*/
    public function near_doctor(){
        //得到列表
        $list = $this->model->nearDoctorList($this->size);
        $list = $this->model->nearDoctorList($this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'body_style'=>'background:#fff;',
            'header1'=>'附近医生',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }
    /*-----------------附近医生--分页----------------*/
    public function near_doctor_more(){
        //得到列表
        $list = $this->model->nearDoctorList($this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }
    /*-----------------医生--主页----------------*/
    public function doctor_main($id){
        $this->handleLogin(true);
        $distance = I('get.distance');
        //根据医生id查询医生所有信息
        $doctor = M('Doctor')->find($id);
        //执业资质图片集
        if($doctor['licence']) {
            $doctor['imgs'] = explode(',', $doctor['licence']);
        }
        //根据医生ID查询出诊所信息
        $clinic=M('Clinic')->where(array('id'=>$doctor['clinic_id']))->find();
        $where['doctor_id']=$id;
        if(defined('UID')){
            //查询当前患者是否关注
            $where1= array();
            $where1['patient_id'] = UID;
            $where1['doctor_id']=$id;
            $where1['type']=0;
            $attention=M('Attention')->where($where1)->find();
            $this->assign('attention', $attention);
        }
        //得到评价列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        $this->assign('doctor', $doctor);
        //环信用户
        $hx_name = getHxUser($id,1);
        $this->assign('hx_name', $hx_name['username']);
        $this->assign('clinic', $clinic);
        $this->assign('distance', $distance);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'医生主页',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 医生评论-更多
     */
    public function doctor_main_more($id){
        $where['a.doctor_id'] = $id;
        //得到列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /*-----------------预约------------------*/
    public function brief($id){
        $this->handleLogin(true);//验证登录
        if(IS_POST){
            //根据id查询医生的帐号
            $doctor=M('Doctor')->where(array('id'=>$id))->find();
            //根据医生的用户名查询出该医生的register_id
            $info=M('DoctorPush')->where(array('mobile'=>$doctor['mobile']))->find();
             $register_id=$info['register_id'];
            //获取预约的时间
            $time=$_POST['time'];
            $message='预约时间为'+$time;
            //向医生端推送信息
            $data=sendNotifySpecial($register_id,$message);
            if($data){
                //向数据库表中插入数据
                $mydata = array(
                    'doctor_id' =>$id,
                    'patient_id'=>UID,
                    'time' =>$time ,
                    'date' => NOW_TIME,
                );
                $re=M('DoctorAppoint')->add($mydata);
                if($re===false){
                    $this->ajaxReturn(array('code'=>1,'msg'=>'预约不成功'));
                }else{
                    $this->ajaxReturn(array('code'=>0));
                }
            }
        }
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'预约',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    public  function  getDoctorConsult(){
            $id=$_POST['id'];
            $rst = getHxUser($id,1);
        if($rst){
            $this->ajaxReturn(array('result'=>0,'HXName'=>$rst['username']));

        }else{
            $this->ajaxReturn(array('result'=>1));
        }

    }




    /*-----------------名方验方------------------*/
    public function prescription(){
        $name = I('get.name');
        //得到相关信息
        $list = $this->model->getPreCate($name);
        //分配其他信息
        $this->assign('list',$list);

        //设置头部
        $my_header = array(
            'back'=>U('index'),
            'header'=>true,
            'header1'=>'名方验方',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        $this->assign('name',$name);
        //分配其他信息
        $this->assign('list',$list);
        //加载视图
        $this->display();

    }
    /**
     * 名方验方详情
     * @param $id
     */
    public function receip_detail($id){
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
        $this->display();
    }


    /*-----------------疾病库------------------*/
    public function disease_database($type=1){
        if($type==1){ //科室分类
            $category = 0;
        }else{ //分群分类
            $category = 1;
        }
        //得到相关信息
        $list = $this->model->getCate($category);
        //分配其他信息
        $this->assign('list',$list['list']);
        $this->assign('all',$list['all']);
        $this->assign('type',$type);
        //设置头部
        $my_header = array(
            'back'=>U('index'),
            'h_title'=>'疾病库',
            'header1'=>'疾病库',
            'header'=>true,
            'header2'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }
    /**
     * 得到疾病的详情
     * @param $id
     */
    public function diseasedetail($id){
        //得到疾病具体信息
        $info = M('Disease')->find($id);
        //设置头部
        $name = $info['disease_name']?:'疾病详情';
        $my_header = array(
            'header'=>true,
            'body_style'=>'background:#fff;',
            'header1'=>$name,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //分配其他信息
        $this->assign('info',$info);
        //加载视图
        $this->display();
    }



    /*-----------------更多活动------------------*/
    public function more_activity(){
        //得到列表
        $list = $this->model->ActivityList($this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'body_style'=>'background:#fff;',
            'h_title'=>'更多活动',
            'header1'=>'更多活动',
            'header'=>true,
            'header2'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }
    /*-----------------更多活动--分页----------------*/
    public function more_activity_more(){
        //得到列表
        $list = $this->model->ActivityList($this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }
    /*-----------------活动详情----------------*/
    public function activity_detail($id){
        if(IS_POST){
            $this->handleLogin(true);//验证登录
            $mydata = array(
                'activity_id'=>I('post.id'),
                'patient_id'=>UID,
                'create_time'=>NOW_TIME,
                'patient_name'=>$this->userinfo['name'],
                'patient_mobile'=>$this->userinfo['mobile']
            );
            $result=M('ActivityPatient')->add($mydata);
            if ($result) {
                $res['code']=0;
            } else {
                $res['code'] = 1;
            }
            $this->ajaxReturn($res);

        }
        $distance = I('get.distance',0);
        $model= M('Activity');
        $content =$model->alias('a')->field('a.*,b.clinic_name as clinic,b.clinic_specialty as specialty,b.clinic_address as address,b.clinic_score,b.clinic_pic')->where(array('a.id='.$id)) ->join(' LEFT JOIN __CLINIC__ b on a.clinic_id=b.id')->find();
        if($content && $content['clinic_pic']){
            $content['clinic_pic'] = current(explode(',',$content['clinic_pic']));
        }
        //根据活动ID查询当前用户是否参加该活动
        $where['activity_id']=$id;
        $where['patient_id']=UID;
        $status=M('ActivityPatient')->where($where)->find();
        //渲染页面
        $this->assign('status',$status);
        $this->assign('distance',$distance);
        $this->assign($content);
        //设置头部
        if(strpos($_SERVER['HTTP_REFERER'],'login')===false){
            $back = -1;
        }else{
            $back = U('index');
        }
        $my_header = array(
            'back'=>$back,
            'body_style'=>'background:#fff;',
            'h_title'=>'活动详情',
            'header1'=>'活动详情',
            'header'=>true,
            'header2'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /*-----------------更多资讯------------------*/
    public function more_info(){
        //得到列表
        $list = $this->model->InformationList($this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'body_style'=>'background:#fff;',
            'h_title'=>'更多资讯',
            'header1'=>'更多资讯',
            'header'=>true,
            'header2'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /*-----------------更多资讯--分页----------------*/
    public function more_info_more(){
        //得到列表
        $list = $this->model->InformationList($this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /*-----------------资讯详情----------------*/
    public function info_detail($id){
        $model= M('Information');
        $content =$model->field('id,title,image,date,content') ->where(array('id='.$id))->find();
        //渲染页面
        $this->assign($content);
        //设置头部
        $my_header = array(
            'body_style'=>'background:#fff;',
            'h_title'=>'资讯详情',
            'header1'=>'资讯详情',
            'header'=>true,
            'header2'=>true,
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /*-----------------消息----------------*/
    public function message(){
        $this->handleLogin(true);//验证登录
        //得到消息
        $where = array('a.patient_id'=>UID);
        $messages = $this->model->consultList($where,$this->size);
        $this->assign('list',$messages['rows']);
        $this->assign('count',$messages['count']);
        $this->assign('size',$this->size);
        //设置头部
        $my_header = array(
            'back'=>U('index'),
            'body_style'=>'background:#fff;',
            'header1'=>'消息',
            'header'=>true,
            'footer'=>true,
            'footer1'=>'message',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 消息-更多
     */
    public function message_more(){
        //得到消息
        $where = array('a.patient_id'=>UID);
        $messages = $this->model->consultList($where,$this->size);
        $this->assign('list',$messages['rows']);
        $this->display();
    }

    /**
     * 评价
     * @param $id
     * @param $did
     * @param $pid
     * @param $type int 默认修改咨询, 2时修改诊疗状态
     */
    public function assess($id=0,$did=0,$pid=0,$type=1){
        $this->handleLogin(true);//验证登录
        if(IS_POST){
            $model = M('DoctorAssess');
            $model->startTrans();
            //添加评价
            $data = I('post.');
            $url = html_entity_decode($data['url']);
            unset($data['url']);
            if($type==1) $pid = UID;
            $data['patient_id'] = $pid;
            $data['doctor_id'] = $did;
            if($id==0) $data['type']=2;//诊疗评价
            $res = $model->add($data);
            if($res===false){
                $model->rollback();
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            }
            //修改其他状态
            $d = array(
                'id'=>$id,
                'status'=>3,
            );
            if($type==1){
                $mymodel = M('DoctorConsult');
            }else{
                $mymodel = M('DoctorTreatment');
            }
            $res = $mymodel->save($d);
            if($res===false){
                $model->rollback();
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            }
            $model->commit();
            //更新状态
            D('CommonDoctor')->putToCount(4,$did,$data['score']);//推到医生表中
            $this->ajaxReturn(array('code'=>0,'url'=>$url));
        }else{
            //设置头部
            $my_header = array(
                'body_style'=>'background:#fff;',
                'header1'=>'医生评价',
                'header'=>true,
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $this->assign('myurl',$_SERVER['HTTP_REFERER']);
            //加载视图
            $this->display();
        }
    }

    /**
     * 设置登录前的url
     * @param string $url
     */
    public function setCookie($url=''){
        if($url){
            cookie('url',htmlspecialchars_decode($url));
        }
    }
}