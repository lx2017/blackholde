<?php
namespace Doctor\Controller\LoginAndRegister;
use Doctor\Model\LoginAndRegister\LoginModel;
use Doctor\Model\LoginAndRegister\RegisterModel;
use Doctor\Controller\BaseController;
use Sms\Controller;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;



/**
* 该类主要是实现用户手机注册，并将用户信息存入数据库中
 **/
class RegisterController extends BaseController {
    /*注册入口函数*/
    public function Index()
    {
        $t=I('t');
        if($t=='wap'){
            $this->display('Login/register_mobile');
        }else{
            $this->display('Login/register');
        }

    }
    /*用户注册发送验证码并检测用户是否存在*/
    public function SendYzmst(){
        $mobile = I('post.mobile');
        $daos = new RegisterModel();
        $list =$daos->FindByMobile($mobile);
        if(count($list) > 0){
            responseJson(1007, '用户已存在');
        } else{
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
        $smsApi = new SmsApi();
        $result = $smsApi->checkCode(I('mobile'), I('code'));
        if ($result) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(1005, '发送失败');
        }
    }
    /*手机验证码*/
    public function CheckYzmwap(){
        $x=$_REQUEST['post.x'];
        $f=$_REQUEST['post.f'];
        $smsApi = new SmsApi();
        $result = $smsApi->checkCode($_REQUEST['mobile'], $_REQUEST['code']);
        if ($result) {
            responseJson(1002, '发送成功');
        } else {
            responseJson(1005, '发送失败');
        }
    }
    /*用户注册*/
    /**
     * @param $x
     * @param $y
     */
    /*手机用户注册入库*/
    public function InsertsInfo(){
        if(IS_POST){
            $mobile =I('post.mobile');
            $password =I('post.password');
            $password = think_ucenter_md5($password, UC_AUTH_KEY);
            $data['mobile'] = $mobile;
            $data['password'] =$password;
            $data['register_time'] =date('Y-m-d H:i:s');
            $Dao = new RegisterModel();
            $result = $Dao->insertdata($data);
            if($result){
                $userinfo =$Dao->FindinfoByMobile($mobile);
                $this->assign('userinfo',$userinfo);
                $this->display('UserCenter/myself_mobile');
                //$this->display('Login/login_mobile');
            }else{
                $this->error('注册失败'); //查询失败后返回上一页
            }
        }

    }
    /*pc 用户注册*/
    public function InsertsRegisterInfo(){
        $mobile =I('post.number');
        $password =I('post.userpassword');
        $password = think_ucenter_md5($password, UC_AUTH_KEY);
        $data['mobile'] = $mobile;
        $data['password'] =$password;
        $data['register_time'] =date('Y-m-d H:i:s');
        $Dao = new RegisterModel();
        $result = $Dao->insertdata($data);
        if($result){
            $userinfo =$Dao->FindinfoByMobile($mobile);
            $this->assign('userinfo',$userinfo);
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/myself');/*pc*/
          //  $this->display('Login/login');
        }else{
            $this->error('注册失败'); //查询失败后返回上一页
        }
    }
    public function CheckMobile(){
        $f=I('post.f');
        $mobile =I('post.mobile');
        $dao = new RegisterModel();
        $list =$dao->FindByMobile($mobile);
        if ($list) {
            responseJson(1007, '用户已经存在');
        } else {
            responseJson(1006, '用户可以用');
        }

    }
    /*填写验证码入口文件*/
    public function YanZheng(){
        $mobile= I('mobile');
        $this->assign('mobile',$mobile);
        $this->display('Login/register_mobile2');
    }
    /*手机用户密码页*/
    public function RePassword(){
        $mobile =I('mobile');
        $this->assign('mobile',$mobile);
        $this->display('Login/register_mobile3');
    }

}
