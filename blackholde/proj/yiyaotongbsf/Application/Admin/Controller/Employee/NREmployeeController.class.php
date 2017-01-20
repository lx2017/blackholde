<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/12
 * Time: 上午11:58
 */
namespace Admin\Controller\Employee;

use Admin\Model\Employee\UcenterMemberModel;

class NREmployeeController extends \Think\Controller
{
    /**
     * 检测登录名是否存在
     */
    public function checkLoginName()
    {
        $login_name = I('username');
        if ($login_name) {
            $memberModel = new UcenterMemberModel();
            $count = $memberModel->getCountByLoginName($login_name);
            if ($count == 0) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
    }

    public function checkEmail()
    {
        $email = I('email');
        if ($email) {
            $memberModel = new UcenterMemberModel();
            $count = $memberModel->getCountByEmail($email);
            if ($count == 0) {
                echo json_encode(true);
            } else {
                echo json_encode(false);
            }
        } else {
            echo json_encode(false);
        }
    }
}