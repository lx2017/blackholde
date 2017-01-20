<?php
namespace Home\Model\SaleManager\County;
use \Home\Model\SaleManager\ParentModel;
use Think\Model;
class SalemanagerModel extends ParentModel{
	private  $tableName = 'saleman'; 
	private  $validate = array(
			array('phone','',' 手机号已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
			//array('phone','require','手机号必须！'), //默认情况下用正则进行验证
	);
	public function __construct(){
		parent::__construct($this->tableName,$this->validate);
	}
	public function findInfobyCondition($condition, $limit = NULL, $startLine = null, $order = NULL, $group = NULL, $field=NULL, $fieldFlag=FALSE)  {
		$re = parent::findInfobyCondition($condition,$limit,$startLine,$order,$group,$field,$fieldFlag);
		foreach ($re as $key=>$value){
			if($value['sex']=='1'){
				$re[$key]['sex']='男';
			}
			if($value['sex']==2){
				$re[$key]['sex']='女';
			}
		}
		return $re;
	}
}