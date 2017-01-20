<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/24
 * Time: 下午2:32
 */

namespace Sms\Api;
define('UC_CLIENT_PATH', dirname(dirname(__FILE__)));
//载入配置文件
require_cache(UC_CLIENT_PATH . '/Conf/config.php');


//载入配置文件
use Sms\Model\SmsCodeModel;
use Sms\Library\YunTongXun\Service\CCPRestSDKService;

/**
 * 短信的Api接口
 * Class Api
 * @package Sms\Api
 */
class SmsApi
{
    private $config;
    private $service;

    /**
     * 构造方法，检测相关配置
     */
    public function __construct()
    {
        //相关配置检测
        defined('SMSSDK') || throw_exception('SMSCODE配置错误：缺少SMSSDK');
        defined('SMSCONFIG') || throw_exception('SMSCODE配置错误：缺少SMSCONFIG');
        $smsConfig = json_decode(SMSCONFIG, true);
        $sdk_Config = $smsConfig[SMSSDK];
        if (empty($sdk_Config)) throw_exception('SMSCODE配置错误');
        foreach ($sdk_Config as $v) {
            if (is_null($v) || trim($v) == '') {
                throw_exception('SMSCODE配置错误');
            }
        }

        $this->config = $sdk_Config;
        $this->initServiceObj(SMSSDK);
    }

    /**
     * 发送模版消息
     * @param $params 发送短信厂商的参数
     * @param $smsCode 短信验证码
     * @param int $expire 失效时间以秒为单位
     * @param null $uid 用户id
     * @return bool
     */

    public function sendTemplateSMS($params, $smsCode, $uid = null, $expire = 600)
    {
        // 发送模板短信
        $result = $this->service->sendTemplateSMS($params);
        if ($result == NULL) {
            return false;
        }
        if ($result->statusCode != 0) {
            return false;
        } else {
            $model = new SmsCodeModel();
            return $model->addSmsCode($smsCode, $expire, $uid);
        }
    }

    /**
     * 验证验证码是否正确
     * @param $mobile
     * @param $code
     * @return json
     */
    public function checkCode($mobile, $code)
    {
        $model = new SmsCodeModel();
        $result = $model->checkCodeByMobileAndCode($mobile, $code);
        if ($result) {
            return getJson(1, '成功');
        } else {
            return getJson(0, $model->getError());
        }

    }

    private function initServiceObj($sdk)
    {
        if ($sdk == 'YUNTONGXUN') {
            return $this->service = new CCPRestSDKService($this->config);
        }
    }
}