<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/24
 * Time: 下午2:28
 */
//sms_sdk为sms_config的键
define('SMSCODETEMPLATE', '75606');
define('SMSSDK', 'YUNTONGXUN');
define('SMSCONFIG', json_encode(array(
    /***
     * SMS_ACCOUNT_SID:容联云通讯平台提供的ACCOUNT SID
     * SMS_ACCOUNT_TOKEN:容联云通讯平台提供的AUTH TOKEN
     * SMS_APP_ID:容联云通讯平台提供的APP ID
     * SMS_SEVER_IP:容联云通讯平台提供的接口地址
     * SMS_SEVER_PORT:容联云通讯平台的接口的端口号
     * SMS_SOFT_VERSION:容联云通讯平台提供的版本号
     */
    'YUNTONGXUN' => array(
        'SMS_ACCOUNT_SID' => 'aaf98f895350b68801535e9bb8f21779',
        'SMS_ACCOUNT_TOKEN' => '4c1fe1f023274ecbaa652415c85f6862',
        'SMS_APP_ID' => '8a48b5515350d1e201535e9d43061735',
        'SMS_SEVER_IP' => 'sandboxapp.cloopen.com',
        'SMS_SEVER_PORT' => '8883',
        'SMS_SOFT_VERSION' => '2013-12-26')

))
);