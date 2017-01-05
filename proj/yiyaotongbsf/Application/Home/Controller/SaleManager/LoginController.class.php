<?php

namespace Home\Controller\SaleManager;

use Home\Controller\SaleManager\BasicController;
use Home\Model\SaleManager\City\SaleManagerModel;
use Home\Model\SaleManager\Clinic\ClinicModel;
use Home\Common\SalemanagerHelper;
class LoginController extends BasicController{
	protected  $saleMod;
	function _initialize() {
		$this->saleMod = new SaleManagerModel();
		parent::_initialize($this->saleMod);
	}
	
	public function login(){
		if(IS_POST){
			//判断用户名和密码
			$REQUEST = ( array ) I ( 'request.' );
			//查询电话
			if(is_numeric($REQUEST['phone'])&&!empty($REQUEST['password'])){
				$tuser = $this->saleMod->findInfobyCondition(array('phone'=>$REQUEST['phone']));
				if(!empty($tuser)){
					if($tuser[0]['password']===think_ucenter_md5($REQUEST['password'],UC_AUTH_KEY)){//登录成功
						$sesseionmane = C('SESSION_PREFIX').'_salemanager';
						session($sesseionmane,null);
						session($sesseionmane,$tuser[0]);
						//判断是否第一次登录
						if($tuser[0]['is_login']==0){ //第一次登录
							$this->redirect('/Home/SaleManager/Login/changePassword','',0,'');

						}else{//bu shi
							$url = SalemanagerHelper::redirectUrl($tuser[0]['role_id'])."index";
							$this->redirect($url,'',0,'');
						}
						
					}else{
						$this->assign('status','error');
						$this->assign('msg','密码错误！');
					}
					
				}else{
					$this->assign('status','error');
					$this->assign('msg','该手机号没有注册！');
				}			
			}else{
				$this->assign('status','error');
				$this->assign('msg','请输入正确的帐号密码！');
			}
			$this->assign('data',$REQUEST);
			$this->display();
		}else{
			$this->display();
		}	
	}
	
	public function findPassword(){
		if(IS_AJAX){
			$REQUEST = ( array ) I ( 'request.' );
			if(!empty($REQUEST['password']) &&!empty($REQUEST['mobile'])){
				$data['password']=think_ucenter_md5($REQUEST['password'],UC_AUTH_KEY);
				$re = $this->saleMod->updateFieldByCondition(array('phone'=>$REQUEST['mobile']), $data);
				if($re['status']==1){
					responseJson(1, '密码修改成功！');
				}else{
					responseJson(1, '密码修改失败！');
				}
			}else{
				responseJson(1, '参数错误！');
			}
		}else{
			$this->display();
		}
		
	}
	
	/**
	 * 退出操作
	 */
	public function loginOut(){
		$sesseionmane = C('SESSION_PREFIX').'_salemanager';
		session($sesseionmane,null);
		if(IS_AJAX){
			responseJson(3001, '退出成功!','/Home/SaleManager/Login/login');
		}else{
			$this->redirect('/Home/SaleManager/Login/login','',0,'');
		}
		
	}
	
	/**
	 * 修改密码
	 */
	public function changePassword(){
		$sesseionmane = C('SESSION_PREFIX').'_salemanager';
		//print_r(session($sesseionmane)['password']);
		if(IS_AJAX){
			$REQUEST = ( array ) I ( 'request.' );
			if(!empty($REQUEST['originpasswd']) &&!empty($REQUEST['password'])){
				$originpassword=think_ucenter_md5($REQUEST['originpasswd'],UC_AUTH_KEY);
				$data['password']=think_ucenter_md5($REQUEST['password'],UC_AUTH_KEY);
				$data['is_login']=1;
				if(session($sesseionmane)['password']==$originpassword){
					$re = $this->saleMod->updateFieldByCondition(array('phone'=>session($sesseionmane)['phone']), $data);
					if($re['status']==1){
						$url = SalemanagerHelper::redirectUrl(session($sesseionmane)['role_id'])."index";
						responseJson(1, '密码修改成功！',$url);
					}else{
						responseJson(2, '密码修改失败！');
					}
				}else{
					responseJson(2, '原始密码不正确！');
				}
				
			}else{
				responseJson(2, '参数错误！');
			}	
		}else{
			$this->display();
		}
	}
}
