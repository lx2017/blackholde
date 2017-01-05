<?php
/**
 * 后台首页控制器
 */

namespace Admin\Controller\Column;

use Admin\Controller\AdminController;
use Admin\Model\Employee\AuthGroupModel;

/**
 * 栏目管理控制器
 * @author  yuxiang <418404572@qq.com>
 */
class ColumnController extends AdminController
{
	protected $model;
	
	public function __construct(){
		parent::__construct();
		$this->model=new \Admin\Model\Column\ColumnModel();
	}
	/**
     * 栏目管理列表首页--展示数据树为自己写的--现改用ztree
	 * 2016-06-20启用
     */
    public function index()
    {
        if (UID) {
            $this->meta_title = '栏目列表首页';
            $data=$this->model->getAll();
			$return=$this->model->getAll();

            if (empty($data)) $this->error('403:禁止访问');
			//获取下拉菜单
			$data=$this->model->arr2tree2($this->model->getAll(),0);
			//获得数据树输出到页面
			//$data=$this->model->getChildren($data);
			$data=json_encode($data);
			$this->assign('data', $data);

			$key = I('key');
			$list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
			$list = int_to_string($list);
			$actions = $this->getMenuActions($key);
			$actionInfos = [];
			foreach ($actions as $action) {
				$actionInfos[$action['key']] = $action;
			}
			//dump($actionInfos);die;
			$this->assign('key',$key);
			$this->assign('actions', $actionInfos);
			$this->assign('_list', $list);
			$this->assign('_use_tip', true);
			if(IS_POST){
				$data=I("post.");
				$data['app_icon_url']=session("iconPic");
				if($this->model->data($data)->save()){
					$this->success("编辑成功");
				}
			}
			//在这里进行判断是移动端还是pc端
			if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
				 echo json_encode(array('code'=>1002,"msg"=>'成功','data'=>$return));
			}else{ //你是PC端访问的--直接显示
				 $this->display();
			}
        } else {
            $this->redirect(ADMIN_PATH_NAME . '/Login/Public/login');
        }
    }
    /**
     * 栏目管理列表首页--展示数据树为自己写的--现改用ztree
	 * 2016-06-20停用
     */
    public function index222()
    {
        if (UID) {
            $this->meta_title = '栏目列表首页';
            $data=$this->model->getAll();
			
            if (empty($data)) $this->error('403:禁止访问');
			
            $this->assign('data', $data);

		$key = I('key');
        $list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
        $list = int_to_string($list);
        $actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$this->assign('key',$key);
        $this->assign('actions', $actionInfos);
        $this->assign('_list', $list);
        $this->assign('_use_tip', true);
		//$file="http://".$_SERVER['HTTP_HOST'].__ROOT__."/Html/column/".$_SESSION['name'].".shtml";
		if(is_file("./Html/column/".$_SESSION['nameColumn'].".shtml") and (time()-filemtime("./Html/column/".$_SESSION['nameColumn'].".shtml"))<=1*60){
			 $this->display("./Html/column/".$_SESSION['nameColumn'].".shtml");
		}else{
			 $this->display();
		}
           
        } else {
            $this->redirect(ADMIN_PATH_NAME . '/Login/Public/login');
        }
    }
	/**
	 *生成静态页面
	 */
	 public function viewHtml(){
	 	$key1=I('get.key1');
		$key=I('get.key');
		//dump($key1);
		//dump($key);die;
		if($key=="COLUMNVIEW"){
            $data=$this->model->getAll();
	        $list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
	        $list = int_to_string($list);
	        $actions = $this->getMenuActions($key1);
			
	        $actionInfos = [];
	        foreach ($actions as $action) {
	            $actionInfos[$action['key']] = $action;
	        }
			$this->assign('data', $data);
	        $this->assign('actions', $actionInfos);
	        $this->assign('_list', $list);
	        $this->assign('_use_tip', true);
			$name=date("YmdHis").rand(0,11111);
			$this->buildHtml($name, HTML_PATH . '/column/', 'index', 'utf8');
			$_SESSION['nameColumn']=$name;
			$this->success("成功");
		}
	 }
	/**
	 *删除
	 */
	 public function del(){
	 	$id=intval(I('get.id'));
		if(!$id){
			$this->error("参数为空");
		}
		//判断当前栏目下是否有子栏目，有子栏目的话，不可删除
		if($this->model->where("column_pid=".$id)->find()){
			$this->error("有子栏目，不可删除");
		}
	 	if($this->model->where("column_id=".$id)->delete()){
	 		//$this->success("删除成功");
	 		//在这里进行判断是移动端还是pc端
				if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
					 echo json_encode(array('code'=>1002));
				}else{ //你是PC端访问的--直接显示
					 $this->success("删除成功");
				}
	 	}
		
	 }
	 /**
	 *删除--异步编辑
	 */
	 public function edit(){
	 	$column_id=I("post.column_id",0,'intval');
		$column_pid=I("post.column_pid",0,'intval');
		//获得当前条目数据
		$dataOne=M("Column")->where("column_id=".$column_id)->find();
		$dataOne['app_icon_url']="http://".$_SERVER['HTTP_HOST'].$dataOne['app_icon_url'];
		//获取下拉菜单
		$data=$this->model->arr2tree1($this->model->getAll(),0);
		//获得数据树输出到页面
		$data=$this->model->getChildren($data);
		foreach($data as $k=>$v){
			$data[$k]['column_title']=str_pad("",$v['deep']*3, "-",STR_PAD_RIGHT).$v['column_title'];
		}
		$dataOne['column']=$data;
		//dump($dataOne);die;
		$this->ajaxReturn($dataOne);
		
		if(IS_POST){
			
			if($this->model->edit()){
				//$this->success("编辑成功");
				//在这里进行判断是移动端还是pc端
				if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
					 echo json_encode(array('code'=>1002));exit;
				}else{ //你是PC端访问的--直接显示
					$this->success("编辑成功");    
				}
			}
		}
	 }
	 /**
	 *编辑--原来版本
	  * 20160621停用
	 */
	 public function edit222(){
	 	$id=I('get.id',0,'intval');
		//获取当前数据
		$dataOne=$this->model->find($id);
		//获取下拉菜单
		$data=$this->model->arr2tree1($this->model->getAll(),0);
		//获得数据树输出到页面
		$data=$this->model->getChildren($data);
		//获得它的父级--没有父级就是它自己
		$fatherData=$this->model->find($dataOne['column_pid']);
		
		$this->assign('fatherData',$fatherData);
		$this->assign('dataOne',$dataOne);
		$this->assign('data',$data);
		if(IS_POST){
			if($this->model->edit()){
				//$this->success("编辑成功");
				//在这里进行判断是移动端还是pc端
				if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
					 echo json_encode(array('code'=>1002));
				}else{ //你是PC端访问的--直接显示
					$this->success("编辑成功");    
				}
			}
		}
		$this->display('edit');
	 }
	 /**
	  * 添加数据
	  * */
	  public function add(){
	  	//获取下拉菜单
		$data=$this->model->arr2tree1($this->model->getAll(),0);
		//获得数据树输出到页面
		$data=$this->model->getChildren($data);
		$this->assign('data',$data);
		
		$key = I('key');//dump($key);die;
	        $list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
	        $list = int_to_string($list);
	        $actions = $this->getMenuActions($key);
	        $actionInfos = [];
	        foreach ($actions as $action) {
	            $actionInfos[$action['key']] = $action;
	        }
			//dump($actionInfos);die;
	        $this->assign('actions', $actionInfos);
			$this->assign('key',$key);
	        $this->assign('_list', $list);
	        $this->assign('_use_tip', true);
	  	if(IS_POST){//dump($_POST);die;
	  		if(!$this->model->addData()){
	  			$this->error($this->model->error);
	  		}
	  		//$this->success("添加成功");
	  		//在这里进行判断是移动端还是pc端
			if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
				 echo json_encode(array('code'=>1002));
			}else{ //你是PC端访问的--直接显示
				$this->success("添加成功");     
			}
	  	}
		$this->display();
	  }
	/**
	 * 异步删除图片
	 */
	public function check(){
	    $column_id=I("get.column_id",0,'intval');
		//echo $column_id;die;
		//获得当前栏目的column_pid
		$data=array();
		if($column_id){
			$pid=M("Column")->where("column_id=".$column_id)->getField("column_pid");
			if($pid==0){
					//获得当前分类下子分类最大的sort
					$maxSort=(M("Column")->where("column_pid=".$column_id)->Max("sort"));
					$data['sort']=intval($maxSort)+1;
					$data['top']='top';
					
					
				}
			
		}else{
			$column_id=0;
			$pid=0;
			$data['sort']=0;
			$data['top']='empty';
		}
		//dump($maxSort);die;
		//如果column_pid=0并且column_id!=0,则为已经存在的顶级栏目，
		//如果column_pid=0并且column_id=0,则为新添加顶级栏目，
		
		$this->ajaxReturn($data);
	}
	 /**
	 * uploadify上传
	 */
	public function uploadify(){
		
	     $upload = new \Think\Upload();
		 is_dir('./Column/' . date('Y-m-d'))  || mkdir('./Column/' . date('Y-m-d'),0777,true);
		
		 $dir='Uploads/Column/'.date('Y-m-d');
	  	 $upload->savePath  = './Column/'; // 设置附件上传（子）目录
	  	 $file = $upload->upload();
		 
		 if (empty($file)) {
		 	$this->ajaxReturn("上传失败");
		 } else {
		 	//编辑上传新图片时，原来的图片删除
		 	//来这个地方可以缩略图处理生成缩略图,按照原图的比例生成一个最大为50*50的缩略图并保存为thumb.jpg
			$image = new \Think\Image();
			$a=$image->open("./".$dir."/".$file['Filedata']['savename']);
			$a=$image->thumb(50, 50)->save("./".$dir."/"."Thumb_".$file['Filedata']['savename']);
			//$file['Filedata']['thumb']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/"."Thumb_".$file['Filedata']['savename'];
			//$file['Filedata']['url']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/".$file['Filedata']['savename'];
			//$file['Filedata']['savepath']="./Uploads".ltrim($file['Filedata']['savepath'],'.').$file['Filedata']['savename'];
			
			$file['Filedata']['thumb']="./".$dir."/"."Thumb_".$file['Filedata']['savename'];
			$file['Filedata']['url']=__ROOT__."/".$dir."/".$file['Filedata']['savename'];
			$file['Filedata']['savepath']="./Uploads".ltrim($file['Filedata']['savepath'],'.').$file['Filedata']['savename'];
			
		 	/*foreach($files as $k=>$v){
		 		$files[$k]['Filedata']['url']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/".$v['Filedata']['savename'];
		 	}*/
		 	$data = $file['Filedata'];
			//dump($data);die;
		 	session("iconPic",$data['url']);
			$this->ajaxReturn($data);
		 }
	}
	/**
	 * 异步删除图片
	 */
	public function delImg(){
	    $path = I('post.path');
		$thumb = I('post.thumb');
		//删除图片
		unlink($path);
		unlink($thumb);
	}
	
	 /**
	 *栏目异步点击展开与合拢,每点击一次，它的下一级展开
	 */
	 public function ajaxBack(){
	 	$id=I('get.id',0,'intval');
		$data=$this->model->getAll();
		$sonCid=$this->getSon($data,$id);
		$this->ajaxReturn($sonCid);
	 }

	/**
	 * 递归传入数组，子集前面加“-”，,给两个变量，获得数据树
	 */
	public function getSonTree($data,$tid){
		static $num;
		$count=0;//统计不同进入次数
		$num[]=1;
		static $temp = array();
		foreach ($data as $k=>$v) {
				if ($v['column_pid'] == $tid) {
					$count+=1;
					static $one=1;
					if($count!=1){
						$one=2;//回避第一次进入出现的不和谐问题
						$num=array();
						$add=' -*1 ';	
					}
					if($count==1){
						$add='*2';	
					}
					if($one==1){
						foreach($num as $kk=>$vv){
							$add.=" -*3 ";
						}
					}
					if($one==2){
						$add=' -*4 ';
						foreach($num as $kk=>$vv){
							$add.=" -*5 ";
						}
					}
					$v['column_title']=$add.$v['column_title']."*  ".$count;
					$temp[] =$v;
					
					$this->getSonTree($data,$v['column_id']);
				}
		}
		return $temp;	
	}
	
	/**
	  * 判断是移动端还是pc端
	  */
	public  function is_mobile(){ 
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
