<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午2:42
 */
namespace Home\Controller;

use Home\Library\Token\Token;
use Home\Model\InitUserId\InitUserIdModel;

/**
 * 前台需继承的控制器类
 * Class BaseController
 * @package Home\Controller
 */
class BaseController extends \Think\Controller
{
    /**
     * 用户登录id
     * @var null
     */
    protected $userId = null;

    protected function _initialize()
    {
        $isSecret = I('request.x');
        $userSecret = I('server.HTTP_USERSECRET');
        if (is_null($userSecret) || trim($userSecret) == '') {
            $userSecret = cookie('userSecret');
        }
        if ($userSecret) {
            $this->initUserId($userSecret);
        }
        if ($isSecret == 'secret') {
            $timeStamp = I('server.HTTP_TIMESTAMP');
            $userToken = I('server.HTTP_TOKEN');
            $module = CONTROLLER_NAME;

            if (is_null($timeStamp) || is_null($userToken)) {
                responseJson(1000, '非法访问');
            }
            $token = new Token();
            if (!$token->checkToken($timeStamp, $userToken, $module, $userSecret)) {
                responseJson(1000, '非法访问');
            }
        }   
    }

    /*
     * 根据userSecret初始化userId
     * @param $userSecret
     */
    private function initUserId($userSecret)
    {
        if ($userSecret) {
            $intUserIdModel = new InitUserIdModel();
            $userCookie = $intUserIdModel->getUserCookieByUserSecret($userSecret);
            if (count($userCookie) > 0) {
                $expireTime = $userCookie['expire_time'];
                if ($expireTime > time()) {
                    $this->userId = $userCookie['userid'];
                }
            }
        }
    }

    /**
     * 判断用户是否登录
     * @return bool
     */
    public function isLogin()
    {
        if (!is_null($this->userId)) {
            return true;
        } else {
            return false;
        }
    }

}