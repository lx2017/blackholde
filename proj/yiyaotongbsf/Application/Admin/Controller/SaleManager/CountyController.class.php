<?php

namespace Admin\Controller\SaleManager;

use Admin\Controller\SaleManager\BasicController;
use Home\Model\SaleManager\County\SaleManagerModel;
class CountyController extends BasicController{
	protected $roleId = 4;//县总
	protected $superRoleId = 3; //地总
	protected $saleManagerMod ;
	function _initialize() {
		$this->saleManagerMod = new SalemanagerModel();
		parent::_initialize($this->saleManagerMod ,$this->roleId,$this->superRoleId);
	}
}
