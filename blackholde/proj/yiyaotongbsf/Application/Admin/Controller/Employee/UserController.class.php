<?php

namespace Admin\Controller\Employee;

use Admin\Model\Employee\MemberModel;
use Admin\Model\Employee\UcenterMemberModel;
use UserMember\Api\UserMemberApi;
use Admin\Controller\AdminController;


/**
 * 员工管理控制器
 * Class UserController
 * @package Admin\Controller\Employee
 * @author mafengli
 */
class UserController extends AdminController
{

    /**
     * 员工管理首页
     * @author mafengli
     */
    public function index()
    {
        $nickname = I('nickname');
        $key = I('key');
        $map['status'] = array('egt', 0);
        if (is_numeric($nickname)) {
            $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
        } else {
            $map['nickname'] = array('like', '%' . (string)$nickname . '%');
        }

        $list = $this->lists(new MemberModel(), $map);
        int_to_string($list);
        $actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
        $this->assign('actions', $actionInfos);
        $this->assign('_list', $list);
        $this->display();
    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname()
    {
        $nickname = M('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname()
    {
        //获取参数
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

        //密码验证
        $User = new UserMemberApi();
        $uid = $User->login(UID, $password, 4);
        ($uid == -2) && $this->error('密码不正确');

        $Member = D('Member');
        $data = $Member->create(array('nickname' => $nickname));
        if (!$data) {
            $this->error($Member->getError());
        }

        $res = $Member->where(array('uid' => $uid))->save($data);

        if ($res) {
            $user = session('user_auth');
            $user['username'] = $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        } else {
            $this->error('修改昵称失败！');
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword()
    {
        $this->meta_title = '修改密码';
        $this->display();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword()
    {
        //获取参数
        $password = I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');

        if ($data['password'] !== $repassword) {
            $this->error('您输入的新密码与确认密码不一致');
        }

        $Api = new UserMemberApi();
        $res = $Api->updateInfo(UID, $password, $data);
        if ($res['status']) {
            $this->success('修改密码成功！');
        } else {
            $this->error($res['info']);
        }
    }

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action()
    {
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));
        $list = $this->lists($Action);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction()
    {
        $this->meta_title = '新增行为';
        $this->assign('data', null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction()
    {
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign('data', $data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction()
    {
        $res = D('Action')->update();
        if (!$res) {
            $this->error(D('Action')->getError());
        } else {
            $this->success($res['id'] ? '更新成功！' : '新增成功！', Cookie('__forward__'));
        }
    }

    /**
     * 启用员工
     * @auth mafengli
     */
    public function resumeEmp()
    {
        $id = array_unique((array)I('id', 0));
        if ($this->isAdministor($id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] = array('in', $id);
        $this->resume(new MemberModel(), $map);

    }

    /**
     * 禁用员工
     */
    public function forbidEmp()
    {
        $id = array_unique((array)I('id', 0));
        if ($this->isAdministor($id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] = array('in', $id);
        $this->forbid(new MemberModel(), $map);
    }

    /**
     * 删除员工
     */
    public function deleteEmp()
    {
        $id = array_unique((array)I('id', 0));
        if ($this->isAdministor($id)) {
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $map['uid'] = array('in', $id);
        $this->delete(new MemberModel(), $map);
    }


    /**
     * 新增员工
     * @author mafengli
     */
    public function add()
    {
        if (IS_POST) {
            /* 检测密码 */
            if (I('password') != I('repassword')) {
                $this->error('密码和重复密码不一致！');
            }

            $ucenterMemberModel = new UcenterMemberModel();
            $f = $ucenterMemberModel->addEmployee(I('username'), I('password'), I('email'));
            if ($f) { //注册成功
                $this->success('员工添加成功！', U('index', array('key' => 'EMPLOYEELIST')));
            } else { //注册失败，显示错误信息
                $this->error('注册失败');
            }
        } else {
            $this->assign('url', U('', array('key' => I('key'))));
            $this->display();
        }
    }

    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = '用户名长度必须在16个字符以内！';
                break;
            case -2:
                $error = '用户名被禁止注册！';
                break;
            case -3:
                $error = '用户名被占用！';
                break;
            case -4:
                $error = '密码长度必须在6-30个字符之间！';
                break;
            case -5:
                $error = '邮箱格式不正确！';
                break;
            case -6:
                $error = '邮箱长度必须在1-32个字符之间！';
                break;
            case -7:
                $error = '邮箱被禁止注册！';
                break;
            case -8:
                $error = '邮箱被占用！';
                break;
            case -9:
                $error = '手机格式不正确！';
                break;
            case -10:
                $error = '手机被禁止注册！';
                break;
            case -11:
                $error = '手机号被占用！';
                break;
            default:
                $error = '未知错误';
        }
        return $error;
    }

}
