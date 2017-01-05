<?php
namespace Home\Model\SaleManager;
use \Home\Model\SaleManager\ParentModel;
use Think\Model;
class NoticeModel extends ParentModel{
	private  $tableName = 'saleman_notice'; 
	private  $validate = array(
			array('title','require','地区必须！'), //默认情况下用正则进行验证
			array('content','require','手机号必须！'), //默认情况下用正则进行验证
	);
	public function __construct(){
		parent::__construct($this->tableName,$this->validate);
	}
}