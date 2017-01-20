<?php
namespace Patient\Controller\LoginAndRegister;
use Patient\Model\LoginAndRegister\LoginModel;
use Patient\Model\LoginAndRegister\RegisterModel;
use Patient\Controller\BaseController;
use Sms\Controller;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;



/**
* 该类主要是实现用户手机注册，并将用户信息存入数据库中
 **/
class RegisterController extends BaseController {
    /*用户注册入口方法*/
    public function  register(){
        if(IS_POST) {
            //验证手机验证码是否正确
            $mobile = I('post.mobile');
            $smsApi = new SmsApi();
            $result= $smsApi->checkCode($mobile, I('post.code'));
            $result=json_decode($result,true);
            if ($result&&$result['code']==1) {
                //创建患者信息
                $mydata = array(
                    'name'=>$mobile,
                    'mobile'=>$mobile,
                    'password' => think_ucenter_md5(I('post.password'), UC_AUTH_KEY),
                    'family_id'=>get_unique_number()
                );
                $rst1= M('Patient')->add($mydata);
                if($rst1===false){
                    $this->ajaxReturn(array('code'=>1,'msg'=>'注册失败'));
                }else{
                    //添加环信账户
                    $info = getHxUser($rst1,3);
                    D('Huanxin')->createUser($info['username'],$info['password']);
                    $this->ajaxReturn(array('code'=>0,'url'=>U('/Patient/LoginAndRegister/Login/login')));
                }
            } else {
                $this->ajaxReturn(array('code' => 1, 'msg' => '验证码错误,请重新输入！'));
            }
        }else{
            $my_header = array(
                'header'=>true,
                'header1'=>'用户注册',

                'header2'=>true
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $this->display();
        }
    }



    /*验证手机号是否注册，没有注册就发送手机验证码*/
    public function CheckUserYZM(){
        $mobile = I('post.mobile');
        $dao = new LoginModel();
        $user = $dao->FindByMobile($mobile);
        if ($user) {
            $this->ajaxReturn(array('code' => 1, 'msg' => '对不起，该手机号已被注册！'));
        }else{
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
    /*协议*/
    public function near_contract(){

        $my_header = array(
            'header'=>true,
            'header1'=>'注册协议',
            'body_style'=>'background:#fff;',
            'header2'=>true
        );
        $my_header = array_merge($this->header,$my_header);
        $this->assign($my_header);
        //加载视图
        $this->display();
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

}
