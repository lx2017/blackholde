<?php

/**
 * 此文件主要用于相关业务逻辑操作
 */
namespace Home\Controller\SaleManager;

use Think\Controller;
use Home\Controller\BaseController;
use Home\Controller\LoginAndRegister\LoginController;
use Home\Model\SaleManager\County\SaleManagerModel;
use Home\Model\LoginAndRegister\CookieModel;
use Home\Common\SalemanagerHelper;
use Home\Model\SaleManager\NoticeModel;
use Home\Model\SaleManager\SalemanApplyModel;
use Home\Model\SaleManager\Clinic\ClinicModel;
use \Think\Model;

/**
 * 该文件用于公共操作
 */
class BasicController extends BaseController {
	protected $actions;
	protected $saleModel;
	protected $paramtersMod;
	protected $roleId;
	static $_userinfo;
	protected $lowerRoleId;
	static $user;
	protected $noticeModel;
	protected $saleApplyModel;
	protected  $clinicMod;
	protected  $saleOrderModel;
	protected  $saleTripModel;
	protected  $saleSignModel;
	
	/**
	 * 初始化
	 * @param unknown $key
	 */
	function _initialize($key) {
		$this->saleModel = $key;
		parent::_initialize ();
		self::$user = session(C('SESSION_PREFIX').'_salemanager');
		$user = self::$user;
			//session 判断
			if(strtoupper(ACTION_NAME)!='LOGIN' && strtoupper(ACTION_NAME)!='FINDPASSWORD' && IS_AJAX==false){
				if(empty(self::$user)){
					$this->redirect('/Home/SaleManager/Login/login');
				}
			}
			$this->lowerRoleId =SalemanagerHelper::getLowerRoleId($user['id']);
			$user['id'] = think_ucenter_encrypt($user['id']);
			$this->assign('myself',$user);
			$this->noticeModel = new NoticeModel();
			$this->saleApplyModel = new SalemanApplyModel();
			$this->clinicMod = new ClinicModel();
			$this->saleOrderModel = new \Home\Model\SaleManager\SalemanOrderModel();
			$this->saleSignModel = new \Home\Model\SaleManager\SalemanSignModel();
			$this->saleTripModel = new \Home\Model\SaleManager\SalemanTripModel();
			//AJAX提交，判断是否存在session，不存在session，返回相应的code
			if(IS_AJAX){
				if(empty($user)){
					responseJson(2028, C('SALEMANAGER_CODE.2028'),'/Home/SaleManager/Login/login');
				}
			}
	}
	
	/**
	 * 当前用户对应的下级列表(例如：省总对应下级列表为地总，以此类推)
	 */
	public function index(){
		$this->assign('lists',$this->saleModel->findInfobyCondition(array('superior_id'=>self::$user['id'],'role_id'=>$this->lowerRoleId,'status'=>C('SALEMANAGER_STATUS.NORMAL'))));
		$this->infoList(); //获取消息列表
		//$this->display();
	}
	
