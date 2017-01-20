<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/28
 * Time: 下午2:33
 */

namespace Email\Controller;


use Email\Api\EmailApi;
use Home\Controller\BaseController;

class EmailController extends BaseController
{
    /**
     * 发送模版消息
     */
    public function sendCloudTemplateEmail($email,$code)
    {
        $url ="www.register.com/index.php/Home/UserCenter/UserCenter/CheckCode";
        $template_invoke_name ="EMAIL_FIND_PASS";
        $substitution_vars = array('to' => array($email), 'sub' => array('%email%' => array($email), '%url%' => array($url . '?code=' . $code.'&email='.$email)));
        $params = array('substitution_vars' => $substitution_vars, 'subject' => null, 'template_invoke_name' => $template_invoke_name, 'sendbatchflag' => false);
        $emailApi = new EmailApi();
        $result = $emailApi->sendTemplateEmail($params,$email, $code, $this->userId);
        return $result;
    }

    /**
     * 验证标志是否正确
     */
    public function  checkCode()
    {

        if (I('request.f') == 'json') {
            if (!I('email')) responseJson(0, '请输入邮箱');
            if (!preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', I('email'))) responseJson(0, '邮箱格式不正确');
            if (!I('code')) responseJson(0, '请输入验证码');
            $emailApi = new EmailApi();
            die($emailApi->checkByEmailAndCode(I('email'), I('code')));
        }

    }
}