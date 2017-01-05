<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/28
 * Time: 下午2:32
 */

namespace Email\Api;

use Email\Model\EmailRecordModel;
use Email\Library\SendCloudEmail\Service\SendCloudEmailService;
define('UC_CLIENT_PATH', dirname(dirname(__FILE__)));
//载入配置文件
require_cache(UC_CLIENT_PATH . '/Conf/config.php');

class EmailApi
{
    private $config;
    private $from;
    private $service;

    function __construct()
    {
        //相关配置检测
        defined('EMAILSDK') || throw_exception('SMSCODE配置错误：缺少EMAILSDK');
        defined('EMAILCONFIG') || throw_exception('SMSCODE配置错误：缺少EMAILCONFIG');
        defined('EMAILFORM') || throw_exception('SMSCODE配置错误：缺少EMAILFORM');
        $emailConfig = json_decode(EMAILCONFIG, true);
        $sdk_Config = $emailConfig[EMAILSDK];
        if (empty($sdk_Config)) throw_exception('EMAILSDK配置错误');
        foreach ($sdk_Config as $v) {
            if (is_null($v) || trim($v) == '') {
                throw_exception('EMAILSDK配置错误');
            }
        }
        $this->config = $sdk_Config;
        $this->from = EMAILFORM;
        $this->initServiceObj(EMAILSDK);
    }

    /**
     * 发送模版消息
     * @param array $params 给短信平台传递的参数
     * @param $email 邮箱
     * @param $code 标识
     * @param null $uid 用户id
     * @param int $expire 失效时间
     * @return bool|mixed
     */
    public function sendTemplateEmail(array $params, $email, $code, $uid = null, $expire=86400)
    {
        $result = $this->service->sendTemplateEmail($params);
        if ($result) {
            $model = new EmailRecordModel();
            return $model->addEmailRecord($email, $code, $uid, $expire);
        }
        return false;
    }

    /**
     * 根据邮箱和标识验证是否正确，正确返回记录，错误返回false
     * @param $email
     * @param $code
     * @return string 返回json结果
     */
    public function checkByEmailAndCode($email, $code)
    {
        $model = new EmailRecordModel();
        return $model->checkByEmailAndCode($email, $code);
    }

    private function initServiceObj($sdk)
    {
        if ($sdk == 'SENDCLOUD') {
            return $this->service = new SendCloudEmailService($this->config, $this->from);
        }
    }
}