<?php

/**
 * 此文件主要用于编写常用工具方法(县总，省总等)
 */
namespace Home\Common;

use Home\Model\UserCenter\UserCenterModel;
use Home\Model\SaleManager\Alera\AleraModel;
use Home\Model\SaleManager\County\SalemanagerModel;
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
		$user = new \Home\Model\SaleManager\County\SaleManagerModel();
		$tuser = $user->findById( $userId );
// 		session(C('SESSION_PREFIX').'_salemanager',$tuser);
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
	 * 获取下一级的roleid
	 *
	 * @param numeric $userId
	 *        	用户id
	 * @return numeric 当前用户下级的roleid
	 */
	public static function redirectUrl($roleId) {
		$user = new SalemanagerModel ();
		$tuser = $user->findById( $userId );
		// 		session(C('SESSION_PREFIX').'_salemanager',$tuser);
		if (isset ( $roleId )) {
			switch (intval ( $roleId )) {
				
				case C ( 'SALE_MANAGER.SALE_HEAD' ) : // 总部
					return '/Home/SaleManager/Head/';
					break;
				case C ( 'SALE_MANAGER.SALE_MANAGER' ) : // 大区经理
					return '/Home/SaleManager/Salemanager/';
					break;
				case C ( 'SALE_MANAGER.SALE_PROVINCE' ) : // 省总
					return '/Home/SaleManager/Province/';
					break;
				case C ( 'SALE_MANAGER.SALE_CITY' ) : // 地总
					return '/Home/SaleManager/City/';
					break;
				case C ( 'SALE_MANAGER.SALE_COUNTY' ) : // 县总
					return '/Home/SaleManager/County/';
					break;
				case C ( 'SALE_MANAGER.SALE_DEPARTMENT' ) : // 销售部
					return '/Home/SaleManager/Saledepartment/';
					break;
				case C ( 'SALE_MANAGER.CAREER_DEPARTMENT' ) : // 事业部
					return '/Home/SaleManager/Career/';
					break;
				default :
					return '/Home/Saleman/Saleman/';
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
	/**
	 * 消息列表显示问题
	 * @param array $arr 需要转化的数组
	 * @return string $t 返回转化后的字符串
	 */
	public static function linfolist($time){
		$today = date("Y-m-d",strtotime("now"));
		$thisyear = date("Y",strtotime("now"));
		$yestoday = date("Y-m-d",strtotime("-1 day"));
		if(date("Y-m-d",$time)==$yestoday){
		  return '昨天';
		}
		if(date("Y-m-d",$time)==$today){
			return date("H:i:s",$time);
		}
		if(date("Y",$time)==$thisyear){
			return date("m月d日",$time);
		}
		
		return date("Y年m月d日",$time);
	}
	
	/**
	 *申请类型列表,将会议类型根据数字转换出对应的中文
	 */
	public static function traMetting($applyId){
		$type=C('SALEMANAGER_APPLY_NAME');
		foreach ($type as $k=>$v){
			if($k==$applyId){
				return $v;
			}
		}
	}
	/**
	 *会议状态转换为中文
	 */
	public  static function convertApplyStatus($applyStatus){
		switch(intval($applyStatus)){
			case 0:$type='待审批';break;
			case 1:$type='已通过';break;
			case 2:$type='未通过';break;
		}
		return $type;
	}
	
	/**
	 * 订单状态转换为中文
	 */
	public  static function convertOrderStatus($applyStatus){
		switch(intval($applyStatus)){
			case 0:$type='待处理';break;
			case 1:$type='已完成';break;
			case 2:$type='退货';break;
		}
		return $type;
	}
	
	/**
	 *获取当前用户的总部
	 */
	public static function getSaleHeadById($id,$count){
			$count++;
			if($count>5){
				return false;
			}
			$sales = new SalemanagerModel();
			$tmp = $sales->findById($id);
			if($tmp['role_id']==C('SALE_MANAGER.SALE_HEAD')){
				return $tmp;
			}else{
				return self::getSaleHeadById($tmp['superior_id'],$count);
			}
	}
	
	/**
	 * 获取下一级的roleid
	 *
	 * @param numeric $userId
	 *        	用户id
	 * @return numeric 当前用户下级的roleid
	 */
	public static function getRoleName($roleId) {
		// 		session(C('SESSION_PREFIX').'_salemanager',$tuser);
		if (isset ($roleId)) {
			switch (intval ($roleId)) {
				case C ( 'SALE_MANAGER.SALE_HEAD' ) : // 总部
					return '总部';
					break;
				case C ( 'SALE_MANAGER.SALE_MANAGER' ) : // 大区经理
					return '大区经理';
					break;
				case C ( 'SALE_MANAGER.SALE_PROVINCE' ) : // 省总
					return '省总';
					break;
				case C ( 'SALE_MANAGER.SALE_CITY' ) : // 地总
					return '地总';
					break;
				case C ( 'SALE_MANAGER.SALE_COUNTY' ) : // 县总
					return '县总';
					break;
				default :
					return '业务员';
					break;
			}
		} else {
			return 0;
		}
	}
	
	/**
	 * 获取下一级的roleid
	 *
	 * @param numeric $userId
	 *        	用户id
	 * @return numeric 当前用户下级的roleid
	 */
	public static function getRoleTitle($roleId) {
		// 		session(C('SESSION_PREFIX').'_salemanager',$tuser);
		if (isset ($roleId)) {
			switch (intval ($roleId)) {
				case C ( 'SALE_MANAGER.SALE_HEAD' ) : // 总部
					return '大区经理订单';
					break;
				case C ( 'SALE_MANAGER.SALE_MANAGER' ) : // 大区经理
					return '省总订单';
					break;
				case C ( 'SALE_MANAGER.SALE_PROVINCE' ) : // 省总
					return '地总订单';
					break;
				case C ( 'SALE_MANAGER.SALE_CITY' ) : // 地总
					return '县总订单';
					break;
				case C ( 'SALE_MANAGER.SALE_COUNTY' ) : // 县总
					return '业务员订单';
					break;
				default :
					return '业务员订单';
					break;
			}
		} else {
			return 0;
		}
	}
}