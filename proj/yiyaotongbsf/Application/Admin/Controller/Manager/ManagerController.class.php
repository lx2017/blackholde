<?php
/**
 * 左侧菜单管理控制器
 * @author  yuxiang <418404572@qq.com>
 * @change 阿里 2016/09/20
 */
namespace Admin\Controller\Manager;

use Admin\Controller\AdminController;
use Admin\Model\Employee\AuthGroupModel;
use Admin\Model\Employee\ModuleModel;
use Admin\Model\Employee\AuthGroupModuleModel;

class ManagerController extends AdminController
{
	protected $model;
	protected $authModel;
	
	public function __construct(){
		parent::__construct();
		$this->model=new ModuleModel();
		$this->authModel=new AuthGroupModuleModel();
		
	}

	 /**
	  * 添加数据
	  * */
	  public function add(){
		$key = I('key');
		$list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
		$list = int_to_string($list);
		$actions = $this->getMenuActions('ARTICLEADD');
		$actionInfos = [];
		foreach ($actions as $action) {
			$actionInfos[$action['key']] = $action;
		}

		//获得ymt_module表的所有顶级菜单---pid=0
		$topData=$this->model->getTop();

		//获得顶级的下一级
		$twoData=$this->model->arr2tree1($topData);
		$twoData=$this->model->getChildren($topData);

		//dump($twoData);die;
		$this->assign('twoData', $twoData);
		$this->assign('actions', $actionInfos);
		$this->assign('key',$key);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);

		if(IS_POST){
	  		if(!$id=$this->model->addData()){
	  			$this->error($this->model->error);
	  		}
			$this->viewHtml($id);
	  		//在这里进行判断是移动端还是pc端
			if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
				 echo json_encode(array('code'=>1002));
			}else{ //你是PC端访问的--直接显示
				$this->success("添加成功");     
			}
	  	}

		$this->display('add');
	  }


	/**
	 * 生成静态页面--用户提交文章即生成--文章内容页
	 * @author 阿里
	 */
	public function viewHtml($id){
		//获取当前数据
		$dataOne=$this->model->find($id);
		$cid=$dataOne['category_id'];
		//获得栏目名称
		$column=$this->modelColumn->where("column_id=".$cid)->getField("column_title",flase);
		$dataOne['column_title']=$column;

		$list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
		$list = int_to_string($list);
		$actions = $this->getMenuActions('ARTICLEADD');
		$actionInfos = [];
		foreach ($actions as $action) {
			$actionInfos[$action['key']] = $action;
		}
		$this->assign('actions', $actionInfos);
		$this->assign('dataOne',$dataOne);
		$this->assign('cid',$cid);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);
		$name=date("YmdHis").rand(0,11111);
		$this->buildHtml($name, HTML_PATH . '/article/', 'detail', 'utf8');
		$_SESSION['nameArticle']=$name;

	}

	/**
	 * 判断是移动端还是pc端
	 */
	public function is_mobile(){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$mobile_agents = Array("240x320","acer","acoon","acs-","abacho","ahong","airness","alcatel","amoi","android","anywhereyougo.com","applewebkit/525","applewebkit/532","asus","audio","au-mic","avantogo","becker","benq","bilbo","bird","blackberry","blazer","bleu","cdm-","compal","coolpad","danger","dbtel","dopod","elaine","eric","etouch","fly ","fly_","fly-","go.web","goodaccess","gradiente","grundig","haier","hedy","hitachi","htc","huawei","hutchison","inno","ipad","ipaq","ipod","jbrowser","kddi","kgt","kwc","lenovo","lg ","lg2","lg3","lg4","lg5","lg7","lg8","lg9","lg-","lge-","lge9","longcos","maemo","mercator","meridian","micromax","midp","mini","mitsu","mmm","mmp","mobi","mot-","moto","nec-","netfront","newgen","nexian","nf-browser","nintendo","nitro","nokia","nook","novarra","obigo","palm","panasonic","pantech","philips","phone","pg-","playstation","pocket","pt-","qc-","qtek","rover","sagem","sama","samu","sanyo","samsung","sch-","scooter","sec-","sendo","sgh-","sharp","siemens","sie-","softbank","sony","spice","sprint","spv","symbian","tablet","talkabout","tcl-","teleca","telit","tianyu","tim-","toshiba","tsm","up.browser","utec","utstar","verykool","virgin","vk-","voda","voxtel","vx","wap","wellco","wig browser","wii","windows ce","wireless","xda","xde","zte");
		$is_mobile = false;
		foreach ($mobile_agents as $device) {//这里把值遍历一遍，用于查找是否有上述字符串出现过
			if (stristr($user_agent, $device)) { //stristr 查找访客端信息是否在上述数组中，不存在即为PC端。
				$is_mobile = true;
				break;
			}
		}
		return $is_mobile;
	}




}
