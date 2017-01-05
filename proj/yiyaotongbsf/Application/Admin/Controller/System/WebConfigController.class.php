<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/14
 * Time: 下午4:54
 */
namespace Admin\Controller\System;

use Admin\Controller\AdminController;
use Admin\Model\System\ConfigModel;

/**
 * 网站设置的控制器
 * Class WebConfigController
 * @package Admin\Controller\System
 */
class WebConfigController extends AdminController
{
    /**
     * 修改基础配置
     */
    public function base()
    {
        if (IS_POST) {
            $this->saveConfig();
        } else {
            $configModel = new ConfigModel();
            $list = $configModel->getConfigByGroup(1);
            $key = I('key');
            $actions = $this->getMenuActions($key);
            $actionInfos = [];
            foreach ($actions as $action) {
                $actionInfos[$action['key']] = $action;
            }
            $this->assign('list', $list);
            $this->assign('actions', $actionInfos);
            $this->display();
        }
    }

    public function system()
    {
        if (IS_POST) {
            $settings = C('PICTURE_UPLOAD');
            $config = I('config');
            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $file) {
                    if($file['size']!=0){
                        $upload = new \Think\Upload($settings);
                        $info = $upload->uploadOne($file);
                        if (!$info) {
                            $this->error($info->getError());
                        } else {
                            $path = $settings['urlPath'] . $info['savepath'] . $info['savename'];
                            $config[$key] = $path;
                        }
                    }
                }
            }
            $this->saveConfig($config);
        } else {
            $configModel = new ConfigModel();
            $list = $configModel->getConfigByGroup(4);
            $baseModule = $this->getModuleByKey('WEBCONFIG');
            $url = U(ADMIN_PATH_NAME . $baseModule['url'], array('key' => 'WEBCONFIG'));
            $this->assign('url', $url);
            $this->assign('list', $list);
            $this->display();
        }
    }

    private function saveConfig($config = null)
    {
        if ($config == null) {
            $config = I('config');
        }
        if ($config && is_array($config)) {
            $configModel = new ConfigModel();
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $configModel->updateValueByName($map, $value);
            }
        }
        S('DB_CONFIG_DATA', null);
        $this->success('保存成功！');
    }

}