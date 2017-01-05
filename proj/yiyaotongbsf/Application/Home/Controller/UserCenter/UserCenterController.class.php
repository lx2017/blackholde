<?php
namespace Home\Controller\UserCenter;
use Home\Controller\BaseController;
use Home\Controller\LoginAndRegister\LoginController;
use Home\Model\UserCenter\UserCenterModel;
use Home\Model\LoginAndRegister\CookieModel;
use Home\Model\LoginAndRegister\LoginModel;
use Think\Upload;
use Email\Controller;
use Email\Controller\EmailController;
use Email\Model\EmailRecordModel;
use Email\Api;
use Email\Api\EmailApi;
use Email\Library\SendCloudEmail\Service\SendCloudEmailService;
/**
 * 该类主要是实现用户登录
 **/
class UserCenterController extends BaseController {
    /*个人中心首页入口*/
    public function Personal(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $cook = new CookieModel();
            $dao =new UserCenterModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $userinfo= $dao->FindByUserid($list['userid']);
            $this->assign('userinfo',$userinfo);
            $this->assign('mobile',$userinfo['mobile']);
            $this->display('UserCenter/myself');
        }else{
            $use =new LoginController();
            $use->Index();
        }
    }
    /*添加邮箱*/
    public function AddEmail(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $mobile = I('mobile');
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/bindemail');
        }else{
            $use =new LoginController();
            $use->Index();
        }


    }
    /*并保存更新数据*/
    public function BaseInfo(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        $dao = new UserCenterModel();
        $cook = new CookieModel();
        $upload = new \Think\Upload();
        $list =$cook->FindidBynewid($newid);/*用户id*/
        /*if(!empty($_FILES['up']['name'])){
            $upload->maxSize   =     1024*1024*5 ;
            $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg','bmp');// 设置附件上传类型
            $upload->rootPath  =     '';
            $upload->savePath  =     '/Uploads/Picture/';
            $file=$upload->upload();
            $data['picsavename']=$file[0]['savename'];
        }*/

        //$list['userid']=26;/*只有登录过才可以获取*/
        if($_POST){
            $data['nickname'] =I('post.usename');/*昵称*/
            $year =I('post.daty-1');
            $month =I('post.daty-2');
            $day =I('post.daty-3');
            $data['sex'] =I('post.radio');
            $data['headpic'] =I('post.up');
            $data['signature'] =I('post.signature');
            $data['province'] =I('post.s_province');
            $data['city'] =I('post.s_city');
            $data['county'] =I('post.s_county');
            $data['address_detail'] =I('post.address_detail');
            $data['birthday'] = $year.'-'.$month.'-'.$day;

            $boot = $dao->UpInfo($list['userid'],$data);
            if($boot !== false){
               // $this->display('UserCenter/bindemail');
                $this->Personal();
            }else{
                $this->error("更新数据失败"); //查询失败后返回上一页
            }
        }
    }
    /*发送邮件*/
    public function BindEmail(){
       // $dao =new EmailApi();
        $dao = new EmailController();
        $email = I('post.useremail');
        $code = uniqid();
        $boot =$dao ->sendCloudTemplateEmail($email, $code);
        if($boot){
            $dao = new UserCenterModel();
            $mobile =I('post.mobile');
            $codeperiod =time()+3600;
            $emailinfo['emailcode_period'] =$codeperiod;
            $emailinfo['emailcode'] =$code;
            $dao->InsertemailCode($mobile,$emailinfo);/*邮箱信息入库*/
            $emailurl = $this->gotomail($email);
            $this->assign('email',$email);
            $this->assign('emailurl',$emailurl);
            $this->display('UserCenter/bindemail2');
        }
    }
    /*检查邮箱验证码*/
    public function CheckCode(){
        $code=$_GET['code'];
        $email =$_GET['email'];
        $dao = new UserCenterModel();
        $result = $dao->CheckEmailCode($email,$code);
        if($result){
            if($result['yn']=='no'){
                $emailurl = $this->gotomail($email);
                $this->assign('email',$email);
                $this->assign('emailurl',$emailurl);
                $this->display('UserCenter/bindemail2');/*重新发送页*/
            }else{
                $this->display('UserCenter/bindemail3');/*发送成功页*/
            }
        }else{
            $emailurl = $this->gotomail($email);
            $this->assign('email',$email);
            $this->assign('emailurl',$emailurl);
            $this->display('UserCenter/bindemail2');/*重新发送页*/
        }

    }
    /*邮箱跳转首页*/
    function gotomail($mail){
        $t=explode('@',$mail);
        $t=strtolower($t[1]);
        if($t=='163.com'){
            return 'mail.163.com';
        }else if($t=='vip.163.com'){
            return 'vip.163.com';
        }else if($t=='126.com'){
            return 'mail.126.com';
        }else if($t=='qq.com'||$t=='vip.qq.com'||$t=='foxmail.com'){
            return 'mail.qq.com';
        }else if($t=='gmail.com'){
            return 'mail.google.com';
        }else if($t=='sohu.com'){
            return 'mail.sohu.com';
        }else if($t=='tom.com'){
            return 'mail.tom.com';
        }else if($t=='vip.sina.com'){
            return 'vip.sina.com';
        }else if($t=='sina.com.cn'||$t=='sina.com'){
            return 'mail.sina.com.cn';
        }else if($t=='tom.com'){
            return 'mail.tom.com';
        }else if($t=='yahoo.com.cn'||$t=='yahoo.cn'){
            return 'mail.cn.yahoo.com';
        }else if($t=='tom.com'){
            return 'mail.tom.com';
        }else if($t=='yeah.net'){
            return 'www.yeah.net';
        }else if($t=='21cn.com'){
            return 'mail.21cn.com';
        }else if($t=='hotmail.com'){
            return 'www.hotmail.com';
        }else if($t=='sogou.com'){
            return 'mail.sogou.com';
        }else if($t=='188.com'){
            return 'www.188.com';
        }else if($t=='139.com'){
            return 'mail.10086.cn';
        }else if($t=='189.cn'){
            return 'webmail15.189.cn/webmail';
        }else if($t=='wo.com.cn'){
            return 'mail.wo.com.cn/smsmail';
        }else if($t=='139.com'){
            return 'mail.10086.cn';
        }else {
            return '';
        }
    }
    /*pc更新手机号*/
    public function ResetMobile(){
        if($_COOKIE['newid']){
            $mobile =I('mobile');
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/updatatel');
        }else{
            $use =new LoginController();
            $use->Index();
        }


    }
    /*pc更新新的手机号*/
    public function ResetNewMobile(){
        $mobile =$_REQUEST['mobile'];
        $this->assign('mobile',$mobile);
        $this->display('UserCenter/updatatel2');
    }
    /*完成手机修改，更新数据库*/
    public function FinishSetMobile(){
        $mobile=$_REQUEST['mobile'];
        $newmobile=I('post.newtel');
        $dao = new UserCenterModel();
        $result = $dao->UpuserMobile($mobile,$newmobile);
        if($result ){
            $this->display('UserCenter/updatatel3');
        }

    }
    /*更改邮箱*/
    public function ResetEmail(){
        if($_COOKIE['newid']){
            $mobile =I('mobile');
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/updataemail');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*重新设置邮箱*/
    public function NewtEmail(){
        $email =I('post.new-email');
        $mobile =I('post.mobile');
        $dao = new EmailController();
        $code = uniqid();
        $boot =$dao ->sendCloudTemplateEmail($email, $code);
        if($boot){
            $emailurl = $this->gotomail($email);
            $user = new UserCenterModel();
            $codeperiod =time()+3600;
            $emailinfo['emailcode_period'] =$codeperiod;
            $emailinfo['emailcode'] =$code;
            $user->InsertemailCode($mobile,$emailinfo);/*邮箱信息入库*/
            $this->assign('email',$email);
            $this->assign('emailurl',$emailurl);
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/updataemail2');
        }

    }

    /*个人信息*/
    public function MyselfInfo(){
        if($_COOKIE['newid']){
            $newid =$_COOKIE['newid'];
            $cook = new CookieModel();
            $dao =new UserCenterModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $userinfo= $dao->FindByUserid($list['userid']);
            if($userinfo['birthday']){
                $userinfo['birthday'] = explode("-", $userinfo['birthday']);
                $userinfo['nowyear']=intval($userinfo['birthday'][0]);
                $userinfo['nowmon']=intval($userinfo['birthday'][1]);
                $userinfo['nowday']=intval($userinfo['birthday'][2]);
            }
            $year = date('Y',time());
            $years=range(1900,$year);
            $allyears = array_reverse($years);
            $month =range(1,12);
            $days =range(1,31);
            $this->assign('userinfo',$userinfo);
            $this->assign('years',$allyears);
            $this->assign('month',$month);
            $this->assign('days',$days);
            $this->display('UserCenter/myselfinfo');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*检测新邮箱用户是否存在*/
    public function CheckEmail(){
        $mobile=I('post.mobile');
        $email =I('post.email');
        $dao = new UserCenterModel();
        $boot =$dao->SelectEmail($email,$mobile);
        if(count($boot)>0){
            responseJson(1007, '邮箱已经存在');
        } else{
            responseJson(1002, '邮箱可以添加');
        }
    }
    /*退出登录*/
    public function exitlogin(){
        $path = $_SERVER['HTTP_HOST'];
        setcookie('newid','',time()-3600,'/',$path);
        $this->success('退出成功','/Home/LoginAndRegister/Login/Index');
    }
    public function UpUserPassword(){
        if($_COOKIE['newid']){
            $mobile=I('mobile');
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/updatapassword');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*用户更改密码*/
    public function ResetPassword(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $password=I('post.new-password');
            $password = think_ucenter_md5($password, UC_AUTH_KEY);
            $dao = new UserCenterModel();
            $cook = new CookieModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $data['password']=$password;
            $boot = $dao->UpInfo($list['userid'],$data);
            if($boot!== false){
                $this->display('UserCenter/updatapassword2');
            }else{
                $this->error("修改失败"); //查询失败后返回上一页
            }
        }


    }
    /*检测原始密码是否正确*/
    public function CheckPassword(){
        $mobile=I('post.mobile');
        $password =I('post.password');
        $password = think_ucenter_md5($password, UC_AUTH_KEY);
        $dao = new UserCenterModel();
        $boot =$dao->SelectPassword($password,$mobile);
        if(count($boot)>0){
            responseJson(1002, '原始密码正确');
        } else{
            responseJson(1006, '原始密码不正确');
        }
    }
    /*第三方用户解绑*/
    public function Delthirdname(){
        $third_name =I('third');
        $dao =new LoginModel();
        $user = new UserCenterModel();
        $userinfo= $dao->FindByName($third_name);
        $boot =$user->DelthirdName($third_name);
        if($boot){
            $this->assign('userinfo',$userinfo);
            $this->assign('mobile',$userinfo['mobile']);
        }
        $this->display('UserCenter/myself');/*pc*/
    }
    /*手机版修改昵称*/
    public function Mynickname(){
        $nickname =I('nickname');
        $id =I('id');/*用户id*/
        if($nickname){
            $this->assign('nickname',$nickname);
        }
        $this->assign('userid',$id);
        $this->display('UserCenter/editemylove');
    }
    /*手机版修改性别*/
    public function Mysex(){
        $nickname =I('sex');
        $id =I('id');/*用户id*/
        if($nickname){
            $this->assign('sex',$nickname);
        }
        $this->assign('userid',$id);
        $this->display('UserCenter/editesex');
    }
    /*跟新用户昵称入库*/
    public function Upusernickname(){
        $userid=I('userid');
        $nickname=I('mynickname');
        $user = new UserCenterModel();
        $data['nickname'] =$nickname;
        $boot =$user->UpInfo($userid,$data);
        if($boot !== false){
            $userinfo =$user->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }

    }
    /*更新*/
    public function Upusersex(){
        $userid=I('post.userid');
        $mysex=I('post.mysex');
        $user = new UserCenterModel();
        $data['sex'] =$mysex;
        $boot =$user->UpInfo($userid,$data);
        if($boot !== false){
            $userinfo =$user->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }else{
            $this->assign('userid',$userid);
            $this->display('UserCenter/editesex');
        }
    }
    /*修改生日*/
    public function Mybirdthday(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid) {
            $cook = new CookieModel();
            $list = $cook->FindidBynewid($newid);/*用户id*/
            $id =$list['userid'];
            $this->assign('userid',$id);
            $this->display('UserCenter/editebirdthday');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*生日更新入库*/
    public function Upbirdthday(){
        $user = new UserCenterModel();
        $newid = $_COOKIE['newid'];/*获取用户newid*/

       if($newid) {
            $cook = new CookieModel();
            $list = $cook->FindidBynewid($newid);
            $userid =$list['userid'];
            $mybirdthday=I('post.date');

           $data['birthday'] =$mybirdthday;
            $boot =$user->UpInfo($userid,$data);
            if(false !== $boot || 0 !== $boot){

                $userinfo =$user->FindByUserid($userid);
              $this->assign('userinfo',$userinfo);
                $this->display('UserCenter/myself_mobile');
            }
       }else {
           $use = new LoginController();
           $use->Index();
       }

    }
    /*个性签签名的修改*/
    public function Mysignature(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid) {
            $cook = new CookieModel();
            $list = $cook->FindidBynewid($newid);/*用户id*/
            $userid = $list['userid'];
            $signature =I('signature');
            if($signature){
                $this->assign('signature',$signature);
            }
            $this->assign('userid',$userid);
            $this->display('UserCenter/editsignature');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*个性名称更新入库*/
    public function Upsignature(){
        $userid=I('post.userid');
        $mysignature=I('post.mysignature');
        $user = new UserCenterModel();
        $data['signature'] =$mysignature;
        $boot =$user->UpInfo($userid,$data);
        if($boot !== false){
            $userinfo =$user->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }else{
            $this->assign('userid',$userid);
            $this->display('UserCenter/editsignature');
        }
    }
    /*修改地址*/
    public function Myaddress(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid) {
            $cook = new CookieModel();
            $list = $cook->FindidBynewid($newid);/*用户id*/
            $id =$list['userid'];
            $this->assign('userid',$id);
            $this->display('UserCenter/editadd');
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
    /*更新地址*/
    public function Upaddress(){
        $userid=I('post.userid');
        $data['province']=I('post.s_province');
        $data['city'] = I('post.s_city');
        $data['county'] = I('post.s_city');
        $data['address_detail'] = I('post.address');
        $user = new UserCenterModel();
        $boot =$user->UpInfo($userid,$data);
        if($boot !== false || 0!== false){
            $userinfo =$user->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }else{
            $this->assign('userid',$userid);
            $this->display('UserCenter/editadd');
        }
    }
    /*修改头像*/
    public function Upimgage(){
        $userid=I('post.userid');
        $data['headpic'] =I('post.up');
        $user = new UserCenterModel();
        $boot =$user->UpInfo($userid,$data);

            $userinfo =$user->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');

    }
    /*wap 修改手机号*/
    public function Mynewmobile(){
        $mobile=I('mobile');
        $userid =I('id');
        $this->assign('userid',$userid);
        $this->assign('mobile',$mobile);
        $this->display('UserCenter/edittel');
    }
    /*检测新用户是否已经在*/
    public function CheckisMobile(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $cook = new CookieModel();
            $dao =new UserCenterModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $mobile =I('mobile');
            $user = new UserCenterModel();
            $boot =$user->Checkismobile($list['userid'],$mobile);
            if(count($boot)>0){
                responseJson(1007, '该用户已经存在');
            }else{
                responseJson(1006, '该用户可以使用');
            }
        }else{
            $use =new LoginController();
            $use->Index();
        }
    }
    /*更新手机入库*/
    public function UpnewMobile(){
        $mobile=I('post.oldmobile');
        $newmobile=I('post.mobile');
        $userid=I('post.userid');
        $dao = new UserCenterModel();
        $result = $dao->UpuserMobile($mobile,$newmobile);
        if($result ){
            $userinfo =$dao->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }else{
            $this->assign('userid',$userid);
            $this->assign('mobile',$mobile);
            $this->display('UserCenter/edittel');
        }
    }
    /*wap 端更改密码*/
    public function Mypassword(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $cook = new CookieModel();
            $dao =new UserCenterModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $userid =$list['userid'];
            $this->assign('userid',$userid);
            $this->display('UserCenter/editpassword_mobile');
        }else{
            $use =new LoginController();
            $use->Index();
        }
    }
    /*确认原始密码是否正确*/
    public function QRpassword(){
        $password=I('password');
        $password =  think_ucenter_md5($password, UC_AUTH_KEY);
        $id=I('id');
        $dao =new UserCenterModel();
        $boot =$dao->FindidBypassword($password,$id);/*用户id*/
        if(count($boot)>0){
            responseJson(1007, '原始密码正确');
        }else{
            responseJson(1006, '原始密码不正确');
        }
    }
    /*更新密码入库*/
    public function Upnewpassword(){
        $dao = new UserCenterModel();
        $userid =I('post.userid');
        $newpassword =I('post.password');
        $password =think_ucenter_md5($newpassword, UC_AUTH_KEY);
        $data['password'] =$password;
        $boot =$dao->UpInfo($userid,$data);
        if($boot !==false){
            $userinfo =$dao->FindByUserid($userid);
            $this->assign('userinfo',$userinfo);
            $this->display('UserCenter/myself_mobile');
        }else{
            $this->assign('userid',$userid);
            $this->display('UserCenter/editpassword_mobile');
        }

    }
    /*添加手机号*/
    public function AddMobile(){
        $this->display('UserCenter/addmobile');
    }
    /*添加手机入库*/
    public function AddnewMobile(){
        $newid = $_COOKIE['newid'];/*获取用户newid*/
        if($newid){
            $cook = new CookieModel();
            $dao =new UserCenterModel();
            $list =$cook->FindidBynewid($newid);/*用户id*/
            $userid =$list['userid'];
            $newmobile=I('post.newmobile');
            $result = $dao->UpusernewMobile($userid,$newmobile);
            if($result ){
                $this->display('UserCenter/finishmobile');
            }
        }else{
            $use =new LoginController();
            $use->Index();
        }

    }
}
