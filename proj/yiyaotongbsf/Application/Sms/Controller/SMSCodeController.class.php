<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/24
 * Time: 下午3:28
 */
namespace Sms\Controller;

use Home\Controller\BaseController;
use Sms\Model\SmsCodeModel;
use Sms\Api\SmsApi;
use Think\Controller;

/**
 * 短信验证码控制器
 * Class SMSCodeController
 * @package Sms\Controller
 */
class SMSCodeController extends BaseController
{
    /**
     * 获取短信验证码
     */
    public function gainCode()
    {
        if (I('request.f') == 'json') {
            if (!I('mobile')) responseJson(0, '请输入手机号码');
            if (!preg_match('/^(13|14|15|18|17)[0-9]{9}/', I('mobile'))) responseJson(0, '手机号码格式不正确');
            defined('SMSCODETEMPLATE') || throw_exception('SMSCODE配置错误：缺少SMSCODETEMPLATE');
            $smsApi = new SmsApi();
            $code = rand(1000, 9999);
            $mobile = trim(I('mobile'));
            $expire = 10;
            $params = array('to' => $mobile, 'datas' => array($code, $expire), 'tempId' => SMSCODETEMPLATE);
            $result = $smsApi->sendTemplateSMS($params, $code, $this->userId);
            if ($result) {
                responseJson(1, '发送成功');
            } else {
                responseJson(0, '发送失败');
            }
        }
    }

    /**
     * 验证手机号码是否正确
     */
    public function checkCode()
    {
        if (I('request.f') == 'json') {
            if (!I('mobile')) responseJson(0, '请输入手机号码');
            if (!preg_match('/^(13|14|15|18|17)[0-9]{9}/', I('mobile'))) responseJson(0, '手机号码格式不正确');
            if (!I('code')) responseJson(0, '请输入短信验证码');
            $smsApi = new SmsApi();
            die($smsApi->checkCode(I('mobile'), I('code')));
        }
    }
}