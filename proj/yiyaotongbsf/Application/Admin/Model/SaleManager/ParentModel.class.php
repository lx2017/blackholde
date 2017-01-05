<?php
/**
 * 此文件主要用于model类的增删改查基本操作
 */
namespace Home\Model\SaleManager;
use Think\Page;

/**
 * 该文件为县总，地总，省总，大区经理，总部的父类，实现了model的增／删／改／查等数据相关操作
 */
class ParentModel{
	private $Mod; //模型变量
	private $validate; //校验数组
	
	/**
	 * 主要实现数据库表名，校验数组的注入
	 * @param string $model 模型对应的数据库表名 
	 * @param array $validate 模型需要校验的字段
	 * @return 
	 */
	public function __construct($model,$validate){
		if(!isset($this->model)){
			$this->Mod = D($model);
		}
		if(!isset($this->validate)){
			$this->validate=$validate;
		}
	}
	
	/**
	 * 模型类的添加操作，包含（1.数据验证，2.数据重复校验，3.添加新数据）
	 * @param array $data 需要添加的数据
	 * @return  array  包含插入操作状态码，状态信息等
	 */
	public function add($data){
		if (!$this->Mod->validate($this->validate)->create($data)){ // 如果创建失败 表示验证没有通过 输出错误提示信息
	    	 	$re = array(
	    	 		'status'=>C('SALE_MANAGER.ADD_VALIDATE_FAIL_STATUS'),
	    	 		'msg'=>$this->Mod->getError()
	    	 	);
		}else{ // 验证通过 可以进行其他数据操作
			if($this->checkRepeat($data)){ //校验数据否重复，如果重复，返回重复信息
				$re = array(
						'status'=>C('SALE_MANAGER.ADD_INSERT_REPEATL_STATUS'),
						'msg'=>C('SALE_MANAGER.ADD_INSERT_REPEAT_MSG'),
						'data'=>$data
				);
				return $re;
			}
			$data ['add_time'] = time();
			$result = $this->Mod->filter('strip_tags')->add(); // 写入数据到数据库,并且过滤数据
			if($result){// 如果主键是自动增长型 成功后返回值就是最新插入的值
				$insertId = $result;
				$data['id'] = $insertId;
				$re = array(
						'status'=>C('SALE_MANAGER.ADD_INSERT_SUCCESS_STATUS'),
						'msg'=>C('SALE_MANAGER.ADD_INSERT_SUCCESS_MSG'),
						'data'=>$data
				);
			}else{
				$re = array(
						'status'=>C('SALE_MANAGER.ADD_INSERT_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.ADD_INSERT_FAIL_MSG')
				);
			}
		}
		
		return $re;
	}
	
