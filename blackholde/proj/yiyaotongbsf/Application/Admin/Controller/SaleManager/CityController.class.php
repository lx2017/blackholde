<?php

namespace Admin\Controller\SaleManager;

use Admin\Controller\SaleManager\BasicController;
use Home\Model\SaleManager\County\SaleManagerModel;
class CityController extends BasicController{
	protected $roleId = 3;
	protected $superRoleId = 2;
	protected $saleManagerMod ;
	function _initialize() {
		$this->saleManagerMod = new SaleManagerModel();
		parent::_initialize($this->saleManagerMod ,$this->roleId,$this->superRoleId);
	}
}
