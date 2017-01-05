<?php

namespace Patient\Controller\Patient;

use Patient\Model\Patient\PatientModel;
use Patient\Controller\BaseController;
class PersonController extends BaseController{

    public $model = null;
    public $userinfo = null;
    private $size = 5;

    public function __construct()
    {
        parent::__construct();
        $this->handleLogin(true);//该控制器所以操作均要求登录情况下
        $this->model = new PatientModel();
    }

    /**
     * 我的
     */
    public function mine(){
        //我的医生数量
        $doctor_num = $this->model->doctorList(2);
        //我的关注数量
        $attention_num = M('Attention')->where(array('patient_id'=>UID))->count();
        //我的咨询数量
        $consult_num = M('DoctorConsult')->where(array('patient_id'=>UID,'status'=>1))->count();
        //渲染视图
        $this->assign('doctor_num',$doctor_num);
        $this->assign('attention_num',$attention_num);
        $this->assign('consult_num',$consult_num);
        $this->assign('userinfo',$this->userinfo);
        //设置头部
        $my_header = array(
            'body_style'=>'position:relative;',
            'header'=>false,
            'footer'=>true,
            'footer1'=>'person',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 设置
     */
    public function setup(){
        //设置头部
        $header = array(
            'back'=>U('mine'),
            'header'=>true,
            'header1'=>'设置',
            'header2'=>true
        );
        $this->assign($header);
        //加载视图
        $this->display();
    }

    /**
     * 成为诊所
     */
    public function become_clinic(){
        if(IS_POST){//保存信息
            $data = I('post.');
            $rst = M('ClinicApply')->add($data);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'添加失败'));
            }else{
                $this->ajaxReturn(array('code'=>0));
            }
        }
        //设置头部
        $my_header = array(
            'class'=>'me-bc',
            'header'=>true,
            'header1'=>'成为诊所',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 成为医生
     */
    public function become_doctor(){

        //设置头部
        $my_header = array(
            'class'=>'me-bc',
            'header'=>true,
            'header1'=>'成为医生',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 我的优惠券
     */
    public function my_coupon(){

        //设置头部
        $my_header = array(
            'class'=>'me-bc',
            'header'=>true,
            'header1'=>'我的优惠券',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 我的预约
     */
    public function my_appointment(){
        $where = array('a.patient_id'=>UID);
        //得到列表
        $list = $this->model->appointList($where,$this->size);
        $this->assign('userinfo',$this->userinfo);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的预约',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 我的预约
     */
    public function my_appointment_more(){

        $where = array('a.patient_id'=>UID);
        //得到列表
        $list = $this->model->appointList($where,$this->size);
        $this->assign('userinfo',$this->userinfo);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的预约',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 我的咨询
     */
    public function my_advice(){
        $where = array('a.patient_id'=>UID);
        //得到列表
        $list = $this->model->consultList($where,$this->size);
        $this->assign('size', $this->size);
        $this->assign('list', $list['rows']);
        $this->assign('count',$list['count']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'我的咨询',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 咨询列表--更多
     */
    public function my_advice_more(){
        $where = array('a.patient_id'=>UID);
        //得到列表
        $list = $this->model->consultList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 我的关注诊所
     */
    public function concern_clinic(){
        //得到列表
        $list = $this->model->clinicList();
        $this->assign('list', $list);
        //设置头部
        $my_header = array(
            'style'=>'<body style="background:#fff;">',
            'back'=>U('mine'),
            'header'=>true,
            'header1'=>'我的关注诊所',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 我的关注医生
     */
    public function concern_doctor(){
        if(IS_POST){
            $id = I('id');
            if(!$id) $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            $model = M('Attention');
            //删除
            $res = $model->delete($id);
            if($res===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            }
            $this->ajaxReturn(array('code'=>0));
        }else{
            $where['a.patient_id'] =UID;
            //得到列表
            $list = $this->model->doctorsList($where,$this->size);
            $this->assign('size', $this->size);
            $this->assign('list', $list['rows']);
            $this->assign('count',$list['count']);
            //设置头部
            $my_header = array(
                'style'=>'<body style="background:#fff;">',
                'back'=>U('mine'),
                'header'=>true,
                'header1'=>'我的关注医生',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 我的关注医生_更多
     */
    public function concern_doctor_more(){
        $where['a.patient_id'] =UID;
        $where['a.type']=1;//代表诊所
        //得到列表
        $list = $this->model->doctorsList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 我的医生
     */
    public function my_doctor(){
        if(IS_POST){
            $id = I('id');
            if(!$id) $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            $model = M('DoctorAppoint');
            $model->startTrans();
            //删除预约
            $res = $model->where('doctor_id='.$id)->delete();
            if($res===false){
                $model->rollback();
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            }
            //删除咨询
            $res = M('DoctorConsult')->where('doctor_id='.$id)->delete();
            if($res===false){
                $model->rollback();
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙, 请稍后再试'));
            }
            $model->commit();
            $this->ajaxReturn(array('code'=>0));
        }else{
            //得到列表
            $list = $this->model->doctorList();
            $this->assign('list', $list);
            //设置头部
            $my_header = array(
                'style'=>'background:#fff;',
                'header'=>true,
                'header1'=>'我的医生',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 我的医生--更多
     */
    public function my_doctor_more(){
        $where = array('a.patient_id'=>UID);
        //得到列表
        $list = $this->model->doctorList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 查看医生详情
     * @param $doctor_id
     */
    public function doctor($doctor_id){
        $doctor = M('Doctor')->where(array('id'=>$doctor_id))->find();
        $this->assign('doctor',$doctor);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'医生详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 保存头像
     * @param string $path
     */
    public function set_head($path=''){
        if(!$path) $this->ajaxReturn(array('code'=>1,'msg'=>'文件为空'));
        //处理头像图片
        $rst = $this->model->handleImage(array($path),'patient');
        if($rst===false){
            $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
        }
        $data['image'] = implode(',',$rst);
        //保存头像
        $res = M('Patient')->where('id='.UID)->save($data);
        if($res===false){
            $this->ajaxReturn(array('code'=>1,'msg'=>'保存头像失败'));
        }
        //清空userinfo
        session('userinfo',null);
        $this->ajaxReturn(array('code'=>0));
    }

    /**
     * 个人资料
     * @param $id
     */
    public function person_info($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            //保存信息
            $res = M('Patient')->where('id='.UID)->save($data);
            if($res!==false){
                if($id){
                    $url = U('family_list');
                }else{
                    //清空userinfo
                    session('userinfo',null);
                    $url = U('person_info');
                }
                $this->ajaxReturn(array('code'=>0,'url'=>$url));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            if($id){
                $patient = M('Patient')->find($id);
                $back = U('family_list');
            }else{
                $patient = $this->userinfo;
                $back = U('mine');
            }
            //渲染视图
            $this->assign('userinfo',$patient);
            $this->assign('id',$id);
            //设置头部
            $my_header = array(
                'back'=>$back,
                'header'=>true,
                'header1'=>'个人资料',
                'header3'=>'保存',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之婚孕史
     */
    public function marriage($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            if(empty($id)) $id = UID;
            //保存信息
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['obsterical_history']);
            $marriage=$userinfo[0];
            $bear=$userinfo[1];
            //渲染视图
            $this->assign('marriage',$marriage);
            $this->assign('bear',$bear);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'婚育史',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之手术和外伤
     */
    public function operationand_hurt($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            if(empty($id)) $id = UID;
            //保存信息
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['operation_trauma']);
            $hurt=$userinfo[0];
            $buchong=$userinfo[1];
            //渲染视图
            $this->assign('hurt',$hurt);
            $this->assign('buchong',$buchong);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'手术和外伤',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之家族病史
     */
    public function family_ill($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            if(empty($id)) $id = UID;
            //保存信息
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['family_history']);
            $hurt=$userinfo[0];
            $history=$userinfo[1];
            //渲染视图
            $this->assign('hurt',$hurt);
            $this->assign('history',$history);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'家族病史',
                'header3'=>'保存',
                'class'=>'me-com',
                  'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之药物过敏
     */
    public function allergic_drug($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            if(empty($id)) $id = UID;
            //保存信息
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['drug_allergy']);
            $drug=$userinfo[0];
            $other=$userinfo[1];
            //渲染视图
            $this->assign('drug',$drug);
            $this->assign('other',$other);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'过敏药物',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之食物过敏
     */
    public function foodand_contact($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            //保存信息
            if(empty($id)) $id = UID;
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['contact_allergy']);
            $contact=$userinfo[0];
            $other=$userinfo[1];
            //渲染视图
            $this->assign('contact',$contact);
            $this->assign('other',$other);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'食物和接触过敏',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 个人资料之个人习惯
     */
    public function person_accustomed($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            //保存信息
            if(empty($id)) $id = UID;
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到患者信息
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $userinfo=explode('、',  $user['habit']);
            $habit=$userinfo[0];
            $other=$userinfo[1];
            //渲染视图
            $this->assign('habit',$habit);
            $this->assign('other',$other);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'个人习惯',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }


    /**
     * 个人资料之备注
     */
    public function comment($id=0){
        if(IS_POST){//保存信息
            $data = I('post.');
            //保存信息
            if(empty($id)) $id = UID;
            $res = M('Patient')->where('id='.$id)->save($data);
            if($res!==false){
                //清空userinfo
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //渲染视图
            if($id){
                $user = M('Patient')->find($id);
            }else{
                $user = $this->userinfo;
            }
            $this->assign('remark',$user['remark']);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'备注',
                'header3'=>'保存',
                'class'=>'me-com',
                'header5'=>'add'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 我的家庭档案
     */
    public function family_list(){
        //查询当前用户的家庭所有信息
        $family = M('Patient')->where(array('family_id'=>$this->userinfo['family_id']))->select();
        //渲染视图
        $this->assign('family',$family);
        //设置头部
        $my_header = array(
            'back'=>U('mine'),
            'header'=>true,
            'header1'=>'家庭档案',
            'header3'=>'添加',
            'header5'=>'add',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 添加的家庭档案
     */
    public function add_file(){
        if(IS_POST){//保存信息
            $data = I('post.');
            //查询当前用户的家庭所有信息
            $user= $this->userinfo;
            $data['family_id']=$user['family_id'];
            $mobile= M('Patient')->where(array('mobile'=>$data['mobile']))->find();
            if($mobile){
                $this->ajaxReturn(array('code'=>101,'msg'=>'该手机号已被注册'));
            }else{
                $data['password'] = think_ucenter_md5(C('DEFAULT_PASSWORD'), UC_AUTH_KEY);
                $rst = M('Patient')->add($data);
                if($rst===false){
                    $this->ajaxReturn(array('code'=>1,'msg'=>'添加失败'));
                }else{
                    //添加环信账户
                    $info = getHxUser($rst,3);
                    D('Huanxin')->createUser($info['username'],$info['password']);
                    $this->ajaxReturn(array('code'=>0));
                }
            }
        }
        //设置头部
        $my_header = array(
            'back'=>U('mine'),
            'header'=>true,
            'header1'=>'添加档案',
            'header3'=>'保存',
            'header5'=>'add',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 诊疗记录
     * @param int $id
     */
    public function treatment($id=0){
        //得到患者信息
        $patient = M('Patient')->find($id);
        $this->assign('patient',$patient);
        $this->assign('id',$id);
        //得到诊疗记录
        $where['a.patient_id'] = $id;
        $res = $this->model->treatmentlist($where,$this->size);
        $this->assign('list',$res['rows']);
        $this->assign('count',$res['count']);
        $this->assign('size',$this->size);
        //设置头部
        $my_header = array(
            'back'=>U('family_list'),
            'header'=>true,
            'header1'=>'诊疗记录',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 诊疗记录-更多
     * @param $id
     */
    public function treatment_more($id){
        //得到诊疗记录
        $where['a.patient_id'] = $id;
        $res = $this->model->treatmentlist($where,$this->size);
        $this->assign('list',$res['rows']);
        //加载视图
        $this->display();
    }

    /**
     * 诊疗详情
     */
    public function diagnosis_detail($id,$pid){
        //得到诊疗详情
        $info = M('DoctorTreatment')->alias('a')->field('a.*,b.name as doctor,c.clinic_name as clinic')->where(array('a.id'=>$id))->join(' LEFT JOIN __DOCTOR__ b on a.doctor_id=b.id  LEFT JOIN __CLINIC__ c on b.clinic_id=c.id')->find();
        $this->assign($info);
        //设置头部
        $back = U('treatment',array('id'=>$pid));
        $my_header = array(
            'back'=>$back,
            'header'=>true,
            'header1'=>'诊疗详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 诊疗详情-查看
     */
    public function diagnosis($id,$field){
        $content = M('DoctorTreatment')->field($field)->where(array('id'=>$id))->find();
        //渲染页面
        $this->assign('content',$content[$field]);
        //设置头部
        $my_header = array(
            'class'=>'me-ds-bg',
            'header'=>true,
            'header1'=>'诊疗详情',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 意见反馈
     */
    public function feed_back(){
        if(IS_POST){//保存信息
            $content = I('post.content');
            $contact = $this->userinfo['mobile'];
            if($contact && $content){
                if(M('Feedback')->add(array('content'=>$content,'contact'=>$contact))){
                    $this->ajaxReturn(array('code'=>0));
                }
            }
            $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
        }else{//页面
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'意见反馈',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 修改密码
     */
    public function modify(){
        if(IS_POST){
            //修改密码
            $data = I('post.');
            //验证旧密码
            $where = array('mobile'=>$this->userinfo['mobile']);
            $info = M('Patient')->where($where)->find();
            if(empty($info)){
                $this->ajaxReturn(array('code'=>1,'msg'=>'获取用户信息失败'));
            }
            $password = $info['password'];
            $now = think_ucenter_md5($data['pass'], UC_AUTH_KEY);
            if($password != $now){
                $this->ajaxReturn(array('code'=>2,'msg'=>'密码错误'));
            }
            //修改密码
            $now_pass = think_ucenter_md5($data['newpass'], UC_AUTH_KEY);
            $rst = M('Patient')->save(array('id'=>$info['id'],'password'=>$now_pass));
            if($rst===false){
                $this->ajaxReturn(array('code'=>3,'msg'=>'修改密码失败'));
            }
            $this->ajaxReturn(array('code'=>0));
        }else{
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'修改密码',
                'class'=>'login-bg',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 删除患者
     * @param $id
     */
    public function deletePatient($id){
        if(!$id) $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙,请稍后再试'));
        $rst = D('CommonDoctor')->deletePatient($id);
        if($rst){
            $this->ajaxReturn(array('code'=>0));
        }else{
            $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙,请稍后再试'));
        }
    }

    /**
     * 通知方式
     */
    public function notice(){
        if(IS_POST){
            $value = I('post.value');
            $rst = M('Patient')->where('id='.UID)->save(array('is_allow'=>$value));
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙,请稍后再试'));
            }else{
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }
        }else{
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'新消息通知',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $this->assign('is_allow',$this->userinfo['is_allow']);
            //加载视图
            $this->display();
        }
    }
}