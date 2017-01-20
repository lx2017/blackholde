<?php
/**
 * 后台首页控制器
 */

namespace Admin\Controller\Article;

use Admin\Controller\AdminController;
use Admin\Model\Employee\AuthGroupModel;
use Think\Upload;
use Think\Image;
use Admin\Model\Employee\ModuleModel;
/**
 * 图文管理控制器
 * @author  yuxiang <418404572@qq.com>
 * @changed by ali
 */
class ArticleController extends AdminController
{
	protected $model;
	protected $modelColumn;
	
	public function __construct(){
		parent::__construct();
		$this->model=new \Admin\Model\Article\ArticleModel();
		$this->modelColumn=new \Admin\Model\Column\ColumnModel();
		
	}

    /**
     * 文章列表首页
     */
    public function index()
    {

    	if (UID) {
            $this->meta_title = '图文列表首页';
            $data=$this->model->getAll();
            if (empty($data)) $this->error('没有相关数据');

			$key = I('key');

			//获取权限组(员工组)
	        $list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
	        $list = int_to_string($list);

			//图文栏目的操作集
			$actions = $this->getMenuActions($key);
	        $actionInfos = [];
	        foreach ($actions as $action) {
	            $actionInfos[$action['key']] = $action;
	        }

			//获取栏目下拉菜单
			$dataColumn=$this->modelColumn->arr2tree2($this->modelColumn->getAll(),0);

			//获得数据树输出到页面
			$dataColumn=$this->modelColumn->getChildren($dataColumn);

			//$data=json_encode($data);
	        $this->assign('dataColumn', $dataColumn);
			$this->assign('data', $data);
	        $this->assign('actions', $actionInfos);
			$this->assign('key',$key);
	        $this->assign('_list', $list);
	        $this->assign('_use_tip', true);
			
			//异步搜索 start,返回数据--thinkphp没有全文索引，或者直接写原生解决
			if(I("get.title")){
				$getTitle=I("get.title");
	    		$title=array();
	    		$title['title']=array('like',"%".$getTitle."%");
	    		session("title",$title);
    			$this->ajaxReturn(serialize($backData));
			}
			
			//下拉异步 start,返回数据
			if(I("get.cid")){
				$cid=I("get.cid",0,'intval');
	    		session("cid",$cid);
    			$this->ajaxReturn($cid);
			}
			/*进行分页 start*/
			if(I("get.data")=="comeHere"){
				$count=$this->model->where(session('title'))->count();
				$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(2)
				$data = $this->model->where(session('title'))->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
				
			}elseif(I("get.select")=="select" and I("get.id")){
				$count=$this->model->where("category_id=".session("cid"))->count();
				$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(2)
				$data = $this->model->where("category_id=".session("cid"))->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
				
			}else{
				$count=$this->model->count();
				$Page = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(2)
				$data = $this->model->order('add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			}
			$show = $Page->show();// 分页显示输出
			if(strlen($show)<=15){
				$show=<<<show
				 <div>  
				<span class="current">共 1 页</span>
				</div>
show;
			}
			$this->assign('cid',I("get.id"));// 赋值分页输出
			$this->assign('_page',$show);// 赋值分页输出
			// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
 			$this->assign('data',$data);// 赋值数据集	
			/*进行分页 end*/	
			session('title',null);
			session('cid',null);
			//在这里进行判断是移动端还是pc端
			if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
				foreach($data as $k=>$v){
					unset($data[$k]['picture']);
				}
				 echo json_encode(array('code'=>1002,"msg"=>'成功','data'=>$data));
			}else{ //你是PC端访问的--直接显示
				 $this->display();      
			}  
			
			
        } else {
            $this->redirect(ADMIN_PATH_NAME . '/Login/Public/login');
        }
    }




	public function detail(){
		$id=I('get.id',0,'intval');
		//获取当前数据
		$dataOne=$this->model->find($id);
		$cid=$dataOne['category_id'];
		//获得栏目名称
		$column=$this->modelColumn->where("column_id=".$cid)->getField("column_title",flase);
		$dataOne['column_title']=$column;
		$key = I('key');
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
		//dump($dataOne);die;
		//在这里进行判断是移动端还是pc端
		if($this->is_mobile()){ // 您是手机端访问的，已跳转到手机端
			unset($dataOne['picture']);
			echo json_encode(array('code'=>1002,"msg"=>'成功','data'=>$dataOne));
		}else{ //你是PC端访问的--直接显示
			// $this->display();
			 //判断是否显示静态页面
			if(is_file("./Html/article/".$_SESSION['nameArticle'].".shtml") and (time()-filemtime("./Html/article/".$_SESSION['nameArticle'].".shtml"))<=10*60){
					 $this->display("./Html/article/".$_SESSION['nameArticle'].".shtml");
			}else{
					 $this->display();
			}
		}
		
	}




	/**
	 *生成静态页面--用户提交文章即生成--文章内容页
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
	 *生成静态页面--此处为点击“生成静态页面”按钮生成静态页面
	 * 
	 * 2016-06-20日改成用户提交即生成静态页面--文章列表页静态
	 * 2016-06-22日停用
	 */
	 public function viewHtml222(){
	 	$key1=I('get.key1');//目的是为页面显示“生成静态文件按钮”；
		$key=I('get.key');
		if($key=="ARTICLEVIEW"){
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
			$this->buildHtml($name, HTML_PATH . '/article/', 'index', 'utf8');
			$_SESSION['nameArticle']=$name;
			$this->success("成功");
		}
	 }



	/**
	 * uploadify上传
	 */
	public function uploadify(){
		
	     $upload = new \Think\Upload();
		 is_dir('./Article/' . date('Y-m-d'))  || mkdir('./Article/' . date('Y-m-d'),0777,true);
		
		 $dir='Uploads/Article/'.date('Y-m-d');
	  	 $upload->savePath  = './Article/'; // 设置附件上传（子）目录
	  	 $file = $upload->upload();
		 
		 if (empty($file)) {
		 	$this->ajaxReturn("上传失败");
		 } else {
		 	//编辑上传新图片时，原来的图片删除
		 	//来这个地方可以缩略图处理生成缩略图,按照原图的比例生成一个最大为50*50的缩略图并保存为thumb.jpg
			$image = new \Think\Image();
			$a=$image->open("./".$dir."/".$file['Filedata']['savename']);
			$a=$image->thumb(50, 50)->save("./".$dir."/"."Thumb_".$file['Filedata']['savename']);

			$file['Filedata']['thumb']=__ROOT__."/".$dir."/"."Thumb_".$file['Filedata']['savename'];
			$file['Filedata']['url']=__ROOT__."/".$dir."/".$file['Filedata']['savename'];
			$file['Filedata']['savepath']="./Uploads".ltrim($file['Filedata']['savepath'],'.').$file['Filedata']['savename'];

		 	$data = $file['Filedata'];
		 	
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
	 *删除
	 */
	 public function del(){
	 	$id=intval(I('get.id'));
		if(!$id){
			$this->error("参数为空");
		}
	 	if($this->model->where("id=".$id)->delete()){
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
	 *编辑
	 */
	 public function edit(){
	 	$id=I('get.id',0,'intval');
		//获取当前数据
		$dataOne=$this->model->find($id);
		$cid=$dataOne['category_id'];
		//获取栏目下拉菜单
		$data=$this->modelColumn->arr2tree1($this->modelColumn->getAll(),0);
		//获得栏目数据树输出到页面
		$data=$this->modelColumn->getChildren($data);
		$key = I('key');
		$list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
		$list = int_to_string($list);
		$actions = $this->getMenuActions('ARTICLEADD');
		$actionInfos = [];
		foreach ($actions as $action) {
			$actionInfos[$action['key']] = $action;
		}
		$this->assign('actions', $actionInfos);
		$this->assign('dataOne',$dataOne);
		$this->assign('data',$data);
		$this->assign('cid',$cid);
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
	  	//获取栏目下拉菜单
		$data=$this->modelColumn->arr2tree1($this->modelColumn->getAll(),0);
		//获得栏目数据树输出到页面
		$data=$this->modelColumn->getChildren($data);
		
		$key = I('key');
		$list = $this->lists(new AuthGroupModel(), array('module' => 'admin'), 'id asc');
		$list = int_to_string($list);
		$actions = $this->getMenuActions('ARTICLEADD');
		$actionInfos = [];
		foreach ($actions as $action) {
			$actionInfos[$action['key']] = $action;
		}
		$this->assign('data', $data);
		$this->assign('actions', $actionInfos);
		$this->assign('key',$key);
		$this->assign('_list', $list);
		$this->assign('_use_tip', true);
	  	if(IS_POST){
	  		if(!$id=$this->model->addData()){
	  			$this->error($this->model->error);
	  		}
			$this->viewHtml($id);
	  		//$this->success("添加成功");
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
	 * 栏目异步点击展开与合拢,每点击一次，它的下一级展开
	 */
	 public function ajaxBack(){
	 	$id=I('get.id',0,'intval');
		$data=$this->model->getAll();
		$sonCid=$this->getSon($data,$id);
		$this->ajaxReturn($sonCid);
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
