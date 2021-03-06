<?php
namespace Home\Model\SaleManager;
use \Home\Model\SaleManager\ParentModel;
use Think\Model;
class UsersModel extends ParentModel{
	private  $tableName = 'users'; 
	private  $validate = array(
			array('mobile','require','手机号已存在'), //默认情况下用正则进行验证
	);
	public function __construct(){
		parent::__construct($this->tableName,$this->validate);
	}
}