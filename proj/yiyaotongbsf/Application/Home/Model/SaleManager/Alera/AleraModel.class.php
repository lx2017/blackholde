<?php
namespace Home\Model\SaleManager\Alera;
use \Home\Model\SaleManager\ParentModel;
use Think\Model;
class AleraModel extends ParentModel{
	private  $tableName = 'area'; 
// 	private  $validate = array(
// 			array('description','require','描述必须！'), //默认情况下用正则进行验证
// 			array('user_id','require','用户id必须！'), //默认情况下用正则进行验证
// 	);
	public function __construct(){
		parent::__construct($this->tableName,$this->validate);
	}
}