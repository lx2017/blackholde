<?php

/**
 * 此文件主要用于编写常用工具方法(县总，省总等)
 */
namespace Admin\Common;

use Home\Model\UserCenter\UserCenterModel;
use Home\Model\SaleManager\Alera\AleraModel;
use Home\Model\SaleManager\SalemanagerModel;
use Think\Upload;

/**
 * 该文件为县总，地总，省总，大区经理，总部的常用方法
 */
class SalemanagerHelper {
	
	/**
	 * 时间戳转换为：年－月－日 时：分：秒格式
	 *
	 * @param int $time
	 *        	需要转换的时间戳
	 * @return string 格式化后的时间
	 */
	public static function timeFormat($time) {
		return date ( 'Y-m-d H:i:s', $time );
	}
	
	/**
	 * 将格式化时间转换为时间戳
	 *
	 * @param string $formatTime
	 *        	需要转换的时间
	 * @return string 格式化后的时间戳
	 */
	public static function toTimestamp($formatTime) {
		return strtotime ( $formatTime );
	}
	
	/**
	 * 将含有time字段的数组，格式化时间
	 *
	 * @param array $data
	 *        	需要格式化的数组
	 * @return array 格式化后的数组
	 */
	public static function dataTimeFormat($data) {
		foreach ( $data as $key => $value ) {
			if (strpos ( $key, 'time' ) === TRUE) {
				$data[$key] = $this->timeFormat ($value);
			}
		}
		return $data;
	}
	/**
	 * 获取下一级的roleid
	 *
	 * @param numeric $userId
	 *        	用户id
	 * @return numeric 当前用户下级的roleid
	 */
	public static function getLowerRoleId($userId) {
		$user = new UserCenterModel ();
		$tuser = $user->FindByUserid ( $userId );
		if (isset ( $tuser ['role_id'] )) {
			switch (intval ( $tuser ['role_id'] )) {
				case C ( 'SALE_MANAGER.SALE_HEAD' ) : // 总部
					return C ( 'SALE_MANAGER.SALE_MANAGER' );
					break;
				case C ( 'SALE_MANAGER.SALE_MANAGER' ) : // 大区经理
					return C ( 'SALE_MANAGER.SALE_PROVINCE' );
					break;
				case C ( 'SALE_MANAGER.SALE_PROVINCE' ) : // 省总
					return C ( 'SALE_MANAGER.SALE_CITY' );
					break;
				case C ( 'SALE_MANAGER.SALE_CITY' ) : // 地总
					return C ( 'SALE_MANAGER.SALE_COUNTY' );
					break;
				case C ( 'SALE_MANAGER.SALE_COUNTY' ) : // 县总
					return C ( 'SALE_MANAGER.SALE_CLERK' );
					break;
				default :
					return C ( 'SALE_MANAGER.SALE_CLERK' );
					break;
			}
		} else {
			return 0;
		}
	}
	
	/**
	 * 附件上传
	 */
	public static function  upload(){
		$setting = C ( 'ATTACHMENT_UPLOAD' );
		/* 调用文件上传组件上传文件 */
		$uploader = new Upload ( $setting, 'Local' );
		$info = $uploader->upload ( $_FILES );
		if ($info) {
			foreach($info as $file){
				$file_name = $setting ['rootPath'] .$file ['savepath'] .$file ['savename'];
				$re[$file ['key']] = substr($file_name,1);
			}
			return $re;
		}else{
			return false;
		}
	}
	
	/**
	 * 获取用户所管辖的范围
	 * @param string $locations //用户管辖范围的ids
	 * @param id $roleId // 用户角色id
	 */
	public function getLocations($locations,$roleId){
		$location = new AleraModel ();
		switch ($roleId) {
			case C ( 'SALE_MANAGER.SALE_HEAD' ) : // 总部
				$code = 1;
				$zero = '000000';
				break;
			case C ( 'SALE_MANAGER.SALE_MANAGER' ) : // 大区经理
				$code = 2;
				$area = 'state';
				$zero = '0000';
				break;
			case C ( 'SALE_MANAGER.SALE_PROVINCE' ) : // 省总
				$code = 4;
				$zero = '00';
				$area = 'city';
				break;
			case C ( 'SALE_MANAGER.SALE_CITY' ) : // 地总
				$code = 6;
				$area = 'city';
				$zero = '';
				break;
			default :
				return C ( 'SALE_MANAGER.SALE_CLERK' );
				break;
		}
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
		$sql = "select left(sz_code,".$code.")  from ymt_area where id in (".$locations.")";
		$parm = self::arr2str($Model->query($sql));
		$pql = "select sz_code as code ,ymt_area.".$area." from ymt_area where left(sz_code,".$code.")  in (".$parm.") and sz_code= concat(left(sz_code,".$code."),'00') group by $area";	
		return $Model->query($pql);
	}
	
