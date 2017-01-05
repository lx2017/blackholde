<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/28
 * Time: 下午2:33
 */
//emial_sdk为email_config的键
define('EMAILSDK', 'SENDCLOUD');

//email发送邮箱
define('EMAILFORM', 'service@iymatou.com');
define('EMAILCONFIG', json_encode(array(
        /***
         * EMAIL_APP_USER:触发类型的APP_USER
         * EMAIL_APP_KEY:APP_KEY
         * EMAIL_APP_BATCH_USER:批量类型的APP_USER
         * EMAIL_TEMPLATE_URL:模版发送地址
         */
        'SENDCLOUD' => array(
            'EMAIL_APP_USER' => 'yuanmatou1',
            'EMAIL_APP_KEY' => 'TC40YSoftRIepiug',
            'EMAIL_APP_BATCH_USER' => 'yuanmatou',
            'EMAIL_TEMPLATE_URL' => 'https://sendcloud.sohu.com/webapi/mail.send_template.json'
        ))
));
