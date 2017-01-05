<?php

/**
 * 此文件主要用于相关业务逻辑操作
 */
namespace Admin\Controller\SaleManager;

use Think\Controller;
use Admin\Common\SalemanagerHelper;
use Admin\Controller\AdminController;
use Think\Upload;


/**
 * 该文件用于公共操作
 */
class BasicController extends AdminController {
	protected $actions;
	protected $saleModel;
	protected $paramtersMod;
	protected $roleId;
	protected $superRoleId;
	static $_userinfo;
	protected $lowerRoleId;
	
	/**
	 * 初始化
	 * @param unknown $key
	 */
	function _initialize($model,$roleId,$superRoleId) {
		$this->saleModel = $model;
		$this->roleId = $roleId;
		$this->superRoleId = $superRoleId;
		parent::_initialize ();
	}
	
	/**
	 * 当前用户对应的下级列表(例如：省总对应下级列表为地总，以此类推)
	 */
	public function lists(){
		$REQUEST = ( array ) I ( 'request.' );
		if(!empty($REQUEST['key'])){
			$actions = $this->getMenuActions($REQUEST['key']);
		}
		foreach ( $actions as $action ) {
				$actionInfos [$action ['key']] = $action;
		}
		$list = $this->saleModel->pageList(array('role_id'=>$this->roleId));
		$this->assign('list', $list['rows']);
		$this->assign('page',$list['pageHtml']);
		$this->assign('_total',$list['total']);
		$this->assign('actions',$actionInfos);
		$this->display();
	}
	
	/**
	 * 每个用户详细信息的获取
	 */
	public function detail(){
		$REQUEST = ( array ) I ( 'request.' );
		if(is_numeric($REQUEST['userId'])){
			$user = $this->saleModel->findInfobyCondition(array("id"=>$REQUEST['userId']));
			$this->display('user',$user);
		}else{
			$this->error('错误!');
		}
	}
	
