<?php

namespace Admin\Controller\Employee;

use Admin\Model\AuthRuleModel;
use Admin\Model\Employee\AuthGroupAccessModel;
use Admin\Model\Employee\AuthGroupModel;
use Admin\Controller\AdminController;
use Admin\Model\Employee\AuthGroupModuleModel;
use Admin\Model\Employee\MemberModel;
use Admin\Model\Employee\ModuleModel;
use Admin\Model\Employee\UcenterMemberModel;

/**
 * 权限管理控制器
 * Class AuthManagerController
 * @package Admin\Controller\Employee
 */
class AuthManagerController extends AdminController
{

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function updateRules()
    {
        //需要新增的节点必然位于$nodes
        $nodes = $this->returnNodes(false);

        $AuthRule = M('AuthRule');
        $map = array('module' => 'admin', 'type' => array('in', '1,2'));//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            if ($value['pid'] > 0) {
                $temp['type'] = AuthRuleModel::RULE_URL;
            } else {
                $temp['type'] = AuthRuleModel::RULE_MAIN;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids = array();//保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            if (isset($data[$key])) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']] = $rule;
            } elseif ($rule['status'] == 1) {
                $ids[] = $rule['id'];
            }
        }
        if (count($update)) {
            foreach ($update as $k => $row) {
                if ($row != $diff[$row['id']]) {
                    $AuthRule->where(array('id' => $row['id']))->save($row);
                }
            }
        }
        if (count($ids)) {
            $AuthRule->where(array('id' => array('IN', implode(',', $ids))))->save(array('status' => -1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if (count($data)) {
            $AuthRule->addAll(array_values($data));
        }
        if ($AuthRule->getDbError()) {
            trace('[' . __METHOD__ . ']:' . $AuthRule->getDbError());
            return false;
        } else {
            return true;
        }
    }


    /**
     * 用户组列表
     */
    public function index()
    {
        $key = I('key');
        $list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
        $list = int_to_string($list);
        $actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
        $this->assign('actions', $actionInfos);
        $this->assign('_list', $list);
        $this->assign('_use_tip', true);
        $this->display();
    }

    /**
     * 创建用户组信息
     * @author mafengli
     */
    public function createGroup()
    {
        if (IS_POST) {
            $_POST['module'] = 'admin';
            $_POST['type'] = AuthGroupModel::TYPE_ADMIN;
            $authoGroupModel = new AuthGroupModel();
            $data = $authoGroupModel->create();
            if ($data) {
                $r = $authoGroupModel->add();
                if ($r === false) {
                    $this->error('操作失败:' . $authoGroupModel->getError());
                } else {
                    $this->success('操作成功!', U('index', array('key' => 'AUTHMANAGER')));
                }
            } else {
                $this->error('操作失败:' . $authoGroupModel->getError());
            }
        } else {
            $this->assign('url', U('', array('key' => I('key'))));
            $this->display();
        }
    }

    /**
     * 编辑员工组信息
     * @author mafengli
     */
    public function editGroup()
    {
        if (IS_POST) {
            $authoGroupModel = new AuthGroupModel();
            $data['title'] = I('title');
            $data['description'] = I('description');
            $groupId = (int)I('request.id');
            $data['id'] = $groupId;
            if ($data) {
                $r = $authoGroupModel->save($data);
                if ($r === false) {
                    $this->error('操作失败:' . $authoGroupModel->getError());
                } else {
                    $this->success('操作成功!', U('index', array('key' => 'AUTHMANAGER')));
                }
            } else {
                $this->error('操作失败:' . $authoGroupModel->getError());
            }
        } else {
            $groupId = (int)I('id');
            $authoGroupModel = new AuthGroupModel();
            $authGroup = $authoGroupModel->getGroupById($groupId);
            $this->assign('auth_group', $authGroup);
            $this->display();
        }


    }


    /**
     * 访问授权页面
     * @author  mafengli
     */
    public function access()
    {
        if (IS_POST) {
            $groupId = (int)I('request.group_id');
            if ($this->isAdminGroup($groupId)) {
                $this->error('操作失败:不允许对超级管理员组进行操作');
            } else {
                $rules = I('rules');
                $authGroupModuleModel = new AuthGroupModuleModel();
                $flag = $authGroupModuleModel->updateAuthGroupMuduleByGroupId($rules, $groupId);
                if ($flag) {
                    $this->success('操作成功');
                } else {
                    $this->error('操作失败:数据库操作失败');
                }
            }


        } else {
            $this->goAccessPage();
        }

    }

    private function goAccessPage()
    {
        /*查询所有模块*/
        $moduleModel = new ModuleModel();
        $list = $moduleModel->where(array('hide' => 0))->field('key,pid,title,url,tip,hide')->order('sort asc')->select();
        /*查询改组所包括的模块*/
        $authGroupModuleModel = new AuthGroupModuleModel();
        $authGroupModules = $authGroupModuleModel->getAllByGroupId(I('group_id'));
        if (!empty($authGroupModules)) {
            for ($i = 0; $i < count($list); $i++) {
                foreach ($authGroupModules as $a) {
                    if ($list[$i]['key'] == $a['module_key']) {
                        $list[$i]['checked'] = 'checked';
                        break;
                    }
                }
            }
        }
        $nodes = list_to_tree($list, 'key', 'pid', 'child', '0');
        /*查询改页面的操作*/
        $actions = $this->getMenuActions('AUTHMANAGER');
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }

        /*查询所有用户组*/
        $authGroupModel = new AuthGroupModel();
        $authGroups = $authGroupModel->getAllByField('id,title');
        $this->assign('node_list', $nodes);
        $this->assign('actions', $actionInfos);
        $this->assign('authGroups', $authGroups);
        $this->display();
    }

    /**
     * 删除用户组
     * @auth mafengli
     */
    public function deleteGroup()
    {
        $id = array_unique((array)I('id', 0));
        if ($this->isAdminGroup($id)) {
            $this->error("不允许对管理员组执行该操作!");
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }
        $authGroupModel = new AuthGroupModel();
        if ($authGroupModel->deleteByGroupIds($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 用户组授权用户列表
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function user()
    {
        if (IS_POST) {

        } else {
            $groupId = (int)I('group_id');
            if (empty($groupId)) {
                $this->error('参数错误');
            }

            $prefix = C('DB_PREFIX');
            $l_table = $prefix . (AuthGroupModel::MEMBER);
            $r_table = $prefix . (AuthGroupModel::AUTH_GROUP_ACCESS);
            $model = M()->table($l_table . ' m')->join($r_table . ' a ON m.uid=a.uid');
            $_REQUEST = array();
            $list = $this->lists($model, array('a.group_id' => $groupId, 'm.status' => array('egt', 0)), 'm.uid asc', null, 'm.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status');
            int_to_string($list);

            /*查询所有用户组*/
            $authGroupModel = new AuthGroupModel();
            $authGroups = $authGroupModel->getAllByField('id,title');

            /*查询改页面的操作*/
            $actions = $this->getMenuActions('AUTHMANAGER');
            foreach ($actions as $action) {
                $actionInfos[$action['key']] = $action;
            }

            $this->assign('actions', $actionInfos);
            $this->assign('_list', $list);
            $this->assign('authGroups', $authGroups);
            $this->display();
        }

    }

    /**
     * 将分类添加到用户组的编辑页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function category()
    {
        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
            ->getfield('id,id,title,rules');
        $group_list = D('Category')->getTree();
        $authed_group = AuthGroupModel::getCategoryOfGroup(I('group_id'));
        $this->assign('authed_group', implode(',', (array)$authed_group));
        $this->assign('group_list', $group_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->meta_title = '分类授权';
        $this->display();
    }

    public function tree($tree = null)
    {
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function group()
    {
        $uid = I('uid');
        $auth_groups = D('AuthGroup')->getGroups();
        $user_groups = AuthGroupModel::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value) {
            $ids[] = $value['group_id'];
        }
        $nickname = D('Member')->getNickName($uid);
        $this->assign('nickname', $nickname);
        $this->assign('auth_groups', $auth_groups);
        $this->assign('user_groups', implode(',', $ids));
        $this->display();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author mafengli
     */
    public function addToGroup()
    {
        $uid = I('uid');
        $gid = I('request.group_id');
        if (empty($uid)) {
            $this->error('参数有误');
        }
        $uids = explode(',', $uid);
        foreach ($uids as $u) {
            if (is_administrator($u)) {
                $this->error('存在超级管理员的id');
            }
            if (!is_numeric($u)) {
                $this->error('参数有误');
            }
            $ucenterMemeberModel = new UcenterMemberModel();
            $count = $ucenterMemeberModel->getCountById($u);
            if ($count == 0) {
                $this->error('存在非法员工的uid');
            }
        }
        $authGroupModel = new AuthGroupModel();
        if ($gid && !$authGroupModel->checkGroupId($gid)) {
            $this->error($authGroupModel->error);
        }
        $authGroupAccessModel = new AuthGroupAccessModel();
        if ($authGroupAccessModel->addToGroup($uids, $gid)) {
            $this->success('操作成功');
        } else {
            $this->error($authGroupAccessModel->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function removeFromGroup()
    {
        $uid = I('uid');
        $gid = I('group_id');
        if ($uid == UID) {
            $this->error('不允许解除自身授权');
        }
        if (empty($uid) || empty($gid)) {
            $this->error('参数有误');
        }
        $authGroupModel = new AuthGroupModel();
        if (!$authGroupModel->checkGroupId($gid)) {
            $this->error('员工组不存在');
        }
        $authGroupAccessModel = new AuthGroupAccessModel();
        if ($authGroupAccessModel->removeFromGroup($uid, $gid)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 将分类添加到用户组  入参:cid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function addToCategory()
    {
        $cid = I('cid');
        $gid = I('group_id');
        if (empty($gid)) {
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error('用户组不存在');
        }
        if ($cid && !$AuthGroup->checkCategoryId($cid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToCategory($gid, $cid)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    /**
     * 将模型添加到用户组  入参:mid,group_id
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    public function addToModel()
    {
        $mid = I('id');
        $gid = I('get.group_id');
        if (empty($gid)) {
            $this->error('参数有误');
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error('用户组不存在');
        }
        if ($mid && !$AuthGroup->checkModelId($mid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToModel($gid, $mid)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

}
