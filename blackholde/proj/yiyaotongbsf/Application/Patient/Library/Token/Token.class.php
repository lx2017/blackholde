<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/5/18
 * Time: 下午5:10
 */
namespace Patient\Library\Token;

use Patient\Library\Token\Secret;

Class Token
{
    private $secret;

    public function __construct()
    {
        $this->secret = new Secret();
    }

    /**
     * 检测token是否正确
     * @param $timeStamp 时间戳
     * @param $token 加密串
     * @param $module  模块名称
     * @param string $userSecret
     * @return bool true检测通过；false检测不通过
     */
    public function checkToken($timeStamp, $token, $module, $userSecret = '')
    {
        $secret = $this->secret->getSecretStr();
        if ($secret) {
            $arr = array((int)$timeStamp, $userSecret, strtolower($module), $secret);
            sort($arr);
            $temp = md5(implode('', $arr));
            if ($temp == $token) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}