<?php

namespace Admin\Model\Good;

use Think\Model;

Class LableModel extends Model
{
	public function lists()
	{
		return $this->select();
	}

	public function find($condition)
	{	
		return $this->where($condition)->getField('name');
	}

	public function countByLableName($lableName)
	{
		return $this->where(array('name' => $lableName))->count();
	}

	public function addLable($data)
	{
		return $this->data($data)->add();
	}

	public function updateLable($condition, $data)
	{
		return $this->where($condition)->data($data)->save();
	}

	public function deleteLables($id)
	{
		return $this->where($id)->delete();
	}

	public function findID($condition)
	{
		return $this->where($condition)->getField('id');
	}
	
	/**
	 * 获得数据树
	 *
	 * */
	 public function getTree($id)
	{
		return $this->where($condition)->getField('id');
	}
}