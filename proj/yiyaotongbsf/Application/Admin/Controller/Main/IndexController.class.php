<?php
/**
 * 后台首页控制器
 */

namespace Admin\Controller\Main;

use Admin\Controller\AdminController;

/**
 * 后台首页控制器
 * @author mafengli <820471571@qq.com>
 */
class IndexController extends AdminController
{

    /**
     * 后台首页
     * @author mafengli <820471571@qq.com>
     */
    public function main()
    {
        if (UID) {
            $this->meta_title = '管理首页';
            $menus = $this->getMenus(null);
            if (empty($menus)) $this->error('403:禁止访问');
            $this->assign('__MENU__', $menus);
            $this->display();
        } else {
            $this->redirect(ADMIN_PATH_NAME . '/Login/Public/login');
        }
    }



}
