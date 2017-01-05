<?php


/**
 * 模版消息类
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/3/9
 * Time: 下午4:21
 */
namespace Email\Library\SendCloudEmail\Entity;
class Template_mail
{
    //string 	是 	API_USER
    private $api_user;
    //string 	是 	API_KEY
    private $api_key;
    //string 	是 	发件人地址. from 和发信域名, 会影响是否显示代发
    private $from;
    //string 	* 	模板替换变量. 在 use_maillist=false 时使用, 如: {"to": ["ben@ifaxin.com", "joe@ifaxin.com"],"sub":{"%name%": ["Ben", "Joe"],"%money%":[288, 497]}}
    private $substitution_vars;
    //string 	* 	收件人的地址列表. 在 use_maillist=true 时使用
    private $to;
    //string 	否 	邮件标题
    private $subject;
    //string 	是 	邮件模板调用名称
    private $template_invoke_name;
    //string 	否 	发件人名称. 显示如: ifaxin客服支持 <support@ifaxin.com>
    private $fromname;
    //string 	否 	默认的回复邮件地址. 如果 replyto 没有或者为空, 则默认的回复邮件地址为 from
    private $replyto;
    //int 	否 	本次发送所使用的标签ID. 此标签需要事先创建
    private $label;
    //string 	否 	邮件头部信息. JSON 格式, 比如:{"header1": "value1", "header2": "value2"}
    private $headers;
    //string 	否 	邮件附件. 发送附件时, 必须使用 multipart/form-data 进行 post 提交 (表单提交)
    private $files;
    //string (true, false) 	否 	是否返回 emailId. 有多个收件人时, 会返回 emailId 的列表
    private $resp_email_id;
    //string (true, false) 	否 	参数 to 是否支持地址列表, 默认为 false. 比如: to=users@maillist.sendcloud.org
    private $use_maillist;
    //string (true, false) 	否 	邮件内容是否使用gzip压缩. 默认不使用 gzip 压缩正文
    private $gzip_compress;

    function __construct($api_user, $api_key, $from, $substitution_vars, $subject, $template_invoke_name)
    {
        $this->api_user = $api_user;
        $this->api_key = $api_key;
        $this->from = $from;
        $this->substitution_vars = $substitution_vars;
        $this->subject = $subject;
        $this->template_invoke_name = $template_invoke_name;
    }


    /**
     * @return mixed
     */
    public function getApiUser()
    {
        return $this->api_user;
    }

    /**
     * @param mixed $api_user
     */
    public function setApiUser($api_user)
    {
        $this->api_user = $api_user;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param mixed $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getSubstitutionVars()
    {
        return $this->substitution_vars;
    }

    /**
     * @param mixed $substitution_vars
     */
    public function setSubstitutionVars($substitution_vars)
    {
        $this->substitution_vars = $substitution_vars;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getTemplateInvokeName()
    {
        return $this->template_invoke_name;
    }

    /**
     * @param mixed $template_invoke_name
     */
    public function setTemplateInvokeName($template_invoke_name)
    {
        $this->template_invoke_name = $template_invoke_name;
    }

    /**
     * @return mixed
     */
    public function getFromname()
    {
        return $this->fromname;
    }

    /**
     * @param mixed $fromname
     */
    public function setFromname($fromname)
    {
        $this->fromname = $fromname;
    }

    /**
     * @return mixed
     */
    public function getReplyto()
    {
        return $this->replyto;
    }

    /**
     * @param mixed $replyto
     */
    public function setReplyto($replyto)
    {
        $this->replyto = $replyto;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param mixed $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getRespEmailId()
    {
        return $this->resp_email_id;
    }

    /**
     * @param mixed $resp_email_id
     */
    public function setRespEmailId($resp_email_id)
    {
        $this->resp_email_id = $resp_email_id;
    }

    /**
     * @return mixed
     */
    public function getUseMaillist()
    {
        return $this->use_maillist;
    }

    /**
     * @param mixed $use_maillist
     */
    public function setUseMaillist($use_maillist)
    {
        $this->use_maillist = $use_maillist;
    }

    /**
     * @return mixed
     */
    public function getGzipCompress()
    {
        return $this->gzip_compress;
    }

    /**
     * @param mixed $gzip_compress
     */
    public function setGzipCompress($gzip_compress)
    {
        $this->gzip_compress = $gzip_compress;
    }

    public function getArray()
    {
        $arr = array(
            'api_user' => $this->api_user,
            'api_key' => $this->api_key,
            'from' => $this->from,
            'substitution_vars' => $this->substitution_vars,
            'template_invoke_name' => $this->template_invoke_name
        );
        if ($this->subject) {
            $arr['subject'] = $this->subject;
        }
        return $arr;
    }
}