	public static function getCounty(){
		//code的第3～4位不全部为0，且5～6位不同时为0
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
		$sql = "select city,sz_code from ymt_area where right(sz_code,4)<>'0000' and right(sz_code,2)<>'00'";
		return $Model->query($sql);
	}
	/**
	 * 获取城市
	 */
	public static function getCity(){
		//code的第3～4位不全部为0，且5～6位不同时为0
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
		$sql = "select city,sz_code from ymt_area where substr(sz_code,3,2)<>'00'  and  right(sz_code,2) ='00'";
		return $Model->query($sql);
	}
	/**
	 * 获取省份
	 */
	public static function getProvince(){
		//code的第3～4位不全部为0，且5～6位不同时为0
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
		$sql = "select city,sz_code from ymt_area where left(sz_code,2)<>'00' and substr(sz_code,3,4) = '0000'";
		return $Model->query($sql);
	}
	public static function getCascadeArea(){
		$Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
		$sql = "select *  from ymt_area";
		
		$config = S('AREA_DATA_ROW');
		if (!$config) {
			$lists = $Model->query($sql);
			S('AREA_DATA_ROW',$lists);
		}else{
			$lists = S('AREA_DATA_ROW');
		}
		return $lists;
	}
	
	public static function getAreaCode($province,$city){
		 $list = self::getCascadeArea();
		 foreach ($list as $k=>$v){
		 	if(strstr($v['state'],$province)!==false && strstr($v['city'],$city)!==false){
		 		$code= $v['id'];
		 		break;
		 	}
		 }
		 if(!isset($code)){
		 	$code = '';
		 }
		 return $code;
	}
	public static function getAreaCodeByCounty($city){
		$list = self::getCascadeArea();
		foreach ($list as $k=>$v){
			if(strstr($v['city'],$city)!==false){
				$code= $v['id'];
				break;
			}
		}
		if(!isset($code)){
			$code = '';
		}
		return $code;
	}
	
	private function getPid($sz_code){
		if(substr($sz_code,0,2)!='00' && substr($sz_code,2,4)=='0000'){//省份，无
			$pid = null;
		}elseif(substr($sz_code,2,2)!='00' && substr($sz_code,4,2)=='00'){//市的上一级，省
			$pid = substr($sz_code,0,2).'0000';
		}elseif(substr($sz_code,0,2)!='00' && substr($sz_code,4,2)!='00'){//县的上一级，市
			$pid = substr($sz_code,0,4).'00';
		}
		return $pid;
	}
	
	public function convertSex($sexcode){
		if($sexcode==1){
			return "男";
		}else{
			return "女";
		}
	}
	
	public static  function toTree($list=null, $pk='sz_code',$pid = 'pid',$child = '_child'){
		// 创建Tree
		$tree = array();
		if(is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
	
			foreach ($list as $key => $data) {
				$_key = is_object($data)?$data->$pk:$data[$pk];
// 				if(!array_key_exists($_key,$refer[$_key])){
// 					$refer[$_key] =& $list[$key];
// 				}
				$refer[$_key] =& $list[$key];
			}
			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = self::getPid($data[$pk]);
				$is_exist_pid = false;
				if($parentId!==null){
					foreach($refer as $k=>$v){
						if($parentId==$k){
							$is_exist_pid = true;
							break;
						}
					}
				}
				
				if ($is_exist_pid) {
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$child][] =& $list[$key];
					}
				} else {
					$tree[] =& $list[$key];
				}
			}
		}
		return $tree;
	}
	/**
	 * 把二维数组转化为以逗号隔开字符串
	 * @param array $arr 需要转化的数组
	 * @return string $t 返回转化后的字符串
	 */
	private static function arr2str($arr) {
		foreach ( $arr as $v ) {
			$v = join ( ",", $v ); // 可以用implode将一维数组转换为用逗号连接的字符串
			$temp [] = $v;
		}
		$t = "";
		foreach ( $temp as $v ) {
			$t .= $v . ",";
		}
		$t = substr ( $t, 0, - 1 );
		return $t;
	}
}