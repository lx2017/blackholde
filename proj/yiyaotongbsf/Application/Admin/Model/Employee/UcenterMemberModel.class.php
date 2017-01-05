<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/12
 * Time: 下午12:20
 */
namespace Admin\Model\Employee;

use Admin\Model\Employee\MemberModel;

/**
 * 员工模型类
 * Class UserCenterMemberModel
 * @package Admin\Model\Employee
 */
class UcenterMemberModel extends \Think\Model
{

    /* 用户模型自动验证 */
    protected $_validate = array(
        /* 验证用户名 */
        array('username', '2,16', -1, self::EXISTS_VALIDATE, 'length'), //用户名长度不合法
//        array('username', 'checkDenyMember', -2, self::EXISTS_VALIDATE, 'callback'), //用户名禁止注册
        array('username', '', -3, self::EXISTS_VALIDATE, 'unique'), //用户名被占用

        /* 验证密码 */
        array('password', '6,20', -4, self::EXISTS_VALIDATE, 'length'), //密码长度不合法

        /* 验证邮箱 */
        array('email', 'email', -5, self::EXISTS_VALIDATE), //邮箱格式不正确
//        array('email', '1,32', -6, self::EXISTS_VALIDATE, 'length'), //邮箱长度不合法
//        array('email', 'checkDenyEmail', -7, self::EXISTS_VALIDATE, 'callback'), //邮箱禁止注册
        array('email', '', -8, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用
    );

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('password', 'think_ucenter_md5', self::MODEL_BOTH, 'function', UC_AUTH_KEY),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME),
        array('status', 'getStatus', self::MODEL_BOTH, 'callback'),
    );

    /**
     * 通过登录名查询数量
     * @param $loginName
     * @return mixed
     */
    public function getCountByLoginName($loginName)
    {
        return $this->where(array('username' => $loginName))->count();
    }

    /**
     * 通过邮箱查询数量
     * @param $email
     * @return mixed
     */
    public function getCountByEmail($email)
    {
        return $this->where(array('email' => $email))->count();
    }

    /**
     * 添加一个新员工
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  string $email 用户邮箱
     * @return integer          注册成功-用户信息，注册失败-错误编号
     */
    public function addEmployee($username, $password, $email)
    {
        $data = array(
            'username' => $username,
            'password' => $password,
            'email' => $email
        );

        //验证手机
        if (empty($data['mobile'])) unset($data['mobile']);

        /* 添加用户 */
        $flag = $this->create($data);
        if ($flag) {
            M()->startTrans();
            $uid = $this->add();
            if ($uid > 0) {
                $user = array('uid' => $uid, 'nickname' => I('username'), 'status' => 1);
                $model = new MemberModel();
                $f = $model->add($user);
                if ($f > 0 || $f) {
                    M()->commit();
                    return true;
                } else {
                    M()->rollback();
                    return false;
                }
            } else {
                M()->rollback();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 用户登录认证
     * @param  string $username 用户名
     * @param  string $password 用户密码
     * @param  integer $type 用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1)
    {
        $map = array();
        switch ($type) {
            case 1:
                $map['username'] = $username;
                break;
            case 2:
                $map['email'] = $username;
                break;
            case 3:
                $map['mobile'] = $username;
                break;
            case 4:
                $map['id'] = $username;
                break;
            default:
                return 0; //参数错误
        }

        /* 获取用户数据 */
        $user = $this->where($map)->find();
        if (is_array($user) && $user['status']) {
            /* 验证用户密码 */
            if (think_ucenter_md5($password, UC_AUTH_KEY) === $user['password']) {
                $this->updateLogin($user['id']); //更新用户登录信息
                return $user['id']; //登录成功，返回用户ID
            } else {
                return -2; //密码错误
            }
        } else {
            return -1; //用户不存在或被禁用
        }
    }

    /**
     * 获取用户信息
     * @param  string $uid 用户ID或用户名
     * @param  boolean $is_username 是否使用用户名查询
     * @return array                用户信息
     */
    public function info($uid, $is_username = false)
    {
        $map = array();
        if ($is_username) { //通过用户名获取
            $map['username'] = $uid;
        } else {
            $map['id'] = $uid;
        }

        $user = $this->where($map)->field('id,username,email,mobile,status')->find();
        if (is_array($user) && $user['status'] = 1) {
            return array($user['id'], $user['username'], $user['email'], $user['mobile']);
        } else {
            return -1; //用户不存在或被禁用
        }
    }

    /**
     * 检测用户信息
     * @param  string $field 用户名
     * @param  integer $type 用户名类型 1-用户名，2-用户邮箱，3-用户电话
     * @return integer         错误编号
     */
    public function checkField($field, $type = 1)
    {
        $data = array();
        switch ($type) {
            case 1:
                $data['username'] = $field;
                break;
            case 2:
                $data['email'] = $field;
                break;
            case 3:
                $data['mobile'] = $field;
                break;
            default:
                return 0; //参数错误
        }

        return $this->create($data) ? 1 : $this->getError();
    }

    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid)
    {
        $data = array(
            'id' => $uid,
            'last_login_time' => NOW_TIME,
            'last_login_ip' => get_client_ip(1),
        );
        $this->save($data);
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $data 修改的字段数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateUserFields($uid, $password, $data)
    {
        if (empty($uid) || empty($password) || empty($data)) {
            $this->error = '参数错误！';
            return false;
        }

        //更新前检查用户密码
        if (!$this->verifyUser($uid, $password)) {
            $this->error = '验证出错：密码不正确！';
            return false;
        }

        //更新用户信息
        $data = $this->create($data);
        if ($data) {
            return $this->where(array('id' => $uid))->save($data);
        }
        return false;
    }

    /**
     * 验证用户密码
     * @param int $uid 用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     * @author huajie <banhuajie@163.com>
     */
    protected function verifyUser($uid, $password_in)
    {
        $password = $this->getFieldById($uid, 'password');
        if (think_ucenter_md5($password_in, UC_AUTH_KEY) === $password) {
            return true;
        }
        return false;
    }

    /**
     * 根据配置指定用户状态
     * @return integer 用户状态 1: 启用
     */
    protected function getStatus()
    {
        return 1;
    }

    /**
     * 根据id查询数量
     * @param $id
     * @return mixed
     */
    public function getCountById($id)
    {
        return $this->where(array('id', id))->count();
    }


}