	/**
	 * 添加用户(包括添加业务员，地总，省总)
	 * @param mixed $post        	
	 */
	public function add() {
		if (IS_POST) {
			$REQUEST = ( array ) I ( 'request.' );
			$data ['phone'] = ($REQUEST ['phone']);
			$data ['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'),UC_AUTH_KEY);
			$data ['real_name'] = $REQUEST ['real_name'];
			$data ['card_number'] = $REQUEST ['card_number'];
			$data ['sex'] = $REQUEST ['sex'];
			$data ['age'] = $REQUEST ['age'];
			$data['status'] = $REQUEST ['status'];
			$data ['superior_id'] = $REQUEST['superior_id']; //当前用户uid
			$data ['role_id'] =$this->roleId; // 根据type
			$data ['work_event'] =$REQUEST['work_event'];
			$data['manage_locations']=$REQUEST['manage_locations'];
			$re = SalemanagerHelper::upload();//附件上传
			if($re){
				foreach ($re as $key=>$url){
					$data[$key] = $url;
				}
			}
			//$data['manage_locations']=SalemanagerHelper::getAreaCode($REQUEST ['province'],$REQUEST ['manage_locations']);//所属县
			$result = $this->saleModel->add ( $data );
			responseJson ( $result['status'],$result['msg'],$result['data'] );
		} else {
			$supers = $this->saleModel->findInfobyCondition(array('role_id'=>$this->superRoleId));
			$this->assign('supers',$supers);
			$this->display ();
		}
	}
	
	/**
	 * 修改用户(更换下级用户)
	 * @param mixed $post         	
	 */
	public function edit() {
		$REQUEST = ( array ) I ( 'request.' );
		if (IS_POST) {
			if(is_numeric($REQUEST['id'])){
				//$data ['phone'] = intval($REQUEST ['phone']);
				$data ['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'),UC_AUTH_KEY);
				$data ['real_name'] = $REQUEST ['real_name'];
				$data ['card_number'] = $REQUEST ['card_number'];
				$data ['sex'] = $REQUEST ['sex'];
				$data ['age'] = $REQUEST ['age'];
				$data['manage_locations'] = $REQUEST ['manage_locations'];
				$data['status'] = $REQUEST ['status'];
				$data ['work_event'] =$REQUEST['work_event'];
				$result = $this->saleModel->updateById ($REQUEST['id'],$data );
			}else{
				$result =array(
						'status'=>C('SALE_MANAGER.ID_VALIDATE_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.ID_VALIDATE_FAIL_MSG'),
				);
			}
			responseJson ( $result['status'],$result['msg'],$result['data'] );
		}else{
			if(is_numeric($REQUEST['id'])){
				$sale = $this->saleModel->findById($REQUEST['id']);
				$this->assign('sale',$sale);
				$this->display ();
			}else{
				$this->error("系统错误!");
			}
		}
	}
	
	/**
	 * 删除用户
	 * @param mixed $post
	 */
	public function del(){
		$REQUEST = ( array ) I ( 'request.' );
		if(is_numeric($REQUEST['id'])){
			$result = $this->saleModel->delById ($REQUEST['id'],$data );
			responseJson ( $result['status'],$result['msg'],$result['data'] );
		}else{
			$this->error("系统错误!");
		}
	}
	
	/**
	 * 导入用户信息
	 * @param mixed $post
	 */
	public function excelimport(){
		if(IS_POST){
			$REQUEST = ( array ) I ( 'request.' );
			if (isset ( $_FILES ["excel"] ) && ($_FILES ["excel"] ["error"] == 0)) {
				$setting = C ( 'UPLOADIFY_EXCEL_CONF' );
				/* 调用文件上传组件上传文件 */
				$uploader = new Upload ( $setting, 'Local' );
				$info = $uploader->upload ( $_FILES );
				if ($info) {
					vendor ( "PHPExcel.PHPExcel" );
					$file_name = $setting ['rootPath'] . $info ['excel'] ['savepath'] . $info ['excel'] ['savename'];
					if (! file_exists ( $file_name )) {
						die ( 'no file!' );
					}
					$extension = strtolower ( pathinfo ( $file_name, PATHINFO_EXTENSION ) );
					if ($extension == 'xlsx') {
						$objReader = \PHPExcel_IOFactory::createReader ( 'Excel2007' );
					} else if ($extension == 'xls') {
						$objReader = \PHPExcel_IOFactory::createReader ( 'Excel5' );
					} else if ($extension == 'csv') {
						$objReader = \PHPExcel_IOFactory::createReader ( 'CSV' );
					}
					$obj = $objReader->load ( $file_name, $encode = 'utf-8' );
					$currentSheet = $obj->getSheet ( 0 ); // 读取excel文件中的第一个工作表
					$highestRow = $currentSheet->getHighestRow (); // 取得总行数
					$highestColumn = $currentSheet->getHighestColumn (); // 取得总列数
					$erp_orders_id = array (); // 声明数组
					// echo $allRow;exit;
					/**
					 * 从第二行开始输出，因为excel表中第一行为列名
					 */
					if ($highestColumn == 'G') {
						$snum = 1;
						$fnum = 1;
						$rnum = 1;
						$ernum = 1;
						$return ['snum'] = 0;
						$return ['fnum'] = 0;
						$return ['ernum'] = 0;
						$return ['rnum'] = 0;
						$return ['status'] = 3;
						for($i = 2; $i <= $highestRow; $i ++) {
							$data ['phone'] = $obj->getActiveSheet ()->getCell ( "A" . $i )->getValue (); // 手机号
							$data ['real_name'] = $obj->getActiveSheet ()->getCell ( "B" . $i )->getValue (); // 姓名
							$data ['card_number'] = $obj->getActiveSheet ()->getCell ( "C" . $i )->getValue (); // 身份证
							$data ['origin_sex'] = $obj->getActiveSheet ()->getCell ( "D" . $i )->getValue (); // 性别
							$data ['age'] = $obj->getActiveSheet ()->getCell ( "E" . $i )->getValue (); // 年龄
							if(trim($data ['origin_sex'])=='男'){
								$data ['sex']=1;
							}else{
								$data ['sex']=2;
							}
							$data ['origin_manage_locations'] = $obj->getActiveSheet ()->getCell ( "F" . $i )->getValue (); // 所属县
							$data['manage_locations']=$data ['origin_manage_locations'];//所属县
							$data ['work_event'] = $obj->getActiveSheet ()->getCell ( "G" . $i )->getValue (); // 工作事项
							//$data ['status'] = $obj->getActiveSheet ()->getCell ( "H" . $i )->getValue (); // 状态
							$data ['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'),UC_AUTH_KEY);
							//$data ['superior_id'] = $REQUEST['superior_id']; //当前用户uid
							$data ['role_id'] =$this->roleId; // 根据type
							$re = $this->saleModel->add ( $data );
							if ($re ['status'] == 1) {
								$return ['snum'] = $snum ++;
							} else if ($re ['status'] == 2) {
								$return ['fnum'] = $fnum ++;
							} else if ($re ['status'] == 3) {
								$return ['rnum'] = $rnum ++;
								$return ['rdate'] [] ['data'] = $re ['data'];
							}
						}
						echo json_encode ( $return);
						exit;
					} else {
						$return ['status'] = 4;
						$return ['msg'] = '导入表格错误';
						echo json_encode ( $return );
						exit;
					}
				}else{
					$return ['status'] = 4;
					$return ['msg'] = '导入表格错误';
					echo json_encode ( $return );
					exit;
				}
		
			}else{
				$return ['status'] = 4;
				$return ['msg'] = '导入表格错误';
				echo json_encode ( $return );
				exit;
			}
		}else{
			$this->display ();
		}
	}
	/**
	 * 修改个人资料
	 * @param mixed $post
	 */
	public function editMyself(){
		if (IS_POST) {
			$REQUEST = ( array ) I ( 'request.' );
			$data ['phone'] = intval($REQUEST ['phone']);
			$data ['real_name'] = $REQUEST ['real_name'];
			$data ['card_number'] = $REQUEST ['card_number'];
			$data ['sex'] = $REQUEST ['sex'];
			$data ['age'] = $REQUEST ['age'];
			$re = SalemanagerHelper::upload();//附件上传(包括身份证正反面，头像),字段名称跟数据库字段一样即可
			if($re){
				foreach ($re as $key=>$url){
					$data[$key] = $url;
				}
			}
			$result = $this->saleModel->add ( $data );
			responseJson ( $result );
		} else {
			$this->display ();
		}
	}
	
	private function setParamters($key, $value) {
		$this->paramters [$key] = $value;
	}
	private function getParamters($key) {
		return $this->paramters [$key];
	}
}