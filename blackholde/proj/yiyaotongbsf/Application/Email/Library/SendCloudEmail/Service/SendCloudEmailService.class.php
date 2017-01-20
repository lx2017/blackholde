<?php

/**
 * 处理sendcloud业务
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/4/7
 * Time: 上午10:13
 */

namespace Email\Library\SendCloudEmail\Service;

use Email\Library\BaseService;
use Common\Library\HttpUtil;
use Email\Library\SendCloudEmail\Entity\Template_mail;

class SendCloudEmailService implements BaseService
{
    private $apiUser;
    private $apiKey;
    private $templateUrl;
    private $apiBatchUser;
    private $from;

    function __construct(array $config, $emailform)
    {

        $this->apiUser = $config['EMAIL_APP_USER'];
        $this->apiKey = $config['EMAIL_APP_KEY'];
        $this->templateUrl = $config['EMAIL_TEMPLATE_URL'];
        $this->apiBatchUser = $config['EMAIL_APP_BATCH_USER'];
        $this->from = $emailform;
        if (empty($this->apiUser) || empty($this->apiBatchUser) || empty($this->templateUrl) || empty($this->apiKey) || empty($this->from)) {
            throw new Exception('arguments has null');
        }
    }

    /**
     * 发送模版消息
     * @param array $params 键值：substitution_vars arr 模板替换变量array('to' => array($email), 'sub' => array('%email%' => array($email), '%url%' => array($url)))
     * subject string 标题 template_invoke_name string 模版名称  sendbatchflag bool  是否用batchuser发送
     * @return bool  true发送成功，false发送失败
     * @throws Exception
     */
    public function sendTemplateEmail(array $params)
    {
        if ($params && count($params > 0)) {
            if (!array_key_exists('substitution_vars', $params) || !array_key_exists('template_invoke_name', $params)) {
                throw new Exception('params is error');
            }
            $substitution_vars = $params['substitution_vars'];
            $sendbatchflag = isset($params['sendbatchflag']) ? $params['sendbatchflag'] : false;
            $subject = isset($params['subject']) ? $params['subject'] : null;
            $template_invoke_name = $params['template_invoke_name'];
            if (count($substitution_vars) == 0 || is_null($template_invoke_name)) {
                throw new Exception('params is error');
            }
            $json = json_encode($substitution_vars);
            $apiuser = $this->apiUser;
            if ($sendbatchflag) {
                $apiuser = $this->apiBatchUser;
            }
            $tem = new Template_mail($apiuser, $this->apiKey, $this->from, $json, $subject, $template_invoke_name);
            $data = http_build_query($tem->getArray());
            $http = new HttpUtil();
            $json = $http->https_request($this->templateUrl, $data);
            $jsonObj = json_decode($json);
            if ($jsonObj->message == 'success') {
                return true;
            } else {
                return false;
            }
        }
        return false;

    }
}