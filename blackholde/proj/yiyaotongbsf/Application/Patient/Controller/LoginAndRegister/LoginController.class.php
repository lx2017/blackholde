<?php
namespace Patient\Controller\LoginAndRegister;
use Patient\Controller\BaseController;
use Patient\Model\LoginAndRegister\LoginModel;
use Patient\Model\LoginAndRegister\CookieModel;
use Patient\Model\LoginAndRegister\RegisterModel;
use Sms\Controller;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;
use Think\Verify;
use Patient\Library\Weibo\Api\SaeTOAuthV2;

/**
 * 该类主要是实现用户登录
 **/
class LoginController extends BaseController {
    /*用户登录*/
    public function login(){
        if(IS_POST){
            $mobile=I('post.mobile');
            $dao =new LoginModel();
            $user=$dao->FindByMobile($mobile);
            if($user){
                //检验登录密码是否正确
                $password = think_ucenter_md5(I('post.password'), UC_AUTH_KEY);
                $password1 = $user['password'];
                if ($password1 == $password) {
                    $cookie_unit =new CookieModel();
                    if($user){
                        $cookie_unit->creatnewid($user['id']);//给用户id添加随机变量写入缓存，
                    }
                    session('user_id', $user['id']);
                    session('userinfo', serialize($user));
                    if(!empty(I('post.remember'))){     //如果用户选择了，记录登录状态就把用户名和加了密的密码放到cookie里面
                        cookie("mobile", $mobile, time()+3600*24*365);
                        cookie("password", I('post.password'), time()+3600*24*365);
                    }
                    $myurl = cookie('url');
                    cookie('url',null);
                    if ($myurl) {
                        $this->ajaxReturn(array('code'=>"0",'url'=>$myurl));
                    }else{
                        $this->ajaxReturn(array('code'=>"0",'url'=>U('/Patient/Patient/Patient/index')));
                    }
                }else{
                    $this->ajaxReturn(array('code'=>1,'msg'=>'密码不正确'));
                }
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'该手机号未注册，请注册后重新登录'));
            }
        }else{
            $my_header = array(
                'body_style'=>'background:#fff;',
                'class'=>'login-lg'
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $cookie_user= cookie('mobile');
            $cookie_password = cookie('password');
            if($cookie_user&&$cookie_password)
            {
                $this->assign('mobile',$cookie_user);
                $this->assign('password',$cookie_password);
            }
            //加载视图
            $this->display();
        }
    }

    /*找回密码*/
    public function back_password(){
        if(IS_POST){
            //验证手机验证码是否正确
            $mobile = I('post.mobile');
            $smsApi = new SmsApi();
            $result = $smsApi->checkCode($mobile, I('post.code'));
            $result =json_decode($result,true);
            if ($result && $result['code']==1) {
                $password =I('post.password');
                $password = think_ucenter_md5($password, UC_AUTH_KEY);
                $dao = new LoginModel();
                $data['password'] =$password;
                $result = $dao->where("mobile =$mobile")->save($data);
                if($result !== false){
                    $this->ajaxReturn(array('code'=>0,'url'=>U('/Patient/LoginAndRegister/Login/login')));
                }else{
                    $this->ajaxReturn(array('code' => 1,' msg'=>'密码修改失败，请稍候再试！'));
                }
            }else{
                $this->ajaxReturn(array('code' => 1, 'msg' => '验证码错误,请重新输入！'));
                }
        }else{
            $my_header = array(
                'header'=>true,
                'header1'=>'忘记密码',
                'body_style'=>'login-lg',
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            //加载视图
            $this->display();
        }
    }
    /**
     * 注销
     */
    public function logout() {
        cookie('user_id',null);
        cookie('userinfo',null);
        session(null);
        session('[destroy]');
        $this->redirect('/Patient/LoginAndRegister/Login/login');
    }

    /**
     * 获取当前用户名和密码
     */

    public function getPatientInfo(){
        $userinfo=M('Patient')->where(array('id'=>UID))->find();
        if($userinfo){
            $this->ajaxReturn(array('result'=>"0",'HXName'=>$userinfo['mobile'],'HXPassword'=>$userinfo['mobile']));
        }else{
            $this->ajaxReturn(array('result'=>"1"));
        }
    }


    /**
     * 获取患者极光ID
     */
    public function patientRegisterID(){
        //接受客户端传来的json数据
        $json_string = I('get.');
        if($json_string){
            $info=M('PatientPush')->where(array('patient_id'=>$json_string['UID']))->find();

            if($info){
                $mydata = array(
                    'patient_id'=>$json_string['UID'],
                    'register_id'=>$json_string['RegID']
                );
                $result=M('PatientPush')->where(array('patient_id'=>$json_string['UID']))->save($mydata);

                if($result===false){

                    $this->ajaxReturn(array('result'=>"1"));
                }else{
                    $this->ajaxReturn(array('result'=>"0"));
                }
            }else{
                $mydata2 = array(
                    'patient_id'=>$json_string['UID'],
                    'register_id'=>$json_string['RegID']
                            );

                $result2=M('PatientPush')->add($mydata2);
                if($result2===false){
                    $this->ajaxReturn(array('result'=>"1"));
                }else{
                    $this->ajaxReturn(array('result'=>"0"));
                }
            }
        }
        else{
            $this->ajaxReturn(array('result'=>"1"));
        }
    }



    /*检测用户名密码合法性*/
    public function CheckUser(){
        $password =I('post.password');
        $data['password'] =think_ucenter_md5($password, UC_AUTH_KEY);
        $data['mobile'] =I('post.number');
        $Dao = new LoginModel();
        $result = $Dao->FindByTiaoJian($data);
        if ($result) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(1006, '发送失败');
        }

    }

    /*密码检测用户是否存在*/
    public function CheckMobile(){
        $f=I('post.f');
        $mobile =I('post.mobile');
        $Dao =new LoginModel();
        $list = $Dao->FindByMobile($mobile);
        if ($list) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(1007, '发送失败');
        }
    }
    /*发送手机验证码*/
    public function SendYzm()
    {
        $mobile = I('post.mobile');
        $smsApi = new SmsApi();
        $code = rand(1000, 9999);
        $expire = 10;
        $params = array('to' => $mobile, 'datas' => array($code, $expire), 'tempId' => SMSCODETEMPLATE);
        $result = $smsApi->sendTemplateSMS($params, $code, $this->userId);
        if ($result) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(1005, '发送失败');
        }
    }
    /*手机验证码*/
    public function CheckYzm(){
        $x=I('post.x');
        $f=I('post.f');
        $code = I('post.code');
        $smsApi = new SmsApi();
        $result = $smsApi->checkCode(I('mobile'), I('code'));
        if ($result) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(2002, '发送失败');
        }
    }

    /*检测pc端验证码*/
    public function CheckpcYzm(){
        $yzm = I('post.yzm');
        $boot = check_verify($yzm,'');
        if($boot){
            responseJson(1002, '成功');
        }else{
            responseJson(2002, '验证码不正确');
        }

    }


}
