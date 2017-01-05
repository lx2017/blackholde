<?php
namespace Doctor\Controller\Login;
use Doctor\Controller\BaseController;
use Doctor\Model\Login\LoginModel;
use Doctor\Model\Login\CookieModel;
use Sms\Controller;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;
use Think\Verify;



/**
 * 该类主要是实现用户登录
 **/
class LoginController extends BaseController {
    /**
     * 登陆
     */
    public function login(){
        if(IS_POST){
            $mobile=I('post.mobile');
            $dao =new LoginModel();
            $user=$dao->FindByMobile($mobile);
            //判断账户类型
            if(empty($user)){
                $this->ajaxReturn(array('code'=>3,'msg'=>'用户不存在'));
            }
            //检验登录密码是否正确
            $password = think_ucenter_md5(I('post.password'), UC_AUTH_KEY);
            $password1 = $user['password'];
            if ($password1 == $password) {
                $cookie_unit =new CookieModel();
                if($user){
                    $cookie_unit->creatnewid($user['id']);//给用户id添加随机变量写入缓存，
                }
                session('user_id', $user['id']);
                //得到用户信息
                $userinfo = setUserinfo($user['id'],$user);
                if($userinfo==false){
                    $this->ajaxReturn(array('code'=>2,'msg'=>'用户不存在'));
                }
                //返回结果
                if($userinfo['login_type']==1){
                    $home_url = U('/Doctor/Doctor/Doctor/index');
                }else{
                    $home_url = U('/Doctor/Clinic/Clinic/index');
                }
                session('userinfo', serialize($userinfo));
                $url = cookie('url');
                //是否首次登陆
                if($user['is_first']==0){
                    $url = U('/Doctor/Doctor/Person/change');
                }
                if ($url) {
                    cookie('url',null);
                    $this->ajaxReturn(array('code'=>0,'url'=>$url));
                }else{
                    $this->ajaxReturn(array('code'=>0,'url'=>$home_url));
                }
            }else{
                $this->ajaxReturn(array('code'=>1,'msg'=>'密码不正确'));
            }
        }else{
            //设置头部
            $my_header = array(
                'body_style'=>'background:#fff;',
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
        $this->redirect('/Doctor/Login/Login/login');
    }

    /**
     * 忘记密码
     */
    public function forget(){
        if(IS_POST){
            //验证手机验证码是否正确
            $mobile = I('post.mobile');
            $smsApi = new SmsApi();
            $result = $smsApi->checkCode($mobile, I('post.code'));
            $result =json_decode($result,true);
            if ($result && $result['code']==1) {
                //获得账户
                $dao = new LoginModel();
                $user=$dao->FindByMobile($mobile);
                if(empty($user)){
                    $this->ajaxReturn(array('code' => 1,' msg'=>'该手机号未注册'));
                }
                //修改密码
                $password =I('post.password');
                $password = think_ucenter_md5($password, UC_AUTH_KEY);
                $data['password'] =$password;
                $result = $dao->where("id=".$user['id'])->save($data);
                if($result !== false){
                    $this->ajaxReturn(array('code'=>0,'url'=>U('login')));
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
                'header2'=>true,
            );
            $my_header = array_merge($this->header,$my_header);
            $this->assign($my_header);
            $this->display();
        }
    }


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
            $this->display('Login/login_mobile');
        }else{
            $o = new SaeTOAuthV2( '3541069178','7ebbd7e72e955e59cb9494d9b815e209' );
            $code_url = $o->getAuthorizeURL();
            $weixin_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WX_appID."&redirect_uri=".WX_URL."&response_type=code&scope=snsapi_login&state=a#wechat_redirect";
            $this->assign('wburl',$code_url);
            $this->assign('weixin_url',$weixin_url);
            if(count($result)>0){
                $this->LoginbyNopassword($result);
            }else{
                $this->display('Login/login');
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
       $this->display('Login/findpass_mobile');
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
        $this->display('Login/resetpassword');
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
                    $this->display('Login/findpassword3');/*pc跳转页面*/
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
        $this->display('Login/findpassword1');
    }
    /*pc设置新密码*/
    public function SetPasswordpc(){
        $mobile =I('post.number');
        $this->assign('mobile',$mobile);
        $this->display('Login/findpassword2');
    }
    /*pc完成密码设置*/
    public function FinishPassword(){
        $password=I('post.userpassword');
        $mobile =I('post.mobile');
        $password = think_ucenter_md5($password, UC_AUTH_KEY);
        $dao = new LoginModel();
        $result = $dao->where(array("mobile =$mobile"))->setField('password',$password);
        if($result){
            $this->display('Login/findpassword3');
        }
        $this->display('Login/findpassword3');

    }


}
