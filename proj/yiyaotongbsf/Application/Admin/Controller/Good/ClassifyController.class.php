<?php
namespace Admin\Controller\Good;

use Admin\Model\Good\ClassifyModel;
use Admin\Controller\AdminController;

class ClassifyController extends AdminController
{
	private $model;
	public function __construct(){
		parent::__construct();
		$this->model=new \Admin\Model\Good\ClassifyModel();
	}
	public function index()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$data=$this->model->arr2tree1($this->model->getAll());
		//dump($actionInfos);die;
		$this->assign('actions', $actionInfos);
		$this->assign('_list', $data);
        $this->display('index');
	}
	/**
	 * 显示添加页面
	 * */
	public function add()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		//dump($actionInfos);die;
		//获得原有数据的顶级分类
		$top=$this->model->classify_1st();
		//dump($top);
		$this->assign('actions', $actionInfos);
		$this->assign('top', $top);
		$this->display('add');
	}
	/**
	 * 页面传过来父级id获得他的下级
	 * */
	public function gotBottom()
	{
		
		$id=I("post.id",0,'intval');
		$data=$this->model->getBottom($id);
		if(!$data){
			$data=0;
		}
		//dump($data);die;
		$this->ajaxReturn($data);
	}
	/**
	 * 添加数据
	 * 
	 * */
	public function addData()
	{
		
		$id=I("post.id",0,'intval');
		$classify=I("post.classify");
		$data=array();
		$data['pid']=$id;
		$data['classify']=$classify;
		//dump($data);die;
		if($this->model->addData($data)){
			 $this->ajaxReturn("添加成功");
		}
	}

	public function delete()
	{
		$id = array_unique((array)I('id', 0));
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		//dump($id);die;
		$map['id'] = array('in',explode(',', $id));
		$changeState = new ClassifyModel();
		$result = $changeState->deleteClassifys($map);
		if(false !== $result){
			$this->success('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}

	public function edit()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$id=I("get.id",0,'intval');
		//获得当前选定数据
		$data=$this->model->getOne($id);
		//获得原有数据的顶级分类
		$clamodel=new ClassifyModel();
		$top=$clamodel->classify_1st();
		/*dump($data);
		dump($top);die;*/
		$this->assign('actions', $actionInfos);
		$this->assign('data', $data[0]);
		$this->assign('top', $top);
		$this->display('edit');
	}
	/**
	 * 编辑分类--确定
	 * 
	 * */
	public function realEdit()
	{
		
		$id=I("post.id",0,'intval');
		$pid=I("post.pid",0,'intval');
		$classify=I("post.classify");
		$data[]=array();
		if($pid){
			$data['pid']=$pid;
		}
		
		$data['id']=$id;
		$data['classify']=$classify;
		//dump($data);die;
		if($this->model->updata($data)){
			 $this->ajaxReturn("ok");
		}else{
			$this->ajaxReturn("fail");
		}
	}
}