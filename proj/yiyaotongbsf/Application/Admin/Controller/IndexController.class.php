<?php


namespace Admin\Controller;

use Think\Controller;

/**
 * 后台入口控制器
 * @author mafengli <820471571@qq.com>
 */
class IndexController extends Controller
{

    /**
     * 后台入口
     * @author mafengli <820471571@qq.com>
     */
    public function index()
    {
        $this->redirect(ADMIN_PATH_NAME . 'Main/Index/main');
    }

}
