<?php

namespace Home\Controller\SaleManager;

use Home\Controller\SaleManager\BasicController;
use Home\Model\SaleManager\County\SaleManagerModel;
use Home\Model\SaleManager\Clinic\ClinicModel;
use Home\Model\SaleManager\DoctorModel;
use Home\Model\SaleManager\UsersModel;
class CountyController extends BasicController{
	protected  $clinicMod; 
	private $saleMod;
	protected  $doctorMod;
	protected  $userMod;
	function _initialize() {
		$this->clinicMod = new ClinicModel();
		$this->saleMod = new SaleManagerModel();
		$this->doctorMod = new DoctorModel();
		$this->userMod = new UsersModel();
		parent::_initialize($this->saleMod);
	}
	
	/**
	 * 工作计划
	 */
	public function workPlan(){
		$this->display();
	}
	
	/**
	 * 目标诊所
	 */
	public function targetClinic(){
		$REQUEST = ( array ) I ( 'request.' );
		if(IS_AJAX){
			if(!empty($REQUEST['pageStart'])){
				$lists = $this->clinicMod->findInfobyCondition(array('saleman_id'=>self::$user['id'],'type'=>C('SALEMANAGER_CLINIC_TYPE.AIM'),'is_delete'=>C('SALEMANAGER_CLINIC_IDDELETE.NORMAL')),C('SALEMANAGER_PAGE_NUM'),$REQUEST['pageStart'],'add_time desc');		
				foreach ($lists as $k=>$v){
					$map = D('doctor')->where('clinic_id='.$v['id'])->field('name')->find();
					$lists[$k]['id']=think_ucenter_encrypt($v['id']);
					$lists[$k]['manager_name']=$map['name'];
				}
			}
			responseJson(200, '刷新成功',$lists);
		}else{
			$lists = $this->clinicMod->findInfobyCondition(array('saleman_id'=>self::$user['id'],'type'=>C('SALEMANAGER_CLINIC_TYPE.AIM'),'is_delete'=>C('SALEMANAGER_CLINIC_IDDELETE.NORMAL')),C('SALEMANAGER_PAGE_NUM'),0,'add_time desc');
			if(empty($lists)){
				$this->redirect('/Home/SaleManager/County/notargetclinic/','',0,'');
			}
			foreach ($lists as $k=>$v){
				$map = D('doctor')->where('clinic_id='.$v['id'])->field('name')->find();
				$lists[$k]['id']=think_ucenter_encrypt($v['id']);
				$lists[$k]['manager_name']=$map['name'];
			}
			//print_r($lists);
			$this->assign('lists',$lists);
			$this->display();
		}
		
	}
	
	/**
	 *  添加目标诊所
	 */
	public function addTargetClinic(){
		if(IS_POST){
			$REQUEST = ( array ) I ( 'request.' );
			$data['clinic_name']=$REQUEST['clinic_name']; //诊所名称
			$data['clinic_phone']=$REQUEST['clinic_phone'];  //诊所联系电话
			$data['clinic_address']=$REQUEST['clinic_address']; //地址，自动定位
			$data['saleman_id']=self::$user['id'];//业务员id	
			$data['type']=C('SALEMANAGER_CLINIC_TYPE.AIM');//目标诊所
			$data['is_delete']=C('SALEMANAGER_CLINIC_IDDELETE.NORMAL');
			
			//如果没有输入负责人，则只插入诊所表
			if(!empty($REQUEST['manager_name'])){
				$model = new \Think\Model();
				$model->startTrans();
				$re = $this->clinicMod->add($data);//添加诊所
				if($re['status']==1){
					$manager['clinic_id']=$re['data']['id'];
				}
				$manager['status']=0;
				$manager['name']=$REQUEST['manager_name'];
				$manager['type']=2;//诊所类型
				$manager['is_manager']=1; //不是管理员
				$maRe = $this->doctorMod->add($manager);
				if($re['status']==1&&$maRe['status']==1){
					$model->commit();
					responseJson ($re['status'],$re['msg'],$re['data'] );
				}else{
					$model->rollback();
					responseJson ($re['status'],$maRe['msg'],$re['data'] );
				}
			}else{
				$re = $this->clinicMod->add($data);
			}
			responseJson ($re['status'],$re['msg'],$re['data'] );
		}else{
			$this->display();
		}
	}
	/**
	 * 删除目标诊所
	 */
	public function delClinic(){
		$REQUEST = ( array ) I ( 'request.' );
		$clinId = think_ucenter_decrypt($REQUEST['id']);
		if(is_numeric($clinId)){
			$model = new \Think\Model();
			$model->startTrans();
			$re = $this->clinicMod->updateFieldByCondition(array('id'=>$clinId), array('is_delete'=>C('SALEMANAGER_CLINIC_TYPE.DELETE'),'type'=>C('SALEMANAGER_CLINIC_TYPE.AIM')));
			$delRe = $this->doctorMod->deleteByCondition(array('clinic_id'=>$REQUEST['id']));
			if($re['status']==1&& $delRe['status']==1){
				$model->commit();
			}else{
				$model->rollback();
				responseJson ($re['status'],'删除失败!',$re['data'] );
			}
			responseJson ($re['status'],$re['msg'],$re['data'] );
		}else{
			responseJson ('2','错误','删除失败！');
		}
		
	}
	
