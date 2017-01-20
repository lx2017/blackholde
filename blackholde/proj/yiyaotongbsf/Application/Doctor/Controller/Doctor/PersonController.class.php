<?php
/**
 * 医生个人管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Doctor\Controller\Doctor;

use Doctor\Model\Doctor\DoctorModel;
use Doctor\Controller\BaseController;
class PersonController extends BaseController{

    public $model = null;
    private $size = 5;

    public function __construct()
    {
        parent::__construct();
        $this->handleLogin(true);//该控制器所以操作均要求登录情况下
        $this->model = new DoctorModel();
    }

    /**
     * 个人首页
     */
    public function index(){
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'个人',
            'header2'=>false,
            'footer'=>true,
            'footer1'=>'person',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        $this->assign('login_type',$this->userinfo['login_type']);
        //加载视图
        $this->display();
    }

    /**
     * 个人资料
     */
    public function info(){
        if(IS_POST){//保存信息
            $data = I('post.');
            //处理身份证图片
            $rst = $this->model->handleImage($data['imgs'],'doctor');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['id_image'] = implode(',',$rst);
            unset($data['imgs']);
            //处理头像图片
            $rst = $this->model->handleImage(array($data['image']),'doctor');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['image'] = implode(',',$rst);
            //保存信息
            $res = M('Doctor')->where('id='.UID)->save($data);
            if($res){
                //信息修改,情况userinfo
                session('userinfo', null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{
            //得到医生信息
            $userinfo = M('Doctor')->alias('a')->field('a.*,b.clinic_name as clinic,b.clinic_address as address')->where('a.id='.UID)->join(' __CLINIC__ b on a.clinic_id=b.id')->find();
            //处理身份证图片
            if($userinfo && $userinfo['id_image']){
                $userinfo['imgs'] = explode(',',$userinfo['id_image']);
            }
            //渲染视图
            $this->assign('userinfo',$userinfo);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'个人资料',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 我的主页
     */
    public function main(){
        if($this->userinfo['login_type']==1){//医生
            //得到医生信息
            $doctor = $this->userinfo;
            //执业资质图片集
            if($doctor['licence']){
                $doctor['imgs'] = explode(',',$doctor['licence']);
            }
            $this->assign('doctor',$doctor);
            //得到评价的数据
            $where = array(
                'a.doctor_id'=>UID
            );
            $assess = $this->model->assessList($where,$this->size);
            $this->assign('size', $this->size);
            $this->assign('list', $assess['rows']);
            $this->assign('count',$assess['count']);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'医生主页',
            );
            $display = 'main.doctor';
        }else{//诊所
            //得到诊所信息
            $clinic = $this->userinfo;
            //执业资质图片集
            if($clinic['clinic_licence']) {
                $clinic['imgs'] = explode(',', $clinic['clinic_licence']);
            }
            //得到所有的医生信息
            $doctors = M('Doctor')->field('id,name,good,score')->where(array('clinic_id'=>UID,'status'=>0))->order('score desc')->select();
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
                $assess = $this->model->assessList($where,$this->size);
            }
            $this->assign('size', $this->size);
            $this->assign('list', $assess['rows']);
            $this->assign('count',$assess['count']);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'诊所主页',
            );
            $display = 'main.clinic';
        }
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        $this->assign('userinfo',$this->userinfo);
        //加载视图
        $this->display($display);
    }

    /**
     * 得到诊所的更多评价
     */
    public function main_clinic_more(){
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
        $this->display('main.clinic_more');
    }

    /**
     * 得到医生的更多评价
     */
    public function main_doctor_more(){
        //添加查询条件
        $where['a.doctor_id'] =UID;
        //得到列表
        $list = $this->model->assessList($where,$this->size);
        $this->assign('list', $list['rows']);
        //加载视图
        $this->display('main.doctor_more');
    }

    /**
     * 诊所简介详情
     */
    public function clinic_detail(){
        $this->assign('detail',$this->userinfo['clinic_introduction']);
        $this->assign('clinic_name',$this->userinfo['clinic_name']);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'诊所简介',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display('clinic.detail');
    }

    /**
     * 系统消息
     */
    public function news(){
        //得到系统消息及个人消息
        $where = array('accept_id'=>array('in','0,'.UID));
        $where['accept_id'] = UID;
        $where['type'] = $this->userinfo['login_type'];
        $map['_complex'] = $where;
        $map['_logic'] = 'or';
        $map['accept_id'] = 0;
        $list = M('SystemMessage')->where($map)->order('`date` desc')->select();
        $this->assign('list',$list);
        //设置头部
        $my_header = array(
            'header'=>true,
            'header1'=>'系统消息',
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
    }

    /**
     * 意见反馈
     */
    public function feedback(){
        if(IS_POST){//保存信息
            $content = I('post.content');
            if($this->userinfo['login_type']==1){
                $contact = $this->userinfo['mobile'];
            }else{
                $contact = $this->userinfo['manager_mobile'];
            }
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
    public function change(){
        if(IS_POST){
            //修改密码
            $data = I('post.');
            //验证旧密码
            if($this->userinfo['login_type']==1){
                $where = array('mobile'=>$this->userinfo['mobile'],'status'=>0);
            }else{
                $where = array('mobile'=>$this->userinfo['manager_mobile'],'status'=>0);
            }
            $info = M('Doctor')->field('id,password,mobile')->where($where)->find();
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
            $rst = M('Doctor')->save(array('id'=>$info['id'],'password'=>$now_pass));
            if($rst===false){
                $this->ajaxReturn(array('code'=>3,'msg'=>'修改密码失败'));
            }
            $this->ajaxReturn(array('code'=>0));
        }else{
            //首次登陆
            if($this->userinfo['is_first']==0){
                if($this->userinfo['login_type']==1){
                    $where = array('id'=>$this->userinfo['id']);
                }else{
                    $where = array('id'=>$this->userinfo['did']);
                }
                M('Doctor')->where($where)->setField('is_first',1);
            }
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'修改密码',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display('modify');
        }
    }

    /**
     * 医生首页(头像)
     */
    public function index1(){
        if(IS_POST){
            $data = I('post.');
            $data['id'] = UID;
            $res = M('Doctor')->save($data);
            if($res===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'修改失败'));
            }else{
                //信息修改,情况userinfo
                session('userinfo', null);
                $this->ajaxReturn(array('code'=>0));
            }
        }else{
            //得到医生信息
            $userinfo = M('Doctor')->alias('a')->field('a.*,b.clinic_name as clinic,b.clinic_address as address')->where('a.id='.UID)->join(' __CLINIC__ b on a.clinic_id=b.id')->find();
            //渲染视图
            $this->assign('userinfo',$userinfo);
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
    }

    /**
     * 执业资质
     */
    public function permit(){
        if(IS_POST){//修改信息
            $data = I('post.');
            //处理图片
            $rst = $this->model->handleImage($data['imgs'],'doctor');
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存图片失败'));
            }
            $data['licence'] = implode(',',$rst);
            unset($data['imgs']);
            //保存信息
            $res = M('Doctor')->where('id='.UID)->save($data);
            if($res){
                //信息修改,情况userinfo
                session('userinfo', null);
                $this->ajaxReturn(array('code'=>0));
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'保存失败'));
            }
        }else{//获得信息
            //得到资质信息
            $res = M('Doctor')->field('permit,licence')->find(UID);
            $data = array(
                'permit'=>'',
                'imgs'=>array(),
            );
            if($res){
                $data['permit'] = $res['permit'];
                $imgs = explode(',',$res['licence']);
                $data['imgs'] = array_filter($imgs);//去空
            }
            $this->assign($data);
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'执业资质',
                'header3'=>'保存',
                'header4'=>'javascript:;',
                'header5'=>'sava',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }

    /**
     * 通知方式
     */
    public function message(){
        $id = $this->userinfo['login_type']==1?UID:$this->userinfo['did'];
        if(IS_POST){
            $value = I('post.value');
            $rst = M('Doctor')->where('id='.$id)->save(array('is_allow'=>$value));
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>'网络繁忙,请稍后再试'));
            }else{
                session('userinfo',null);
                $this->ajaxReturn(array('code'=>0));
            }
        }else{
            //得到当前状态
            $value = M('Doctor')->field('is_allow')->find($id);
            $value2 =0;
            if($value) $value2 = $value['is_allow'];
            //设置头部
            $my_header = array(
                'header'=>true,
                'header1'=>'新消息通知',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $this->assign('is_allow',$value2);
            //加载视图
            $this->display();
        }
    }
}