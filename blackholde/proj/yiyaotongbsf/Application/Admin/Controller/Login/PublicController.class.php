<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/6
 * Time: 下午2:39
 */
namespace Admin\Controller\Login;

use Think\Controller;
use Admin\Model\Login\LoginMemberModel;
use Admin\Model\Employee\UcenterMemberModel;
use Admin\Model\Login\ConfigModel;

class PublicController extends Controller
{
    public function index()
    {
        if (UID) {
            $this->meta_title = '管理首页';
            $this->display();
        } else {
            $this->redirect('Login/Public/login');
        }
    }

    /**
     * 后台用户登录
     * @author mafengli <820471571@qq.com>
     */
    public function login($username = null, $password = null, $verify = null)
    {

        if (IS_POST) {
            /* 检测验证码 TODO: */
//             if (!check_verify($verify)) {
//                 $this->error('验证码输入错误！');
//             }

            /* 调用UC登录接口登录 */
            $User = new UcenterMemberModel();

			
			$uid = $User->login($username, $password);
            if (0 < $uid) { //UC登录成功
                /* 登录用户 */
                $loginMemberModel = new LoginMemberModel();
                if ($loginMemberModel->login($uid)) { //登录用户
                    //TODO:跳转到登录前页面
                    $this->success('登录成功！', U(ADMIN_PATH_NAME . 'Main/index/main'));
                } else {
                    $this->error($loginMemberModel->getError());
                }

            } else { //登录失败
                switch ($uid) {
                    case -1:
                        $error = '用户不存在或被禁用！';
                        break; //系统级别禁用
                    case -2:
                        $error = '密码错误！';
                        break;
                    default:
                        $error = '未知错误！';
                        break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else {
            if (is_login()) {
                $this->redirect(ADMIN_PATH_NAME . 'Main/Index/main');
            } else {
                /* 读取数据库中的配置 */
                $config = S('DB_CONFIG_DATA');
                if (!$config) {
                    $configModel = new ConfigModel();
                    $config = $configModel->lists();
                    /*加入缓存*/
                    S('DB_CONFIG_DATA', $config);
                }
                C($config); //添加配置

                $this->display();
            }
        }

    }

    /**
     * 退出登录
     * @author ali
     * @date 2016.09.18
     */
    public function logout()
    {
        if (is_login()) {
            session('user_auth', null);
            session('user_auth_sign', null);
            session('[destroy]');
            $this->success('退出成功！', U('login'));
        } else {
            $this->redirect('login');
        }
    }

    public function verify()
    {
        ob_clean();
        $verify = new \Think\Verify();
        $verify->entry(1);
    }

    /**
     * 欢迎窗口
     */
    public function windows()
    {
        if (UID) {
            $this->display();
        } else {
            $this->redirect(ADMIN_PATH_NAME . '/Login/Public/login');
        }
    }


}