	public function addClinicAccount(){
		$REQUEST = ( array ) I ( 'request.' );
		//$id = think_ucenter_decrypt($REQUEST['id']);
		if(IS_POST){
			$clinId = think_ucenter_decrypt($REQUEST['clinId']);
			if(is_numeric($clinId)){
				$data['clinic_name']=$REQUEST['clinic_name'];//诊所名称
				$data['type']=0;
				$data['is_delete']=C('SALEMANAGER_CLINIC_IDDELETE.NORMAL');
				$manager=array(
					'name'=>$REQUEST['manager_name'],
					'mobile'=>$REQUEST['manager_mobile'],
					'password'=>think_ucenter_md5(C('SALEMANAGER_CLINIC_PASSWORD'), UC_AUTH_KEY),
					'status'=>0
				);
				if(!empty($this->doctorMod->findInfobyCondition(array('mobile'=>$REQUEST['manager_mobile'],'type'=>2)))){
					responseJson ($re['status'],'手机号已存在!',$re['data']);
				}
				$model = new \Think\Model();
				$model->startTrans();
				$user=array(
					'mobile'=>$REQUEST['manager_mobile'],
					'password'=>think_ucenter_md5(C('SALEMANAGER_CLINIC_PASSWORD'), UC_AUTH_KEY),
					'type'=>2
				);
				$re = $this->clinicMod->updateFieldByCondition(array('id'=>$clinId),$data);//更改诊所信息
				$maRe = $this->doctorMod->updateFieldByCondition(array('clinic_id'=>$clinId), $manager);
				$useRe = $this->userMod->add($user);
				if($re['status']==1&&$maRe['status']==1&&$useRe['status']==1){
					$model->commit();
					responseJson ($re['status'],$re['msg'],$re['data']);
				}else{
					$model->rollback();
					echo 'dfd';
					responseJson ('2','参数错误','参数错误！');
				}
			}else{
				responseJson ('2','参数错误','参数错误！');
			}
		}else{
			$clinic = $this->clinicMod->findById(think_ucenter_decrypt($REQUEST['id']));
			$this->assign('clinId',$REQUEST['id']);
			$this->assign('clinname',$clinic['clinic_name']);
			$this->display();
		}
	}
	
	/**
     * 我的诊所
     */
    public function clinic() {
    	$REQUEST = ( array ) I ( 'request.' );
        if (IS_AJAX &&!isset($REQUEST['pageStart'])) {

            //删除诊所
            $clinicId = think_ucenter_decrypt($_GET['clinicid']);
            if (empty($clinicId)) {
                responseJson(0, '数据无效');
                exit;
            }
            $clinicModel = M('clinic');
            $where = array(
                'saleman_id' => array('eq', self::$user['id']),
                'id' => array('eq', $clinicId),
            );
            $data = array(
                'is_delete' =>0
            );
            $excNum = $clinicModel->where($where)->data($data)->save();
            if ($excNum <= 0) {
                responseJson(0, '删除失败');
                exit;
            }
            responseJson(1, '删除成功');
            exit;
        }

        
        //查询我的诊所（有效诊所，未删除）
        $clinicModel = M('clinic');
        $where = array(
        		'saleman_id' => array('eq', self::$user['id']),
        		'is_delete' => array('eq', 1),
        		'type' => array('eq', '0')
        );
        	if(!empty($REQUEST['pageStart'])){
		        $clinicList = $clinicModel->where($where)->limit($REQUEST['pageStart'],C('SALEMANAGER_PAGE_NUM'))->select();
		        //加密诊所id
		        foreach ($clinicList as $k=>$v){
		        		$clinicList[$k]['id']=think_ucenter_encrypt($v['id']);
		        }
        		responseJson(200, '刷新成功',$clinicList);
        }else{
	        $clinicList = $clinicModel->where($where)->limit(0,C('SALEMANAGER_PAGE_NUM'))->select();
	        //加密诊所id
	        foreach ($clinicList as $k=>$v){
	        		$clinicList[$k]['id']=think_ucenter_encrypt($v['id']);
	        }
	        $this->assign(
	                array(
	                    'clinicList' => $clinicList
	                )
	        );
	       // unset($clinicList);
	        if(empty($clinicList)){
	        		$this->display('noclinic');
	        }
	       	 $this->display();
        }
    }

    /**
     * 诊所详情
     */
    public function clinichome() {
        $id = empty($_GET['id']) ? 0 : think_ucenter_decrypt($_GET['id']);
        //查询诊所（有效诊所，未删除）
        $clinicModel = M('clinic');
        $where = array(
            'saleman_id' => array('eq', self::$user['id']),
            'is_delete' => array('eq', 1),
            'id' => array('eq', $id),
        );
        

        //查询当前诊所医生
        $doctorModel = M('doctor');
        $where = array(
            'clinic_id' => array('eq', $id),
        		'type'=>1
        );
        $doctorList = $doctorModel->where($where)->select();
        $manager = $doctorModel->where(array('clinic_id'=>$id,'type'=>2,'is_manager'=>1,'status'=>0))->find();
        $clinic = $clinicModel->where($where)->find();
        if(!empty($manager)){
        		$clinic['manager_name']=$manager['name'];
        		$clinic['manager_mobile']=$manager['mobile'];
        }
        
        $this->assign(
                array(
                    'clinic' => $clinic,
                    'doctorList' => $doctorList
                )
        );
        $this->display();
    }

    
    public function addClinic(){
    		$this->display();
    }
	
	/**
	 *   签到
	 */
	public function sign(){
		$this->display();
	}
	
	/**
	 *   诊所订单
	 */
	public function clinicOrder(){
		$this->display();
	}
	
	/**
	 *   订购药品
	 */
	public function orderMedicine(){
		$this->display();
	}
	
	/**
	 *   消息推送
	 */
// 	public function pushHistory(){
// 		$this->display();
// 	}
	
	/**
	 *   工作汇总
	 */
	public function workSummaryo(){
		$this->display();
	}
}
