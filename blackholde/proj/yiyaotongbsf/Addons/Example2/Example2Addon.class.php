<?php

namespace Addons\Example2;
use Common\Controller\Addon;

/**
 * 示列插件
 * @author XXXX
 */

    class Example2Addon extends Addon{

        public $info = array(
            'name'=>'Example2',
            'title'=>'示列',
            'description'=>'这是一个临时描述',
            'status'=>0,
            'author'=>'XXXX',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的ali钩子方法
        public function ali($param){

        }

    }