<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/24
 * Time: 下午2:49
 */

namespace Sms\Model;
use Think\Model;

/**
 * 短信验证码模型类
 * Class SmsCodeModel
 * @package Sms\Model
 */
class SmsCodeModel extends  Model
{
    /*自动验证*/
    protected $_validate = array(
        array('mobile', '/^(13|14|15|18|17)[0-9]{9}/', -9, self::EXISTS_VALIDATE, 'regex')
    );
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('add_time', 'time_format', self::MODEL_INSERT, 'function')
    );

    /**
     * 写入数据库
     * @param $smsCode 短信验证码
     * @param int $expire 失效时间以秒为单位
     * @param null $uid 用户id
     * @return bool
     */
    public function addSmsCode($smsCode, $expire = 600, $uid = null)
    {
        $data = $this->create();
        if ($data) {
            $data['sms_code'] = $smsCode;
            $data['user_id'] = $uid;
            $data['expire_time'] = time() + $expire;
            return $this->add($data);
        } else {
            return false;
        }
    }

    /**
     * 根据mobile、code检测验证码是否正确
     * @param $mobile
     * @param $code
     * @return bool
     */
    public function checkCodeByMobileAndCode($mobile, $code)
    {
        $expire = $this->where(array('mobile' => $mobile, 'sms_code' => $code))->getField('expire_time');
        if ($expire) {
            if ($expire < time()) {
                $this->error = '验证码已经失效';
                return false;
            }
            return true;
        } else {
            $this->error = '验证码不正确';
            return false;
        }
    }
}