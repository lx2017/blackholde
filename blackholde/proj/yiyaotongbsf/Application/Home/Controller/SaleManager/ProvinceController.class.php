<?php

namespace Home\Controller\SaleManager;

use Home\Controller\SaleManager\BasicController;
use Home\Model\SaleManager\City\SaleManagerModel;
use Home\Model\SaleManager\Clinic\ClinicModel;
class ProvinceController extends BasicController{
	protected  $clinicMod; 
	protected $saleMod;
	function _initialize() {
		$this->clinicMod = new ClinicModel();
		$this->saleMod = new SaleManagerModel();
		parent::_initialize($this->saleMod);
	}
}
