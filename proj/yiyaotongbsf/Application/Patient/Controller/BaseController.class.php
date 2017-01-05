<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午2:42
 */
namespace Patient\Controller;

use Patient\Library\Token\Token;
use Patient\Model\InitUserId\InitUserIdModel;

/**
 * 前台需继承的控制器类
 * Class BaseController
 * @package Patient\Controller
 */
class BaseController extends \Think\Controller
{
    /**
     * 视图头部设置
     * @var array
     */
    protected $header = array(
        'back'=>-1,
        'h_title'=>'',
        'body_style'=>'',
        'header'=>false,//是否显示头部
        'header1'=>'',//头部标题
        'header2'=>true,//头部返回按钮
        'header3'=>'',//头部右侧标题
        'header4'=>'',//头部右侧链接
        'header5'=>'',//头部右侧的id
        'footer'=>false,//是否显示尾部
        'footer1'=>'doctor',//尾部当前状态
    );

    /**
     * 用户登录id
     * @var null
     */
    protected $userId = null;//没多大用,users的id
    protected $userinfo = null;//用户信息

    protected function _initialize()
    {
        $isSecret = I('request.x');
        $userSecret = I('server.HTTP_USERSECRET');

        //设置相关信息
        $this->userId = session('user_id');
        $this->handleLogin();

        if (is_null($userSecret) || trim($userSecret) == '') {
            $userSecret = cookie('userSecret');
        }
        if ($userSecret) {
            $this->initUserId($userSecret);
        }
        if ($isSecret == 'secret') {
            $timeStamp = I('server.HTTP_TIMESTAMP');
            $userToken = I('server.HTTP_TOKEN');
            $module = CONTROLLER_NAME;

            if (is_null($timeStamp) || is_null($userToken)) {
                responseJson(1000, '非法访问');
            }
            $token = new Token();
            if (!$token->checkToken($timeStamp, $userToken, $module, $userSecret)) {
                responseJson(1000, '非法访问');
            }
        }
    }

    /*
     * 根据userSecret初始化userId
     * @param $userSecret
     */
    private function initUserId($userSecret)
    {
        if ($userSecret) {
            $intUserIdModel = new InitUserIdModel();
            $userCookie = $intUserIdModel->getUserCookieByUserSecret($userSecret);
            if (count($userCookie) > 0) {
                $expireTime = $userCookie['expire_time'];
                if ($expireTime > time()) {
                    $this->userId = $userCookie['userid'];
                }
            }
        }
    }




    /**
     * 判断用户是否登录
     * @return bool
     */
    public function isLogin()
    {
        if (!is_null($this->userId)) {
            return true;
        } else {
            return false;
        }
    }
    //验证码
    public function verify() {
        $config = array(
            'fontSize' => 10, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false, // 关闭验证码杂点
        );
        $verify = new \Think\Verify($config);
        $verify->codeSet = '0123456789';
        $verify->entry();
    }
    /**
     * 验证图形验证码
     */
    public function code() {
        $verify = I('vcode', '', 'htmlspecialchars,trim');
        if (!$this->checkVerify($verify)) {
            $data['status'] = "0100";
        } else {
            $data['status'] = "0000";
        }
        $this->ajaxReturn($data);
    }


    //验证码检测
    public function checkVerify($code, $id = '') {
        $verify = new \Think\Verify();
        return $verify->check($code, $id);
    }
    /**
     * 验证用户是否登录,设置用户信息
     * @param $flag bool 默认获取用户信息, 为true时验证登录
     */
    protected function handleLogin($flag=false){
        $userinfo = unserialize(session('userinfo'));

        if($this->userId && !$userinfo){
            //重新设置userinfo
            $userinfo = M('Patient')->find($this->userId);
            if($userinfo){
                session('userinfo', serialize($userinfo));
            }else{
                if(IS_POST){
                    $this->ajaxReturn(array('code'=>-1,'msg'=>'登录信息异常，请重新登录'));
                }else{
                    cookie("url",$_SERVER['HTTP_REFERER']);
                    $this->redirect('/Doctor/Login/Login/Login');//跳转到登录页
                }
            }
        }
        if(!$userinfo && $flag==true){
            if(IS_POST){
                $this->ajaxReturn(array('code'=>-1,'msg'=>'登录信息异常，请重新登录'));
            }else{
//                cookie("url",$_SERVER['HTTP_REFERER']);//不要回跳.有问题
                $this->redirect('/Patient/LoginAndRegister/Login/login');//跳转到登录页
            }
        }else{
            //设置UID
            defined('UID') or define('UID',$userinfo['id']);
            $this->userinfo = $userinfo;
        }
        if(cookie('url') && defined('UID')){
            $this->assign ('uid',UID);
        }
    }

    /**
     * 得到未读消息数
     * @param $patient_id
     * @return int|number
     */
    public function getNonReadNum($patient_id=0){
        if($patient_id==0){
            if(defined('UID')){
                $patient_id = UID;
            }else{
                $this->ajaxReturn(array('code'=>0,'data'=>0));
            }
        }
        $where = array(
            'patient_id'=>$patient_id,
            'now'=>1,
            'no_read_num'=>array('neq',0),
        );
        $res = M('DoctorConsult')->field('no_read_num')->where($where)->select();
        if($res){
            $num = array_column($res,'no_read_num');
            $this->ajaxReturn(array('code'=>0,'data'=>array_sum($num)));
        }else{
            $this->ajaxReturn(array('code'=>0,'data'=>0));
        }
    }
}