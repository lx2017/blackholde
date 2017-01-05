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
		//dump($this->model);die;
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		/*$classifyModel = new ClassifyModel();
		$classify_1sts = $classifyModel->classify_1st();*/
		$data=$this->model->arr2tree2($this->model->getAll());
		$data=json_encode($data);
		if(IS_POST){
			$data=I("post.");
			if($this->model->data($data)->save()){
				$this->success("编辑成功");
			}
		}
		//dump($data);die;
		$this->assign('actions', $actionInfos);
		$this->assign('data', $data);
        $this->display();
	}

	public function add()
	{
		echo 1;
	}

	public function delete()
	{
		$id = array_unique((array)I('id', 0));
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in',explode(',', $id));
		$changeState = new ClassifyModel();
		$result = $changeState->deleteClassifys($map);
		if(false !== $result){
			$this->success('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}
	/**
	 *删除--异步编辑
	 */
	 public function edit(){
	 	$column_id=I("post.column_id",0,'intval');
		$column_pid=I("post.column_pid",0,'intval');
		//获得当前条目数据
		$dataOne=M("Classify")->where("id=".$column_id)->find();
		//$dataOne['app_icon_url']="http://".$_SERVER['HTTP_HOST'].$dataOne['app_icon_url'];
		//获取下拉菜单
		$data=$this->model->arr2tree1($this->model->getAll(),0);
		//dump($data);die;
		//获得数据树输出到页面
		$data=$this->model->getChildren($data);
		foreach($data as $k=>$v){
			$data[$k]['classify']=str_pad("",$v['deep']*3, "-",STR_PAD_RIGHT).$v['classify'];
		}
		$dataOne['classes']=$data;
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
	
}