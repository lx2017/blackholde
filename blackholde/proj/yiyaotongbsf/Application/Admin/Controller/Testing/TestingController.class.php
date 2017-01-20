<?php
namespace Admin\Controller\Testing;

use Admin\Controller\AdminController;


/**
 * 测试控制器
 * Class TestingController
 * @package Admin\Controller\System
 */
class TestingController extends AdminController
{
    public function testTime(){
		echo time();
	}
	
	public function testSpace(){
		echo 'no space';
	}
	
	public function testCode(){
		echo 'no Code';
	}

}