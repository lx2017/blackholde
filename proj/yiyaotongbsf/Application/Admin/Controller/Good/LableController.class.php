<?php

namespace Admin\Controller\Good;

use Admin\Model\Good\LableModel;
use Admin\Controller\AdminController;

class LableController extends AdminController
{
	public function index()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
		$lableModel = new LableModel();
		$lists = $lableModel->lists();
		foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$this->assign('actions', $actionInfos);
		$this->assign('_list', $lists);
        $this->display();
		
	}

	public function add()
	{
		if(IS_POST){
			if(I('lable_name') == ''){
				$this->error('标签不能为空，重新输入');
			}
			$lableModel = new LableModel();
			$data = array('name' => I('lable_name'));
			$result = $lableModel->findID($data);
			if($result){
				$this->error('次标签已经存在，请修改');
			}
			$result = $lableModel->addLable($data);
			if($result){
				$this->success('标签添加成功！', U('index', array('key' => 'LABLELIST')));
			}else{
				$this->error('添加失败');
			}
		}else{
			$this->assign('url', U('', array('key' => I('key'))));
            $this->display();
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
		$changeState = new LableModel();
		$result = $changeState->deleteLables($map);
		if(false !== $result){
			$this->ajaxReturn('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}

	public function edit()
	{
		if(IS_POST){
			$id = I('id');
			$name = I('lable_name');
			$condition = array('id' => $id);
			$data = array('name' => $name);
			$lableModel = new LableModel();
			$result = $lableModel->updateLable($condition,$data);
			if($result !== false){
				$this->success('标签修改成功！', U('index', array('key' => 'LABLELIST')));
			}else{
				$this->error('修改失败');
			}
		}else{
			$id = I('get.id');
			$condition = array('id' => $id);
			$lableModel = new LableModel();
			$result['lable_name'] = $lableModel->find($condition);
			$result['id'] = $id;
			$this->assign('result', $result);
			$this->assign('url', U('', array('key' => I('key'))));
        	$this->display();
		}

	}
}