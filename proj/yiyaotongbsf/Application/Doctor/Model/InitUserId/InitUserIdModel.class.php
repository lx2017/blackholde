<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午3:03
 */
namespace Doctor\Model\InitUserId;

use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Doctor\Model\InitUserId
 */
class InitUserIdModel extends Model
{
    protected $tableName = "user_cookie";

    /**
     * 根据newid判断用户是否登录
     * @param $userSecret 对应数据库的newid
     * @return mixed
     */
    public function getUserCookieByUserSecret($userSecret)
    {

        return $this->where(array('newid' => $userSecret, 'user_type' => 0))->find();
    }
}