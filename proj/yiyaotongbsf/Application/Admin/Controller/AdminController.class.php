<?php

namespace Admin\Controller;

use Think\Controller;
use Admin\Model\AuthRuleModel;
use Admin\Model\Employee\ModuleModel;
use Admin\Model\Employee\AuthGroupModuleModel;
use Admin\Model\Employee\AuthGroupAccessModel;

/**
 * 后台首页控制器
 * @author mafengli <820471571@qq.com>
 */
class AdminController extends Controller
{
    /**
     * 后台控制器初始化
     */
    protected function _initialize()
    {
        // 获取当前用户ID
        define('UID', 1);
        if (!UID) {// 还没登录 跳转到登录页面
            $this->redirect(ADMIN_PATH_NAME . 'Login/Public/login');
        }
        /* 读取数据库中的配置 */
        $config = S('DB_CONFIG_DATA');
        if (!$config) {
            $config = api('Config/lists');
            S('DB_CONFIG_DATA', $config);
        }
        C($config); //添加配置
        //判断是否设置IP拦截
        if (C('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if (!in_array(get_client_ip(), explode(',', C('ADMIN_ALLOW_IP')))) {
                $this->error('403:禁止访问');
            }
        }
        // 检测访问权限
        $access = $this->accessControl();

        if ($access === false) {
            $this->error('403:禁止访问');
        } elseif ($access === null) {
            $key = I('request.key');
            if (!$this->isExe($key)) {
                $this->error('您没有权限执行该操作');
            }

        }

    }


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl()
    {
        $allow = C('ALLOW_VISIT');
        $deny = C('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false;//非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param Model $model 模型名称,供M函数使用的参数
     * @param array $data 修改的数据
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    final protected function editRow($model, $data, $where, $msg)
    {
        $id = array_unique((array)I('id', 0));
        $id = is_array($id) ? implode(',', $id) : $id;
        $where = array_merge(array('id' => array('in', $id)), (array)$where);
        $msg = array_merge(array('success' => '操作成功！', 'error' => '操作失败！', 'url' => '', 'ajax' => IS_AJAX), (array)$msg);
        if ($model->where($where)->save($data) !== false) {
            $this->success($msg['success'], $msg['url'], $msg['ajax']);
        } else {
            $this->error($msg['error'], $msg['url'], $msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param Model $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的 where()方法的参数
     * @param array $msg 执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function forbid($model, $where = array(), $msg = array('success' => '状态禁用成功！', 'error' => '状态禁用失败！'))
    {
        $data = array('status' => 0);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function resume($model, $where = array(), $msg = array('success' => '状态恢复成功！', 'error' => '状态恢复失败！'))
    {
        $data = array('status' => 1);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author huajie  <banhuajie@163.com>
     */
    protected function restore($model, $where = array(), $msg = array('success' => '状态还原成功！', 'error' => '状态还原失败！'))
    {
        $data = array('status' => 1);
        $where = array_merge(array('status' => -1), $where);
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array $where 查询时的where()方法的参数
     * @param array $msg 执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function delete($model, $where = array(), $msg = array('success' => '删除成功！', 'error' => '删除失败！'))
    {
        $data['status'] = -1;
        $data['update_time'] = NOW_TIME;
        $this->editRow($model, $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model = CONTROLLER_NAME)
    {

        $ids = I('request.ids');
        $status = I('request.status');
        if (empty($ids)) {
            $this->error('请选择要操作的数据');
        }

        $map['id'] = array('in', $ids);
        switch ($status) {
            case -1 :
                $this->delete($Model, $map, array('success' => '删除成功', 'error' => '删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success' => '禁用成功', 'error' => '禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success' => '启用成功', 'error' => '启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 获取登录人员导航信息
     * @return array|mixed
     */
    protected function getMenus()
    {
        //查询登录员工所属分组
        $authGroupAccessModel = new AuthGroupAccessModel();
        $authGroupAccess = $authGroupAccessModel->getAuthGroupAccessByUid(UID);
        if ($authGroupAccess && count($authGroupAccess) > 0) {
            $group_ids = [];
            foreach ($authGroupAccess as $item) {
                $group_ids[] = $item['group_id'];
            }

            //查询角色所管理的菜单模块
            $authGroupModuleModel = new AuthGroupModuleModel();
            $authGroupModules = $authGroupModuleModel->getModuleKeyByGroupIds($group_ids);

            if (empty($authGroupModules)) return [];
            $moduleIds = [];
            foreach ($authGroupModules as $value) {
                $moduleIds[] = $value['module_key'];
            }
            $moduleModel = new ModuleModel();
            //查询模块信息
            $modules = $moduleModel->getMenuByModuleKeys($moduleIds);
            if (empty($modules)) return [];

            $menu = [];
            foreach ($modules as $key => $value) {
                if ($value['pid'] === '0') {
                    $menu[$value['key']] = $value;
                    unset($modules[$key]);
                }
            }

            foreach ($modules as $svalue) {
                $menu[$svalue['pid']]['childs'][] = $svalue;
            }
            return $menu;

        } else {
            return [];
        }
    }


    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus_old($controller = CONTROLLER_NAME)
    {
        // $menus  =   session('ADMIN_MENU_LIST'.$controller);
        if (empty($menus)) {
            // 获取主菜单
            $where['pid'] = 0;
            $where['hide'] = 0;
            if (!C('DEVELOP_MODE')) { // 是否开发者模式
                $where['is_dev'] = 0;
            }
            $menus['main'] = M('Menu')->where($where)->order('sort asc')->select();

            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            $current = M('Menu')->where("url like '%{$controller}/" . ACTION_NAME . "%'")->field('id')->find();
            if ($current) {
                $nav = D('Menu')->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];

                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url'])) {
                        $this->error('控制器基类$menus属性元素配置有误');
                    }
                    if (stripos($item['url'], MODULE_NAME) !== 0) {
                        $item['url'] = MODULE_NAME . '/' . $item['url'];
                    }
                    // 判断主菜单权限
                    if (!IS_ROOT && !$this->checkRule($item['url'], AuthRuleModel::RULE_MAIN, null)) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }

                    // 获取当前主菜单的子菜单项
                    if ($item['title'] == $nav_first_title) {
                        $menus['main'][$key]['class'] = 'current';
                        //生成child树
                        $groups = M('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`")->select();
                        if ($groups) {
                            $groups = array_column($groups, 'group');
                        } else {
                            $groups = array();
                        }

                        //获取二级分类的合法url
                        $where = array();
                        $where['pid'] = $item['id'];
                        $where['hide'] = 0;
                        if (!C('DEVELOP_MODE')) { // 是否开发者模式
                            $where['is_dev'] = 0;
                        }
                        $second_urls = M('Menu')->where($where)->getField('id,url');

                        if (!IS_ROOT) {
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key => $to_check_url) {
                                if (stripos($to_check_url, MODULE_NAME) !== 0) {
                                    $rule = MODULE_NAME . '/' . $to_check_url;
                                } else {
                                    $rule = $to_check_url;
                                }
                                if ($this->checkRule($rule, AuthRuleModel::RULE_URL, null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group' => $g);
                            if (isset($to_check_urls)) {
                                if (empty($to_check_urls)) {
                                    // 没有任何权限
                                    continue;
                                } else {
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] = $item['id'];
                            $map['hide'] = 0;
                            if (!C('DEVELOP_MODE')) { // 是否开发者模式
                                $map['is_dev'] = 0;
                            }
                            $menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        if ($menus['child'] === array()) {
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }
                    }
                }
            }
            // session('ADMIN_MENU_LIST'.$controller,$menus);
        }
        return $menus;
    }

    protected function getModuleNodes()
    {
        $moduleModel = new ModuleModel();
        $list = $moduleModel->where(array('hide' => 0))->field('key,pid,title,url,tip,hide')->order('sort asc')->select();
        $nodes = list_to_tree($list, 'key', 'pid', 'child', '0');
        return $nodes;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true)
    {
        static $tree_nodes = array();
        if ($tree && !empty($tree_nodes[(int)$tree])) {
            return $tree_nodes[$tree];
        }
        if ((int)$tree) {
            $list = M('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if (stripos($value['url'], MODULE_NAME) !== 0) {
                    $list[$key]['url'] = MODULE_NAME . '/' . $value['url'];
                }
            }
            $nodes = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'operator', $root = 0);
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        } else {
            $nodes = M('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if (stripos($value['url'], MODULE_NAME) !== 0) {
                    $nodes[$key]['url'] = MODULE_NAME . '/' . $value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree] = $nodes;
        return $nodes;
    }


    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param Model $model 模型名或模型实例
     * @param array $where where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order 排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param array $base 基本的查询条件
     * @param boolean $field 单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists($model, $where = array(), $order = '', $base = array('status' => array('egt', 0)), $field = true)
    {
        $options = array();
        $REQUEST = (array)I('request.');

        $OPT = new \ReflectionProperty($model, 'options');
        $OPT->setAccessible(true);

        $pk = $model->getPk();
        if ($order === null) {
            //order置空
        } else if (isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']), array('desc', 'asc'))) {
            $options['order'] = '`' . $REQUEST['_field'] . '` ' . $REQUEST['_order'];
        } elseif ($order === '' && empty($options['order']) && !empty($pk)) {
            $options['order'] = $pk . ' desc';
        } elseif ($order) {
            $options['order'] = $order;
        }
        unset($REQUEST['_order'], $REQUEST['_field']);

        $options['where'] = array_filter(array_merge((array)$base, /*$REQUEST,*/
            (array)$where), function ($val) {
            if ($val === '' || $val === null) {
                return false;
            } else {
                return true;
            }
        });
        if (empty($options['where'])) {
            unset($options['where']);
        }
        $options = array_merge((array)$OPT->getValue($model), $options);
        $total = $model->where($options['where'])->count();

        if (isset($REQUEST['r'])) {
            $listRows = (int)$REQUEST['r'];
        } else {
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if ($total > $listRows) {
            $page->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p = $page->show();
        $this->assign('_page', $p ? $p : '');
        $this->assign('_total', $total);
        $options['limit'] = $page->firstRow . ',' . $page->listRows;

        $model->setProperty('options', $options);

        return $model->field($field)->select();
    }

    /**
     * 根据功能标识判断是否可以执行
     * @param $key
     * @return bool
     */
    protected function isExe($key)
    {
        $url = $this->getRequestUrl();
        if (strtolower('/' . MODULE_NAME . '/') === strtolower(ADMIN_PATH_NAME)) {
            if (strtolower($url) === strtolower('main/index/main')) {
                return true;
            } else {
                if (is_null($key)) return false;
                /*查询功能模块是否存在*/
                $moduleModel = new ModuleModel();
                $moduleCount = $moduleModel->getCountByUrlAndKey($url, $key);
                if ($moduleCount == 0) return false;
                /*查询功能模块是否分配给role*/
                $authGroupModuleModel = new AuthGroupModuleModel();
                $authGroupModules = $authGroupModuleModel->getInfosByModuleKey($key);
                $groupIds = [];
                foreach ($authGroupModules as $item) {
                    $groupIds[] = $item['group_id'];
                }
                if (empty($authGroupModules)) return false;
                /*查询登录员工是否含有该角色*/
                $authGroupAccessModel = new AuthGroupAccessModel();
                $authGroupAccessCount = $authGroupAccessModel->getCountByUidAndGroupIds(UID, $groupIds);
                if ($authGroupAccessCount == 0) return false;
                return true;
            }
        } else {
            return false;
        }

    }

    /**
     * 根据$key获取操作功能信息
     * @param $key
     * @return mixed
     */
    protected function getModuleByKey($key)
    {
        $moduleModel = new ModuleModel();
        return $moduleModel->getModuleByKey($key);
    }

    /**
     * 获取请求路径ControllerName/ActionName
     * @return string
     */
    private function getRequestUrl()
    {
        $actionName = str_replace('.' . C(URL_HTML_SUFFIX), '', ACTION_NAME);

        $rule = CONTROLLER_NAME . '/' . $actionName;
        return $rule;
    }

    /**
     * 根据父key获取有权限操作按钮
     * @param $key
     * @return array|mixed
     */
    protected function getMenuActions($key)
    {

        $moduleModel = new ModuleModel();
        $actions = $moduleModel->getActionsByParent($key);
        if (empty($actions)) return [];
        $authGroupAccessModel = new AuthGroupAccessModel();
        $authoGorupAccess = $authGroupAccessModel->getAuthGroupAccessByUid(UID);
        if (empty($authoGorupAccess)) return [];

        $groupIds = [];
        foreach ($authoGorupAccess as $item) {
            $groupIds[] = $item['group_id'];
        }

        $authGroupModule = new AuthGroupModuleModel();
        $moduleKeys = $authGroupModule->getModuleKeyByGroupIds($groupIds);
        if (empty($moduleKeys)) return [];

        $moduleIds = [];
        foreach ($moduleKeys as $item) {
            $moduleIds[] = $item['module_key'];
        }

        foreach ($actions as $key => $value) {
            if (!in_array($value['key'], $moduleIds)) unset($actions[$key]);
        }
        return $actions;
    }

    /**
     * 判断是否为超级管理员
     * @param $id 员工id
     * @return bool
     */
    protected function isAdministor($id)
    {
        if (in_array(C('USER_ADMINISTRATOR'), $id)) {
            return true;
        }
        return false;
    }

    /**
     * 判断是否是超级管理员组
     * @param $id
     * @return bool
     */
    protected function isAdminGroup($id)
    {
        if (is_array($id)) {
            if (in_array(C('USER_ADMINGROUP'), $id)) {
                return true;
            }
        } else {
            if (C('USER_ADMINGROUP') == $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获得权限信息
     */
    protected function getAuth(){
        $key = I('key');
        $actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
        $this->assign('actions', $actionInfos);
    }
}
