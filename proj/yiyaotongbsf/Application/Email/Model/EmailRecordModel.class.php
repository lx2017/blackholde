<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/4/20
 * Time: 上午9:25
 */
namespace Email\Model;

use Think\Model;


class EmailRecordModel extends Model
{
    /*自动验证*/
    protected $_validate = array(
        array('email', '/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', -9, self::EXISTS_VALIDATE, 'regex')
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('add_time', 'time_format', self::MODEL_INSERT, 'function')
    );

    /**
     * 添加发送邮件记录
     * @param $email 发送邮件
     * @param $code 标识
     * @param $userId 用户id
     * @param int $expire 失效时间
     * @return bool|mixed
     */
    public function addEmailRecord($email, $code, $userId = null, $expire = 86400)
    {
        $data = $this->create();
        if ($data) {
            $data['email'] = $email;
            $data['code'] = $code;
            $data['user_id'] = $userId;
            $time = time();
            $data['expire_time'] = $time+$expire;
            return $this->add($data);
        } else {
            return false;
        }
    }

    /**
     * 根据email和code查询是否正确
     * @param $email
     * @param $code
     * @return string json字符串
     */
    public function checkByEmailAndCode($email, $code)
    {
        $emailRecord = $this->where(array('email' => $email, 'code' => $code))->find();
        if (empty($emailRecord)) {
            return getJson(0, '验证失败');
        }
        $time = time();
        if ($emailRecord['expire_time'] < $time) {
            return getJson(0, '已失效');
        }
        return getJson(1, '成功', array('user_id' => $emailRecord['user_id']));
    }

}