	/**
	 * 获取下级列表
	 */
	public function getLower(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){ //下拉刷新
			if(!empty($REQUEST['pageStart'])){
				$userList = $this->saleModel->findInfobyCondition(array('superior_id'=>self::$user['id'],'role_id'=>$this->lowerRoleId,'status'=>C('SALEMANAGER_STATUS.NORMAL')),C('SALEMANAGER_PAGE_NUM'),$REQUEST['pageStart']);
			}else{
				$userList=array();
			}			
		}else{
			//默认页面渲染
			$userList = $this->saleModel->findInfobyCondition(array('superior_id'=>self::$user['id'],'role_id'=>$this->lowerRoleId,'status'=>C('SALEMANAGER_STATUS.NORMAL')),C('SALEMANAGER_PAGE_NUM'),0);	
		}
		$this->assign('userid',think_ucenter_encrypt($REQUEST['userid']));
		foreach ($userList as $k=>$v){
			$userList[$k]['id']=think_ucenter_encrypt($v['id']);
		}
		return $userList;
	}
	
	/**
	 * 个人信息详情
	 */
	public  function personData(){
		if(IS_POST){
			$re = self::editMyself();
			if($re['status']==1){
				$this->assign('status','success');
				$this->assign('msg',$re['msg']);
				$userid =think_ucenter_decrypt(self::$user['id']);
				$user = $this->saleModel->findInfobyCondition(array("id"=>self::$user['id']));
				$user[0]['id']=$userid;
				$this->assign('user',$user[0]);
				$this->display();
			}else{
				$this->assign('status','error');
				$this->assign('msg',$re['msg']);
				$userid =think_ucenter_decrypt(self::$user['id']);
				$user = $this->saleModel->findInfobyCondition(array("id"=>self::$user['id']));
				$user[0]['id']=$userid;
				$this->assign('user',$user[0]);
				$this->display();
			}
		}else{
			self::detail();
		}
	}

	/**
	 * 我的下级列表(县总下级是业务员，地总下级是县总)
	 */
	public function mySaleMan() {
		if(IS_AJAX){
			$userList = self::getLower();
			return responseJson(200, '刷新成功',$userList);
		}else{
			
			$userList = self::getLower();
			$this->assign('lists',$userList);
			if(self::$user['role_id']!==C('SALE_MANAGER.SALE_COUNTY')){
				if(empty($userList)){
					$url = SalemanagerHelper::redirectUrl(self::$user['role_id'])."noSaleMan";
					$this->redirect($url);
					exit;
				}
				
			}
			$this->display();
		}	
	}
	
	
	/**
	 * 更换业务员（获取业务员列表）
	 */
	public  function changeSale(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_POST){
			$data['saleman_id'] = $REQUEST['select1'];
			$model = new Model();
        		$model->startTrans();
        		$re = self::changeLower($this->clinicMod, array('saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user'])), $data);//更换诊所
        		$applyCondition=array(
        				'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		$apply2Condition=array(
        				'superior_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		//申请支持更换
        		$appRe = $this->saleApplyModel->updateFieldByCondition($applyCondition, array("saleman_id"=>$data['saleman_id']));
        		
        		$app2Re = $this->saleApplyModel->updateFieldByCondition($apply2Condition, array("superior_id"=>$data['saleman_id']));
        			
        		$noticeCondition=array(
        				'send_user_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		$notice2Condition=array(
        				'accept_saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		//消息更换主人
        		$noticeRe = $this->noticeModel->updateFieldByCondition($noticeCondition, array("send_user_id"=>$data['saleman_id']));
        			
        		$notice2Re = $this->noticeModel->updateFieldByCondition($notice2Condition, array("accept_saleman_id"=>$data['saleman_id']));
        			
        		//订单更换
        		$orderCondition=array(
        				'send_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		$order2Condition=array(
        				'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
        		);
        		$orderRe = $this->saleOrderModel->updateFieldByCondition($orderCondition, array("send_id"=>$data['saleman_id']));
        			
        		$order2Re = $this->saleOrderModel->updateFieldByCondition($order2Condition, array("saleman_id"=>$data['saleman_id']));
        			
						
						//签到更换
			$signCondition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$signRe = $this->saleSignModel->updateFieldByCondition($signCondition, array("saleman_id"=>$data['saleman_id']));	
			//工作计划更换
			$tripCondition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$tripRe = $this->saleTripModel->updateFieldByCondition($signCondition, array("saleman_id"=>$data['saleman_id']));
			//被替换的人设置为禁用	
			$saleResult = $this->saleModel->updateField (think_ucenter_decrypt($REQUEST['origin_user']),array('status'=>C('SALEMANAGER_STATUS.FORBIDDEN')) );
			if($re['status']==1&&$appRe['status']==1&& $app2Re['status']==1 &&$saleResult['status']==1&&$noticeRe['status']==1&&$notice2Re['status']==1&&$orderRe['status']==1&&$order2Re['status']==1&&$signRe['status']==1&&$tripRe['status']==1){
				$model->commit();
				//根据角色跳转
				$url = SalemanagerHelper::redirectUrl(self::$user['role_id'])."mySaleMan";
				$this->redirect($url);
				//redirect('../../mySaleMan');
			}else{
				$model->rollback();
				$this->assign('status','error');
				$this->assign('msg','更换出错');
				$this->display();
			}
		}else{
			$userList = self::getLower();
			$this->assign('lists',$userList);
			$this->assign('userid',$REQUEST['userid']);
			if(empty($userList) || ($userList[0]['id']==$REQUEST['userid']&&count($userList)==1)){
				$url = SalemanagerHelper::redirectUrl(self::$user['role_id'])."noSaleMan";
				$this->redirect($url);
				//$this->display('noSaleMan');
			}
			$this->display();
		}
	}
	
	/**
	 * 删除业务员
	 */
	public function delSaleMan(){
		$REQUEST = ( array ) I ( 'request.' );
		$saleId=think_ucenter_decrypt($REQUEST['userid']);
		if(is_numeric($saleId)){
			$re = self::del($this->saleModel, array('id'=>$saleId), array('status'=>C('SALEMANAGER_STATUS.DELETED')));
			if($re!==false){
				responseJson($re['status'],$re['msg'],$re['data']);
			}else{
				responseJson($re['status'],$re['msg'],$re['data']);
			}
				
		}else{
			responseJson($re['status'],$re['msg'],$re['data']);
		}
	}
	
	/**
	 * 添加用户(包括添加业务员，地总，省总)
	 * @param mixed $post        	
	 */
	public function addSaleman() {
		if (IS_AJAX) {
			$REQUEST = ( array ) I ( 'request.' );
			$data ['phone'] = intval($REQUEST ['phone']);
			$data ['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'),UC_AUTH_KEY);
			$data ['real_name'] = $REQUEST ['real_name'];
			$data ['card_number'] = $REQUEST ['card_number'];
			$data ['sex'] = $REQUEST ['sex'];
			$data ['age'] = $REQUEST ['age'];
			$data ['add_time'] = time();
			$data ['superior_id'] = self::$user['id']; //当前用户uid
			$data ['role_id'] = $this->lowerRoleId; // 根据type
			$data ['manage_locations'] = $REQUEST ['belong_locations'];
			$re = SalemanagerHelper::upload();//附件上传
			if($re){
				foreach ($re as $key=>$url){
					$data[$key] = $url;
				}
			}
			$result = $this->saleModel->add ( $data );
			 //$this->ajaxReturn($result);
			responseJson($result['status'],$result['msg'],$result['data']);
			
		} else {
			$this->display();
		}
	}
	
	/**
	 * 业务员详细信息
	 */
	public  function saleInfos(){
		//self::detail();
		//$this->display();
		$REQUEST = ( array ) I ( 'request.' );
		$userid =think_ucenter_decrypt($REQUEST['userId']);
		if(is_numeric($userid)){
			$user = $this->saleModel->findInfobyCondition(array("id"=>$userid));
			$user[0]['id']=$userid;
			$user[0]['card_number']=substr_replace($user[0]['card_number'],'*********',3,9);
			$this->assign('user',$user[0]);
			$this->display();
		}else{
			$this->assign('status','validatefail');
			$this->assign('msg','参数错误！');
			$this->display();
			//$this->error('错误!');
		}
	}
	
	/**
	 *   支持审批
	 */
	public function appliceSupport(){
		if(IS_POST){
			self::saleApply();
		}else{
			$this->assign('app_type',C('SALEMANAGER_APPLY_NAME'));
			$this->display();
		}
	
	}
	
	
	
	
	
	
	
	
	/**
	 * 每个用户详细信息的获取
	 */
	private function detail(){
		$REQUEST = ( array ) I ( 'request.' );
		$userid =think_ucenter_decrypt($REQUEST['userId']);
		if(is_numeric($userid)){
			$user = $this->saleModel->findInfobyCondition(array("id"=>$userid));
			$user[0]['id']=$userid;
			$this->assign('user',$user[0]);
			$this->display();
		}else{
			$this->assign('status','validatefail');
			$this->assign('msg','参数错误！');
			$this->display();
			//$this->error('错误!');
		}
	}
	
	
	
	/**
	 * 修改用户(更换下级用户)
	 * @param mixed $post         	
	 */
	public function edit() {
		$REQUEST = ( array ) I ( 'request.' );
		$REQUEST['id']=think_ucenter_decrypt($REQUEST['id']);
		if (IS_POST) {
			if(is_numeric($REQUEST['id'])){
				$data ['phone'] = intval($REQUEST ['phone']);
				$data ['password'] = think_ucenter_md5(C('SALE_MANAGER.INIT_PASSWORD'),UC_AUTH_KEY);
				$data ['real_name'] = $REQUEST ['real_name'];
				$data ['card_number'] = $REQUEST ['card_number'];
				$data ['sex'] = $REQUEST ['sex'];
				$data ['age'] = $REQUEST ['age'];
										//所属县
				$result = $this->saleModel->updateById ($REQUEST['id'],$data );
			}else{
				$result =array(
						'status'=>C('SALE_MANAGER.ID_VALIDATE_FAIL_STATUS'),
						'msg'=>C('SALE_MANAGER.ID_VALIDATE_FAIL_MSG'),
				);
			}
			responseJson ( $result );
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
			$data ['id'] = self::$user['id'];
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
			$result = $this->saleModel->updateField (self::$user['id'],$data );
			//资料更新，更新session
			if($result['status']==1){
				$tuser = $this->saleModel->findInfobyCondition(array('id'=>self::$user['id']));
				$sesseionmane = C('SESSION_PREFIX').'_salemanager';
				session($sesseionmane,$tuser[0]);
			}
			if(!empty($REQUEST['f']) && $REQUEST['f']=='json'){
				responseJson ($re['status'],$re['msg'],$re['data'] );
			}else{
				return $result;
			}
		} else {
			$this->display ();
		}
	}
	
	/**
	 * 更换下级
	 * @param mixed $post
	 */
	public function changeLower($model,$where,$data){
		if(empty($where)){
			return false;
		}
		return $model->updateFieldByCondition($where,$data);
	}
	
	/**
	 * 消息列表
	 * @param mixed $post
	 */
	public function infoList(){
		$REQUEST = ( array ) I ( 'request.' );
			$totalMsg = $this->noticeModel->findInfobyCondition(array('is_look'=>0,'accept_saleman_id'=>self::$user['id']),NULL,NULL,'send_time desc');
			$lowers = $this->saleModel->findInfobyCondition(array('superior_id'=>self::$user['id']));
			$lowerIds=array();
			foreach ($lowers as $lk=>$lv){
				$lowerIds[$lv['id']]=$lv['id'];
			}
			$superMsg=array();
			$lowerMsg=array();
			
			foreach ($totalMsg as $k=>$v){
				if($v['send_user_id']==self::$user['superior_id']){
					$u = $this->saleModel->findById($v['send_user_id']);
					$v['send_time_data'] = SalemanagerHelper::linfolist($v['send_time']);
					$v['send_name']= $u['real_name'];
					$v['head_img']= $u['head_img'];
					array_push($superMsg,$v);
				}
				if(array_key_exists($v['send_user_id'], $lowerIds)){
					$u = $this->saleModel->findById($v['send_user_id']);
					$v['send_time_data'] = SalemanagerHelper::linfolist($v['send_time']);
					//$v['send_time '] = \Home\Common\SalemanagerHelper::timeFormat($v['send_time']);
					$v['send_name']= $u['real_name'];
					array_push($lowerMsg,$v);
				}
			}
			$this->assign('supercount',count($superMsg));//上级条数
			$this->assign('lowercount', count($lowerMsg));//下级条数
			$this->assign('supertime',$superMsg[0]['send_time_data']);//上级最后时间
			$this->assign('lowertime', $lowerMsg[0]['send_time_data']);//下级最后时间
			$this->assign('supercount',count($superMsg));//上级条数
			$this->assign('lowercount', count($lowerMsg));//下级条数
			if(!empty($REQUEST['tag'])){
				if($REQUEST['tag']=='upinfo'){
					$this->assign('info',$superMsg);//上级消息
				}
				if($REQUEST['tag']=='downinfo'){
					$this->assign('info',$lowerMsg);//上级消息
				}
			}else{
				if(!empty($superMsg)){
					$this->assign('lastupinfo',$superMsg[0]['send_name']);//上级最近时间的一条
				}
				if(!empty($lowerMsg)){
					$this->assign('lastdowninfo',$lowerMsg[0]['send_name']);//下级最近时间的一条
				}
				$this->assign('super',$superMsg);//上级消息
				$this->assign('lower',$lowerMsg);//下级消息
				$this->assign('total', count($totalMsg));//下级条数
			}
			
			$this->display();
	}
	
	/**
	 * 申请审批
	 */
	private function saleApply(){
		if(IS_POST){
			$REQUEST = ( array ) I ( 'request.' );
			$data['saleman_id']= self::$user['id'];
			$tmp = SalemanagerHelper::getSaleHeadById(self::$user['superior_id'],0);
			$data['apply_content']= $REQUEST['apply_content'];
			$data['type']= $REQUEST['type'];
			$data['superior_id']= self::$user['superior_id'];
			$data['apply_time'] = time();
			$data['copy_id'] = $tmp['id'];
			$re =$this->saleApplyModel->add($data);
			responseJson($re['status'], $re['msg'],$re['data']);
		}		
	}
	/**
	 * 审批记录
	 */
	public function approvalRecord(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){
			if(!empty($REQUEST['pageStart'])){
				if($REQUEST['itemIndex']==0){
					//我发起的下拉刷新
					unset($applyByMe);
					$applyByMe = $this->saleApplyModel->findInfobyCondition(array('saleman_id'=>self::$user['id']),C('SALEMANAGER_PAGE_NUM'),$REQUEST['pageStart'],'apply_time desc');
				}
				if($REQUEST['itemIndex']==1){
					//待审批下拉刷新
					unset($applyByMe);
					$applyByMe = $this->saleApplyModel->findInfobyCondition(array('superior_id'=>self::$user['id'],'reply_status'=>'0'),C('SALEMANAGER_PAGE_NUM'),$REQUEST['pageStart'],'apply_time desc');
				}
				if($REQUEST['itemIndex']==2){
					//已审批刷新
					unset($applyByMe);
					$applyByMe = $this->saleApplyModel->findInfobyCondition('superior_id='.self::$user["id"].' and reply_status<>0 and type<>8',C('SALEMANAGER_PAGE_NUM'),$REQUEST['pageStart'],'apply_time desc');
				}
				foreach($applyByMe as $k=>$v){
					$applyByMe[$k]['id']=think_ucenter_encrypt($v['id']);
					if($REQUEST['itemIndex']!=0){
						$applyByMe[$k]['apply_name'] = $this->saleModel->findInfobyCondition(array('id'=>$applyByMe[$k]['saleman_id']))[0]['real_name'];
					}else{
						$applyByMe[$k]['apply_name'] = self::$user['real_name'];
					}
					$applyByMe[$k]['apply_cstatus']=SalemanagerHelper::convertApplyStatus($applyByMe[$k]['reply_status']);//同意，拒绝，待审批
					$applyByMe[$k]['ctype']=SalemanagerHelper::traMetting($applyByMe[$k]['type']); //申请类型
					$applyByMe[$k]['apply_time']=date ( 'Y-m-d', $applyByMe[$k]['apply_time'] ); //申请时间
					
				}
			}	
			responseJson(200, '下拉刷新成功',$applyByMe);
		}else{
			//我发起的
			$applyByMe = $this->saleApplyModel->findInfobyCondition(array('saleman_id'=>self::$user['id']),C('SALEMANAGER_PAGE_NUM'),0,'apply_time desc');
			foreach($applyByMe as $k=>$v){
				$applyByMe[$k]['id']=think_ucenter_encrypt($v['id']);
				$applyByMe[$k]['apply_name'] = self::$user['real_name'];
				$applyByMe[$k]['apply_cstatus']=SalemanagerHelper::convertApplyStatus($applyByMe[$k]['reply_status']);//同意，拒绝，待审批
				$applyByMe[$k]['ctype']=SalemanagerHelper::traMetting($applyByMe[$k]['type']); //申请类型
			}
			//$Approve = $this->saleApplyModel->findInfobyCondition(array('superior_id'=>self::$user['id']),null,null,'apply_time desc');
			$waitApp = array();
			$proved	= array();
			$proved = $this->saleApplyModel->findInfobyCondition('superior_id='.self::$user["id"].' and reply_status<>0 and type<>8',C('SALEMANAGER_PAGE_NUM'),0,'apply_time desc');
			$waitApp = $this->saleApplyModel->findInfobyCondition(array('superior_id'=>self::$user['id'],'reply_status'=>0),C('SALEMANAGER_PAGE_NUM'),0,'apply_time desc');
			foreach($proved as $k=>$v){//已审批
				$proved[$k]['id']=think_ucenter_encrypt($v['id']);
				$proved[$k]['apply_name'] = $this->saleModel->findInfobyCondition(array('id'=>$proved[$k]['saleman_id']))[0]['real_name'];
				$proved[$k]['apply_cstatus']=SalemanagerHelper::convertApplyStatus($proved[$k]['reply_status']);
				$proved[$k]['ctype']=SalemanagerHelper::traMetting($proved[$k]['type']);
			}
			foreach($waitApp as $k=>$v){//待审批
				$waitApp[$k]['id']=think_ucenter_encrypt($v['id']);
				$waitApp[$k]['apply_name'] = $this->saleModel->findInfobyCondition(array('id'=>$waitApp[$k]['saleman_id']))[0]['real_name'];
				$waitApp[$k]['apply_cstatus']=SalemanagerHelper::convertApplyStatus($waitApp[$k]['reply_status']);
				$waitApp[$k]['ctype']=SalemanagerHelper::traMetting($waitApp[$k]['type']);
			}
			$this->assign('applyByMe',$applyByMe);
			$this->assign('approved',$proved);
			$this->assign('waitProve',$waitApp);
			$this->display();
		}
		
	}
	
	/**
	 * 审批详情
	 */
	public function approvalInfo(){
		$REQUEST = ( array ) I ( 'request.' );
		$applyId =think_ucenter_decrypt($REQUEST['applyId']);
		if(is_numeric($applyId)){
			$appinfo= $this->saleApplyModel->findById($applyId);
			$appinfo['apply_time'] = date ( 'Y-m-d H:i:s', $appinfo['apply_time'] );
			$applyUser = $this->saleModel->findById($appinfo['saleman_id']);
			$appinfo['apply_cstatus']=SalemanagerHelper::convertApplyStatus($appinfo['reply_status']);
			$appinfo['ctype']=SalemanagerHelper::traMetting($appinfo['type']);
			$appinfo['apply_name'] = $applyUser['real_name'];
			$appinfo['ids'] =think_ucenter_encrypt($appinfo['id']);
			$appinfo['saleman_ids'] =think_ucenter_encrypt($appinfo['saleman_id']);
			$this->assign('appinfo',$appinfo);
			$this->display();
		}else{
			$this->assign('status','error');
			$this->assign('msg','参数错误！');
			$this->display();
		}
	}
	
	/**
	 *审批操作
	 */
	public function approvalOpinionOne(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){
			$data['reply_status']=$REQUEST['reply_status'];
			$data['reply_content']=$REQUEST['reply_content'];
			$data['reply_time']=$REQUEST['reply_time'];
			$re = $this->saleApplyModel->updateField(think_ucenter_decrypt($REQUEST['applyId']), $data);
			 //$this->redirect('/home/SaleManager/County/index');
			responseJson($re['status'], $re['msg'],$re['data']);
		}else{
			$applyId =think_ucenter_decrypt($REQUEST['applyId']);
			if(is_numeric($applyId)){
				$appinfo= $this->saleApplyModel->findById($applyId);
				$applyUser = $this->saleModel->findById($appinfo['saleman_id']);
				$appinfo['apply_time'] = date ( 'Y-m-d H:i:s', $appinfo['apply_time'] );
				$appinfo['apply_cstatus']=SalemanagerHelper::convertApplyStatus($appinfo['reply_status']);
				$appinfo['ctype']=SalemanagerHelper::traMetting($appinfo['type']);
				$appinfo['apply_name'] = $applyUser['real_name'];
				$appinfo['ids'] =think_ucenter_encrypt($appinfo['id']);
				$appinfo['saleman_ids'] =think_ucenter_encrypt($appinfo['saleman_id']);
				$this->assign('appinfo',$appinfo);
				if($appinfo['ctype']=='离职申请'){
					$userList = self::getLower();
					foreach ($userList as $k=>$v){
						if($v['id']==think_ucenter_encrypt($appinfo['saleman_id'])){
							unset($userList[$k]);
							continue;
						}
					}
					$this->assign('lists',$userList);
					$this->display('approveopinion');
				}else{
					$this->display();
				}
				
			}else{
				$this->assign('status','error');
				$this->assign('msg','参数错误！');
				$this->display();
			}
		}
		
	}
	
	//离职申请处理
	public function approveOpinion(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){
			$changeUser = think_ucenter_decrypt($REQUEST['change_apply']);//交接人
			$originUser = $REQUEST['origin_user'];//离职人
			$data['reply_status']=$REQUEST['reply_status'];//同意，不同意
			$reApplyStatus = $this->saleApplyModel->updateField((think_ucenter_decrypt($REQUEST['applyId'])), $data);
			$data['saleman_id'] = $changeUser;
			$model = new Model();
			$model->startTrans();
			$re = self::changeLower($this->clinicMod, array('saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user'])), $data);//更换诊所
			$applyCondition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$apply2Condition=array(
					'superior_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			//申请支持更换
			$appRe = $this->saleApplyModel->updateFieldByCondition($applyCondition, array("saleman_id"=>$data['saleman_id']));
		
			$app2Re = $this->saleApplyModel->updateFieldByCondition($apply2Condition, array("superior_id"=>$data['saleman_id']));
			
			$noticeCondition=array(
					'send_user_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$notice2Condition=array(
					'accept_saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			//消息更换主人
			$noticeRe = $this->noticeModel->updateFieldByCondition($noticeCondition, array("send_user_id"=>$data['saleman_id']));
			
			$notice2Re = $this->noticeModel->updateFieldByCondition($notice2Condition, array("accept_saleman_id"=>$data['saleman_id']));
			
			//订单更换
			$orderCondition=array(
					'send_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$order2Condition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$orderRe = $this->saleOrderModel->updateFieldByCondition($orderCondition, array("send_id"=>$data['saleman_id']));
			
			$order2Re = $this->saleOrderModel->updateFieldByCondition($order2Condition, array("saleman_id"=>$data['saleman_id']));
			
			//签到更换
			$signCondition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$signRe = $this->saleSignModel->updateFieldByCondition($signCondition, array("saleman_id"=>$data['saleman_id']));
			
			//工作计划更换
			$tripCondition=array(
					'saleman_id'=>think_ucenter_decrypt($REQUEST['origin_user']),
			);
			$tripRe = $this->saleTripModel->updateFieldByCondition($tripCondition, array("saleman_id"=>$data['saleman_id']));
			
			//被替换的人设置为禁用
			$saleResult = $this->saleModel->updateField (think_ucenter_decrypt($REQUEST['origin_user']),array('status'=>C('SALEMANAGER_STATUS.FORBIDDEN')) );
			
			if($re['status']==1&&$appRe['status']==1&& $app2Re['status']==1 &&$saleResult['status']==1&&$noticeRe['status']==1&&$notice2Re['status']==1&&$orderRe['status']==1&&$order2Re['status']==1&&$signRe['status']==1&&$tripRe['status']==1&&$reApplyStatus['status']==1){
				$model->commit();
				//根据角色跳转
				responseJson(1, '交接成功！');
			}else{
				$model->rollback();
				responseJson(2, '交接失败！');
			}
			responseJson(2, '交接失败！');
		}
	
	}
	/**
	 * 删除操作，假删除
	 * @param mixed $post
	 */
	public function del($model,$where,$data){
		if(empty($where)){
			return false;
		}
		return $model->updateFieldByCondition($where,$data);
	}
	
	/**
	 * 订单（当前用户下级收到的订单，包括业务员收到订单，县总收到订单）
	 * @param mixed $post
	 */
	public function orderlist(){
		//获取当前用户的下级收到订单
		$REQUEST = ( array ) I ( 'request.' );
		$status = $REQUEST['status'];
		if(isset($REQUEST['status']) && $REQUEST['status']!=""){
			$sql=' and s.status='.$status;
		}
		if(IS_AJAX){
			if(isset($REQUEST['pageStart'])){
				//总部，销售部省总订单
				if(self::$user['role_id']==C('SALE_MANAGER.SALE_HEAD')||self::$user['role_id']==C('SALE_MANAGER.SALE_DEPARTMENT')){
					$re = M('saleman')->field('s.id as oid,s.add_date,s.status,s.total_price,ymt_saleman.role_id,ymt_saleman.real_name,ymt_saleman.id as sid,ymt_saleman.head_img')->where('ymt_saleman.role_id in ('.C('SALE_MANAGER.SALE_DEPARTMENT').','.C('SALE_MANAGER.SALE_PROVINCE').') and s.type=1')
					->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')->limit($REQUEST['pageStart'],C('SALEMANAGER_PAGE_NUM'))
					->select();
				}else{
					$re = M('saleman')->field('s.id as oid,s.add_date,s.status,s.total_price,ymt_saleman.role_id,ymt_saleman.real_name,ymt_saleman.id as sid,ymt_saleman.head_img')->where('ymt_saleman.superior_id='.self::$user['id'].$sql.' and s.type=1')
					->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')->limit($REQUEST['pageStart'],C('SALEMANAGER_PAGE_NUM'))
					->select();
				}
 				//echo M()->getLastSql();
				foreach ($re as $key=>$value){
					$re[$key]['id']=think_ucenter_encrypt($value['id']);
					$re[$key]['osid']=think_ucenter_encrypt($value['oid']);
					$re[$key]['status']=SalemanagerHelper::convertOrderStatus($value['status']);
					$re[$key]['role_name']=SalemanagerHelper::getRoleName($value['role_id']);
				}
			}
			//echo M()->getLastSql();
			responseJson(200,'刷新成功',$re);
		}else{
			$sumPrice=0;
			//总部，销售部省总订单总数
			if(self::$user['role_id']==C('SALE_MANAGER.SALE_HEAD')||self::$user['role_id']==C('SALE_MANAGER.SALE_DEPARTMENT')){
				$res = M('saleman')->field('s.total_price')->where('ymt_saleman.role_id in ('.C('SALE_MANAGER.SALE_DEPARTMENT').','.C('SALE_MANAGER.SALE_PROVINCE').') and s.type=1')
				->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')
				->select();
			
			}else{
				//大区经理,省总,县总，地总订单总数
				$res = M('saleman')->field('s.total_price')->where('ymt_saleman.superior_id='.self::$user['id'].$sql.' and s.type=1')
				->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')
				->select();
			}
			foreach ($res as $key=>$value){
				$sumPrice=$sumPrice+intval($value['total_price']);
			}
			//总部，销售部省总订单
			if(self::$user['role_id']==C('SALE_MANAGER.SALE_HEAD')||self::$user['role_id']==C('SALE_MANAGER.SALE_DEPARTMENT')){
				$re = M('saleman')->field('s.id as oid,s.add_date,s.status,s.total_price,ymt_saleman.role_id,ymt_saleman.head_img,ymt_saleman.real_name,ymt_saleman.id as sid')->where('ymt_saleman.role_id in ('.C('SALE_MANAGER.SALE_DEPARTMENT').','.C('SALE_MANAGER.SALE_PROVINCE').') and s.type=1')
				->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')->limit(0,C('SALEMANAGER_PAGE_NUM'))
				->select();
			}else{
				//大区经理省总订单
				$re = M('saleman')->field('s.id as oid,s.add_date,s.status,s.total_price,ymt_saleman.role_id,ymt_saleman.head_img,ymt_saleman.real_name,ymt_saleman.id as sid')->where('ymt_saleman.superior_id='.self::$user['id'].$sql.' and s.type=1')
				->join('inner join ymt_saleman_order  s ON s.send_id = ymt_saleman.id')->order('s.add_date desc')->limit(0,C('SALEMANAGER_PAGE_NUM'))
				->select();
			}
			
			//echo M()->getLastSql();exit;
			foreach ($re as $key=>$value){
				$re[$key]['id']=think_ucenter_encrypt($value['id']);
				$re[$key]['osid']=think_ucenter_encrypt($value['oid']);
				$re[$key]['status']=SalemanagerHelper::convertOrderStatus($value['status']);
				$re[$key]['role_name']=SalemanagerHelper::getRoleName($value['role_id']);
			}
			$this->assign('title',SalemanagerHelper::getRoleTitle(self::$user['role_id']));
			$this->assign('toralprice',$sumPrice);
			$this->assign('total',count($res));
			$this->assign('lists',$re);
			$this->display('SaleManager/County/clinicorder');
		}
		
	}
	
	public function orderInfo(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_POST){
			
		}else{
			if(!empty($REQUEST['oid'])){
				$info = M('saleman_order_detail')->where(array('order_id'=>think_ucenter_decrypt($REQUEST['oid'])))->select();
				foreach ($info as $k=>$v){
					$total+=$v['total_price'];
				}
			}
			$this->assign('total',$total);
			$this->assign('oid',$REQUEST['oid']);
			$this->assign('status',$REQUEST['status']);
			$this->assign('info',$info);
			$this->display();
		}	
	}
	//完成已完成订单
	public function completOrder(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){
			if(!empty($REQUEST['oid'])){
				$re = M('saleman_order')->where('id='.think_ucenter_decrypt($REQUEST['oid']))->data(array('status'=>1))->save();
				if($re!==false){
					responseJson(200, '操作成功');
				}else{
					responseJson(2001, '操作失败');
				}
			}
			responseJson(2001, '参数错误');
		}
	}
	
	/**
	 * 消息推送(上级通知)
	 */
	public function pushHistory(){
		if(IS_POST){
			
		}else{
			$userList = self::getLower();
			$this->assign('lists',$userList);
			$this->display();
		}
	}
	private function setParamters($key, $value) {
		$this->paramters [$key] = $value;
	}
	private function getParamters($key) {
		return $this->paramters [$key];
	}
}