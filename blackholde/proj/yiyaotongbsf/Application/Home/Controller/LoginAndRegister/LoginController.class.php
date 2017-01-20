<?php
namespace Home\Controller\LoginAndRegister;
use Home\Controller\BaseController;
use Home\Library\QqLogin\QqConnect;
use Home\Library\Weixin\Class_Weixin_Adv;
use Home\Model\LoginAndRegister\LoginModel;
use Home\Model\LoginAndRegister\CookieModel;
use Home\Model\LoginAndRegister\RegisterModel;
use Home\Model\UserCenter\UserCenterModel;
use Sms\Controller;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;
use Think\Verify;
use Home\Library\Weibo\Api\SaeTOAuthV2;

/**
 * 该类主要是实现用户登录
 **/
class LoginController extends BaseController {
    /*用户登录入口方法*/
    public function Index(){
        $f =I('t');
        $Verify = new \Think\Verify();
        if(!empty($_COOKIE['newid'])){
           $cookiecode = $_COOKIE['newid'];
        }
        if($cookiecode){
            $co =new CookieModel();
            $result=$co->FindidBynewid($cookiecode);
        }
        if($f == 'wap'){/*mobile*/
            $o = new SaeTOAuthV2( '1071184173','cd77bf49326a116d490448e4ea903c40' );
            $code_url = $o->getAuthorizeURL();
            $weixin_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WX_appID."&redirect_uri=".WX_URL."&response_type=code&scope=snsapi_userinfo&state=a#wechat_redirect";
            $this->assign('wburl',$code_url);
            $this->assign('weixin_url',$weixin_url);
            $this->display('LoginAndRegister/login_mobile');
        }else{
            $o = new SaeTOAuthV2( '3541069178','7ebbd7e72e955e59cb9494d9b815e209' );
            $code_url = $o->getAuthorizeURL();
            $weixin_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WX_appID."&redirect_uri=".WX_URL."&response_type=code&scope=snsapi_login&state=a#wechat_redirect";
            $this->assign('wburl',$code_url);
            $this->assign('weixin_url',$weixin_url);
            if(count($result)>0){
                $this->LoginbyNopassword($result);
            }else{
                $this->display('LoginAndRegister/login');
            }
        }
    }
    /*免登陆方法*/
    public function LoginbyNopassword($result){
        $userid=$result['userid'];
        $dao =new LoginModel();
        $cookie_unit =new CookieModel();
        $userinfo= $dao->FindById($userid);
      /*  if($userinfo){
            $cookie_unit->creatnewid($userinfo['id']);//给用户id添加随机变量写入缓存，
        }*/
        $this->assign('userinfo',$userinfo);
        //var_dump($userinfo);die;
        if($userinfo['third_name']){
            $this->assign('third_name',$userinfo['third_name']);
        }
        $this->assign('mobile',$userinfo['mobile']);
        $this->display('UserCenter/myself');/*pc*/

    }
    /*登录成功后的用户信息处理并跳转指定页面*/
    public function Login_Act(){
        $usermobile =I('post.user_name');/*loginhtml中有原来的name=number改为user_number*/
        $t= $_REQUEST['t'];
        $rememberuser =I('post.un-login');/*记住一个月密码*/
        $dao =new LoginModel();
        $cookie_unit =new CookieModel();
        $userinfo= $dao->FindByMobile($usermobile);
        if($userinfo){
           $cookie_unit->creatnewid($userinfo['id']);//给用户id添加随机变量写入缓存，
        }
        if( $t=='wap'){
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');/*mobile*/
        }else{
            $this->assign('userinfo',$userinfo);
            //var_dump($userinfo);die;
            if($userinfo['third_name']){
                $this->assign('third_name',$userinfo['third_name']);
            }
            $this->assign('mobile',$userinfo['mobile']);
            $this->display('UserCenter/myself');/*pc*/
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

    /*找回密码页*/
    public function FindPassword(){
       $this->display('LoginAndRegister/findpass_mobile');
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
    /*设置新密码*/
    public function ResetPassword(){
        $mobile =I('post.user_number');
        $this->assign('mobile',$mobile);
        $this->display('LoginAndRegister/resetpassword');
    }
    /*更新用户密码*/
    public function UpPassword(){
        $newid=$_COOKIE['newid'];
        $cookie_unit = new CookieModel();
        $user = new UserCenterModel();
        if($newid) {
            $list = $cookie_unit->FindidBynewid($newid);
            $userinfo = $user->FindByUserid($list['userid']);
            $password =I('post.password');
            $mobile =I('mobile');
            $t=$_REQUEST['t'];
            $password = think_ucenter_md5($password, UC_AUTH_KEY);
            $dao = new LoginModel();
            $data['password'] =$password;
            $result = $dao->where("mobile =$mobile")->save($data); // 根据条件更新记录
            if($result !== false){
                if($t=='wap'){
                    $this->assign('userinfo',$userinfo);
                    $this->display('UserCenter/myself');/*手机跳转的页面*/
                }else{
                    $this->display('LoginAndRegister/findpassword3');/*pc跳转页面*/
                }
            }
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*第三方用户登录*/
    public function Qq_Login(){
        $qqobj = new QqConnect();
        $qqobj->getAuthCode();

    }
    /*qq登陆回调地址*/
    public function QqLogin_Act(){
        $qqobj = new QqConnect();
        $cookie_unit =new CookieModel();
        $re=new RegisterModel();
        $dao = new LoginModel();
        $user =new UserCenterModel();
        $newid=$_COOKIE['newid'];
        $code =$_GET['code'];
        $result=$qqobj->getUsrInfo();

        $result =json_decode($result,true);
        $username = $result['nickname'];
        $data['third_name'] =$username;
        $data['register_time'] =date('Y-m-d H:i:s');
        if($newid){
            $list =$cookie_unit->FindidBynewid($newid);
            $userinfo= $user->FindByUserid($list['userid']);
            $tt = $user->InsertemailCode($userinfo['mobile'], $data);
            $this->assign('userinfo',$userinfo);
            $this->assign('mobile',$userinfo['mobile']);
            $this->display('UserCenter/myself');
        }else{
            $userinfo= $dao->FindByName($username);
            if($userinfo){
                $this->assign('mobile',$userinfo['mobile']);
                $cookie_unit->CreatNewid($userinfo['id']);//给用户id添加随机变量写入缓存，
            }else{
                $userid =$re->InsertData($data);
                if($userid){
                    $cookie_unit->CreatNewid($userid);//给用户id添加随机变量写入缓存，
                }
            }
            $this->assign('third_name',$username);
            $this->display('UserCenter/myself');/*pc*/
        }

    }
    /*验证码实现*/
    public function Verify_C(){
        $Verify = new Verify();
        $Verify->useImgBg = true;
        $Verify->fontSize = 18;
        $Verify->length   = 4;
        $Verify->useNoise = false;
        $Verify->codeSet = '0123456789';
        $Verify->imageW = 130;
        $Verify->imageH = 50;
        //$Verify->expire = 600;
        $Verify->entry();
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
    /*微博回调函数*/
    public function WbLogin_Act(){
        $dao = new LoginModel();
        $user =new UserCenterModel();
        $re=new RegisterModel();
        $o = new SaeTOAuthV2( WB_AKEY,WB_SKEY);
        if (isset($_REQUEST['code'])) {
            $keys = array();
            $keys['code'] = $_REQUEST['code'];
            $keys['redirect_uri'] = WB_CALLBACK_URL;
            try {
                $token = $o->getAccessToken( 'code', $keys ) ;
            } catch (OAuthException $e) {
            }
        }
        if ($token) {
            $_SESSION['token'] = $token;
            setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
            $c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
            $ms  = $c->home_timeline(); // done
            $uid_get = $c ->get_uid();
            $uid = $uid_get['uid'];
            $user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
           // $userdata =array('name' => $user_message['name'],'user_id'=> (int)$user_message['id']);
            $data['third_name'] = $user_message['name'];
            $userinfo = $dao->SelectWb($user_message['name']);
            if($userinfo){
                $cookie_unit->CreatNewid($userinfo['id']);//给用户id添加随机变量写入缓存，
            }else{
                $userid =$re->InsertData($data);
                if($userid){
                    $cookie_unit->CreatNewid($userid);//给用户id添加随机变量写入缓存，
                }
            }
            //登录成功跳转地址
            $this->assign('third_name', $data['third_name']);
            $this->display('UserCenter/myself');/*pc*/
        } else {
            //登录失败跳转地址
            $this->display('UserCenter/login');
        }
        $this->assign('third_name', $data['third_name']);
        $this->display('UserCenter/myself');/*pc*/
    }
    /*微信登录*/
    public function loginByWX(){
        $re=new RegisterModel();
        $weixin=new class_weixin_adv(WX_appID, WX_appsecret);
        $code =$_GET['code'];
        //$code ='031f480ff05648034bcda9e0a56d697S';
        //code 获取access_token，openid
        $access_url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".WX_appID."&secret=".WX_appsecret."&code=".$code."&grant_type=authorization_code";
        $res = $weixin->https_request( $access_url);
        $result = json_decode($res, true);
        $access_token = $result["access_token"];
        $open_id = $result["openid"];
        $userrow=$weixin->get_user_info($openid);
        $userdata =array("openid"=> $userrow['openid'],"name"=>$userrow['nickname']);
        $name = $userrow['nickname'];
        $dao = new LoginModel();
        $userinfo= $dao->FindByName($username);
        if($userinfo){
            $cookie_unit->CreatNewid($userinfo['id']);//给用户id添加随机变量写入缓存，
        }else{
            $userid =$re->InsertData($data);
            if($userid){
                $cookie_unit->CreatNewid($userid);//给用户id添加随机变量写入缓存，
            }
        }
        $this->assign('userinfo',$userinfo);
        $this->assign('third_name',$name);
        $this->display('UserCenter/myself');/*pc*/
       // header('Location:'.WX_URL);
    }
    /*pc登录找回密码*/
    public function FindPasswordpc(){
        $this->display('LoginAndRegister/findpassword1');
    }
    /*pc设置新密码*/
    public function SetPasswordpc(){
        $mobile =I('post.number');
        $this->assign('mobile',$mobile);
        $this->display('LoginAndRegister/findpassword2');
    }
    /*pc完成密码设置*/
    public function FinishPassword(){
        $password=I('post.userpassword');
        $mobile =I('post.mobile');
        $password = think_ucenter_md5($password, UC_AUTH_KEY);
        $dao = new LoginModel();
        $result = $dao->where(array("mobile =$mobile"))->setField('password',$password);
        if($result){
            $this->display('LoginAndRegister/findpassword3');
        }
        $this->display('LoginAndRegister/findpassword3');

    }


}
