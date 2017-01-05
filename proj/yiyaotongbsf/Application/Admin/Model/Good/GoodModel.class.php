<?php

namespace Admin\Model\Good;

// use Admin\Model\Good\ClassfiyModel;
use Think\Model;

Class GoodModel extends Model
{
	public function lists()
	{
		return $this->select();
	}

	public function countNum()
	{
		return $this->count();
	}

	public function states($id,$state)
	{
		return $this->where($id)->data($state)->save();
	}

	public function deleteGoods($id)
	{
		return $this->where($id)->delete();
	}

	public function findRow($id)
	{
		$result = $this->where(array('id' => $id))->select();
		return $result[0];
	}
	/**
	 * 获得对应id的一条数据
	 * */
	 public function getOne($id){
	 	return $this->where("id=".$id)->find();
	 }
	 /**
	 * 设置对应id的商品的字段值
	 * */
	 public function setOne($id,$field){
	 	return $this->where("id=".$id)->setField('lable',$field);
	 }
	 /**
	 * 设置对应id的商品的字段值新用
	 * */
	 public function setOneField($id,$field,$ans){
	 	return $this->where("id=".$id)->setField($field,$ans);
	 }
	/**
	 * 添加数据
	 * */
	 public function addData($data){
	 	return $this->data($data)->add($data);
	 }
	 /**
	 * 更新
	 * */
	 public function updata($data){
	 	return $this->save($data);
	 }
	 /**
	 * 获得对应id的商品的字段值
	 * */
	 public function getOneField($id,$field){
	 	return $this->where("id=".$id)->getField($field);
	 }
	 /**
	 * 获得对应id的一条数据
	 * */
	 public function oneGet($id1,$id2){
	 	return $this->where($id1."=".$id2)->select();
	 }
}