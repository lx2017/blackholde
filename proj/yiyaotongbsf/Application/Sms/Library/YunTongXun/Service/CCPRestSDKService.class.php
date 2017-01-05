<?php

/*
 *  Copyright (c) 2014 The CCP project authors. All Rights Reserved.
 *
 *  Use of this source code is governed by a Beijing Speedtong Information Technology Co.,Ltd license
 *  that can be found in the LICENSE file in the root of the web site.
 *
 *   http://www.yuntongxun.com
 *
 *  An additional intellectual property rights grant can be found
 *  in the file PATENTS.  All contributing project authors may
 *  be found in the AUTHORS file in the root of the source tree.
 */
namespace Sms\Library\YunTongXun\Service;
use Common\Library\HttpUtil;
use Sms\Library\BaseService;
class CCPRestSDKService implements BaseService
{
    
    private $AccountSid;
    private $AccountToken;
    private $AppId;
    private $ServerIP;
    private $ServerPort;
    private $SoftVersion;
    private $Batch;  //时间sh
    private $BodyType = "xml";//包体格式，可填值：json 、xml

    function __construct(array $config)
    {
        $this->Batch = date("YmdHis");
        if (!array_key_exists('SMS_ACCOUNT_SID', $config) || !array_key_exists('SMS_ACCOUNT_TOKEN', $config) || !array_key_exists('SMS_APP_ID', $config)
            || !array_key_exists('SMS_SEVER_IP', $config) || !array_key_exists('SMS_SEVER_IP', $config) || !array_key_exists('SMS_SEVER_PORT', $config)
            || !array_key_exists('SMS_SOFT_VERSION', $config)
        ) {
            throw new Exception('arguments has null');
        }
        $this->ServerIP = $config['SMS_SEVER_IP'];
        $this->ServerPort = $config['SMS_SEVER_PORT'];
        $this->SoftVersion = $config['SMS_SOFT_VERSION'];
        $this->AccountSid = $config['SMS_ACCOUNT_SID'];
        $this->AccountToken = $config['SMS_ACCOUNT_TOKEN'];
        $this->AppId = $config['SMS_APP_ID'];
    }

    /**
     * @param array $params 发送的模版数据
     * @return mixed|SimpleXMLElement|stdClass
     * @throws Exception
     */
    function sendTemplateSMS(array $params)
    {
        if (!array_key_exists('to', $params) || !array_key_exists('datas', $params) || !array_key_exists('tempId', $params)) {
            throw new Exception('params has null');
        }
        $to = $params['to'];
        $datas = $params['datas'];
        $tempId = $params['tempId'];
        if (is_null($to) || is_null($datas) || is_null($tempId)) {
            throw new Exception('params is error');
        }
        //主帐号鉴权信息验证，对必选参数进行判空。
        $auth = $this->accAuth();
        if ($auth != "") {
            return $auth;
        }
        // 拼接请求包体
        if ($this->BodyType == "json") {
            $data = "";
            for ($i = 0; $i < count($datas); $i++) {
                $data = $data . "'" . $datas[$i] . "',";
            }
            $body = "{'to':'$to','templateId':'$tempId','appId':'$this->AppId','datas':[" . $data . "]}";
        } else {
            $data = "";
            for ($i = 0; $i < count($datas); $i++) {
                $data = $data . "<data>" . $datas[$i] . "</data>";
            }
            $body = "<TemplateSMS>
                    <to>$to</to> 
                    <appId>$this->AppId</appId>
                    <templateId>$tempId</templateId>
                    <datas>" . $data . "</datas>
                  </TemplateSMS>";
        }
        // 大写的sig参数
        $sig = strtoupper(md5($this->AccountSid . $this->AccountToken . $this->Batch));
        // 生成请求URL        
        $url = "https://$this->ServerIP:$this->ServerPort/$this->SoftVersion/Accounts/$this->AccountSid/SMS/TemplateSMS?sig=$sig";
        // 生成授权：主帐户Id + 英文冒号 + 时间戳。
        $authen = base64_encode($this->AccountSid . ":" . $this->Batch);
        // 生成包头  
        $header = array("Accept:application/$this->BodyType", "Content-Type:application/$this->BodyType;charset=utf-8", "Authorization:$authen");
        // 发送请求
        $httpUtil = new HttpUtil();
        $result = $httpUtil->https_request($url, $body, $header);
        if ($this->BodyType == "json") {//JSON格式
            $datas = json_decode($result);
        } else { //xml格式
            $datas = simplexml_load_string(trim($result, " \t\n\r"));
        }
        //  if($datas == FALSE){
//            $datas = new stdClass();
//            $datas->statusCode = '172003';
//            $datas->statusMsg = '返回包体错误'; 
//        }
        //重新装填数据
        if ($datas->statusCode == 0) {
            if ($this->BodyType == "json") {
                $datas->TemplateSMS = $datas->templateSMS;
                unset($datas->templateSMS);
            }
        }

        return $datas;
    }

    /**
     * 主帐号鉴权
     */
    function accAuth()
    {
        if ($this->ServerIP == "") {
            $data = new stdClass();
            $data->statusCode = '172004';
            $data->statusMsg = 'IP为空';
            return $data;
        }
        if ($this->ServerPort <= 0) {
            $data = new stdClass();
            $data->statusCode = '172005';
            $data->statusMsg = '端口错误（小于等于0）';
            return $data;
        }
        if ($this->SoftVersion == "") {
            $data = new stdClass();
            $data->statusCode = '172013';
            $data->statusMsg = '版本号为空';
            return $data;
        }
        if ($this->AccountSid == "") {
            $data = new stdClass();
            $data->statusCode = '172006';
            $data->statusMsg = '主帐号为空';
            return $data;
        }
        if ($this->AccountToken == "") {
            $data = new stdClass();
            $data->statusCode = '172007';
            $data->statusMsg = '主帐号令牌为空';
            return $data;
        }
        if ($this->AppId == "") {
            $data = new stdClass();
            $data->statusCode = '172012';
            $data->statusMsg = '应用ID为空';
            return $data;
        }
    }
}

