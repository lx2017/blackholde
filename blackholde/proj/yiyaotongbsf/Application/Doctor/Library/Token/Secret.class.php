<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/5/18
 * Time: 下午4:42
 */
namespace Doctor\Library\Token;
class Secret
{
    private $secretStr;
    private $createTime;


    /**
     *加载secret.xml文件，解析出secret
     * @throws Exception
     */
    public function __construct()
    {
        $secretPath = APP_PATH . HOME_PATH_NAME . 'Conf/secret.xml';
        if (file_exists($secretPath)) {
            $doc = new \DOMDocument();
            $doc->load($secretPath);
            $secretObj = $doc->getElementsByTagName("secret");
            if ($secretObj) {
                $this->secretStr = $secretObj->item(0)->nodeValue;
            } else {
                log_message('error', 'error : secret does not exist');
                throw new Exception('secret does not exist');
            }
            $createTimeObj = $doc->getElementsByTagName("createtime");
            if ($createTimeObj) {
                $this->createTime = $createTimeObj->item(0)->nodeValue;
            }
        } else {
            log_message('error', 'error : secret xml is not exist');
            throw new Exception('secret xml is not exist');
        }

    }

    /**
     * @return mixed
     */
    public function getSecretStr()
    {
        return $this->secretStr;
    }

    /**
     * @return string
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


}