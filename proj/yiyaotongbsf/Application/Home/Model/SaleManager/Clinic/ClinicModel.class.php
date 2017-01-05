<?php
namespace Home\Model\SaleManager\Clinic;
use \Home\Model\SaleManager\ParentModel;
use Think\Model;
class ClinicModel extends ParentModel{
	private  $tableName = 'clinic'; 
	private  $validate = array(
			array('manager_mobile','',' 手机号已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
			//array('clinic_phone','require','手机号必须！'), //默认情况下用正则进行验证
	);
	public function __construct(){
		parent::__construct($this->tableName,$this->validate);
	}
}