	/**
	 * 模型类通过id进行更新操作
	 * @param numeric $id 需要修改数据的主键
	 * @param array $data 需要修改的数据
	 * @return  array  包含修改操作状态码，状态信息等
	 */
	public function updateById($id,$data){
		if (!$this->Mod->validate($this->validate)->create($data)){ // 如果创建失败 表示验证没有通过 输出错误提示信息
			$re = array(
					'status'=>C('SALE_MANAGER.ADD_VALIDATE_FAIL_STATUS'),
					'msg'=>$this->Mod->getError()
			);
		}else{
			$result = $this->Mod->where('id='.$id)->data($data)->save();
			if($result!==false){
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_SUCCESS_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_SUCCESS_MSG')
				);
			}else{
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_FAIL_MSG')
				);
			}
			
		}	
		return $re;
	}
	
	/**
	 * 模型类通过id进行字段更新操作
	 * @param numeric $id 需要修改数据的主键
	 * @param array $data 需要修改的数据
	 * @return  array  包含修改操作状态码，状态信息等
	 */
	public function updateField($id,$data){
		if (!$this->Mod->validate($this->validate)->create($data)){ // 如果创建失败 表示验证没有通过 输出错误提示信息
			$re = array(
					'status'=>C('SALE_MANAGER.ADD_VALIDATE_FAIL_STATUS'),
					'msg'=>$this->Mod->getError()
			);
		}else{
			$result = $this->Mod->where('id='.$id)->setField($data);
			if($result!==false){
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_SUCCESS_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_SUCCESS_MSG')
				);
			}else{
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_FAIL_MSG')
				);
			}
		}
		
		return $re;
	}
	
	/**
	 * 模型类通过id进行字段更新操作
	 * @param numeric $where 条件
	 * @param array $data 需要修改的数据
	 * @return  array  包含修改操作状态码，状态信息等
	 */
	public function updateFieldByCondition($where,$data){
		if (!$this->Mod->validate($this->validate)->create($data)){ // 如果创建失败 表示验证没有通过 输出错误提示信息
			$re = array(
					'status'=>C('SALE_MANAGER.ADD_VALIDATE_FAIL_STATUS'),
					'msg'=>$this->Mod->getError()
			);
		}else{
			$result = $this->Mod->where($where)->setField($data);
			if($result!==false){
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_SUCCESS_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_SUCCESS_MSG')
				);
			}else{
				$re = array(
						'status'=>C('SALE_MANAGER.UPDATE_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.UPDATET_FAIL_MSG')
				);
			}
		}
	
		return $re;
	}
	
	/**
	 * 模型类通过id进行删除操作
	 * @param numeric $id 需要修改数据的主键
	 * @return  array  包含删除操作状态码，状态信息等
	 */
	public function delById($id){
		$result = $this->Mod->where('id='.$id)->delete();
		if($result!==false){
			$re = array(
					'status'=>C('SALE_MANAGER.DELETE_SUCCESS_STATUS'),
					'msg'=>C('SALE_MANAGER.DELETE_SUCCESS_MSG')
			);
		}else{
			$re = array(
					'status'=>C('SALE_MANAGER.DELETE_FAIL_STATUS'),
					'msg'=>C('SALE_MANAGER.DELETE_FAIL_MSG')
			);
		}
		return $re;
	}
	
	/**
	 * 模型类通过数组作为查询条件查询
	 * @param numeric $limit 需要查询的条数
	 * @param numeric $startLine 起始行数
	 * @param string  $order 根据某个字段排序,例如：score desc 根据score字段降序
	 * @param mixed   $group 根据某个字段分组，例如：id 根据id分组
	 * @param string  $field 要获取的字段名称，可以为数组或者字符串,例如：'id,name' 或array('id','name')获取id，name字段值
	 * @param bool    $fieldFlag 用于排除某些字段，TRUE or FALSE；默认为FALSE，当$field参数不为空时起作用,
	 * 							例如：$field='id,name',$fieldFlag=TRUE  表示除了id,name不取出外，其他字段值都取出
	 * @return  array  返回查询后的数据结果集
	 */
    public function findInfobyCondition($condition, $limit = NULL, $startLine = null, $order = NULL, $group = NULL, $field=NULL, $fieldFlag=FALSE)  {
        if ($order !== NULL) {
            $this->Mod->order($order);
        }
        if ($group !== NULL) {
            $this->Mod->group($group);
        } 
        if($field ===NULL){  //如果传入的$field为NULL，获取所有字段
            $this->Mod->field(true);
        }  else if($field !==NULL && $fieldFlag === FALSE){ //如果$field不为NULL并且$fieldFlag为FALSE
            $this->Mod->field($field);      
        }  else {//如果$field不为NULL并且$fieldFlag不为FALSE，则表示排除$field中的字段
            $this->Mod->field($field, $fieldFlag);
        }
        if ($limit === NULL and $startLine === null) {
            return $this->Mod->where($condition)->select();
        } else if ($startLine === NULL and $limit !== NULL) {
            return $this->Mod->limit($limit)->where($condition)->select();
        } else if ($limit !== NULL and $startLine !== NULL) {
            return $this->Mod->limit($startLine, $limit)->where($condition)->select();
        } else {
            return null;
        }
    }
	
	/**
	 * 模型类批量添加操作
	 * @param array $data 需要添加的数据
	 * @return  array  返回查询结果集
	 */
	public function addAll($data){
		$result =  $this->Mod->addAll($data);
		return $result;
	}
	
	/**
	 * 模型类批量条件删除操作
	 * @param array $condition 删除条件
	 * @return  bool  如果不等于false，表示删除成功，反之
	 */
	public function deleteByCondition(array $condition) {
		return $this->Mod->where($condition)->delete();
	} 
	
	/**
	 * 模型类查询全部
	 * @return  array  查询数据结果集
	 */
	public function fetchAll() {
		return $this->Mod->select();
	}
	
	/**
	 * 分页查询
	 * @param $where array 条件数组
	 * @return  array  查询数据结果集以及分页信息，当前数组
	 */
	public function pageList($where = array()){
		//得到分页信息
		$count = $this->Mod->where($where)->count();
		$pageTool = new Page($count, 10);//分页
		//设置分页信息
		$page_config = C('PAGE_CONFIG');
		foreach($page_config as $k=>$v){
			$pageTool->setConfig($k,$v);
		}
		$pageHtml = $pageTool->show();
		//得到表信息
		$first = $pageTool->firstRow;
		if ($first >= $count && $count != 0) {
			//超界则总是在最后一页,并且记录不能为0
			$first = $count - $this->Mod->size;
		}
		//查询数据
		$rows = $this->Mod->where($where)->limit($first, $pageTool->listRows)->select();
		$area =S('AREA_DATA_ROW');
		foreach($rows as $key =>$value){
			$tmpArea = explode(',',$rows[$key]['manage_locations']);
			
			foreach ($tmpArea as $a=>$vo){
				foreach ($area as $k=>$v){
					if($vo==$v['id']){
						if(!empty($rows[$key]['locations'])){
							$rows[$key]['locations'] .= ",".$v['city'];
						}else{
							$rows[$key]['locations'] .=$v['city'];
						}
						break;
					}
				}
			}
			
		}
		//返回结果
		return array('rows' => $rows, 'pageHtml' => $pageHtml,'total'=>$count);
	}
	
	/**
	 * 根据id查找记录
	 * @param $id string 条件ID
	 * @return  array  查询数据结果集
	 */
	public function findById($id){
		return $this->Mod->where(array("id"=>$id))->find();
	}
	
	/**
	 * excel导入人员信息
	 */
	public function  import($data){
		return $this->Mod->add($data);
	}
	
	/**
	 * 校验数据是否重复，供本类使用
	 * @param array $where 数据条件
	 * @return  bool  如果等于true，表示重复，反之
	 */
	private function checkRepeat($where){	
		$result = $this->Mod->where($where)->count();
		if(($result>=1)){
			return true;
		}else{
			return false;
		}
	}
}