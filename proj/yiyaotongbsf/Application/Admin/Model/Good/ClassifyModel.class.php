<?php

namespace Admin\Model\Good;

use Think\Model;

Class ClassifyModel extends Model
{
	public function lists()
	{
		$test = "1";
		return $test;
	}

	public function find($condition)
	{	
		return $this->where($condition)->getField('classify');
	}

	public function classify_1st()
	{
		return $this->where(array('pid' => 0))->select();
	}

	public function classify_2nd($condition)
	{
		return $this->where($condition)->select();
	}

	public function classify_3rd($condition)
	{
		return $this->where($condition)->select();
	}
	public function deleteClassifys($condition)
	{
		return $this->where($condition)->delete();
	}

	public function findAllClassifyById_3rd($id)
	{
		$classify_3rd = $this->where(array('id' => $id))->select();
		$classify_2nd = $this->where(array('id' => $classify_3rd[0]['pid']))->select();
		$classify_1st = $this->where(array('id' => $classify_2nd[0]['pid']))->select();
		return array('id_1st' => $classify_1st[0]['id'] ,'classify_1st' => $classify_1st[0]['classify'] ,'id_2nd' => $classify_2nd[0]['id'] ,'classify_2nd' => $classify_2nd[0]['classify'] ,'id_3rd' => $classify_3rd[0]['id'] ,'classify_3rd' => $classify_3rd[0]['classify'] );
	}
	/**
    * @return mixed 返回所有内容
    */
    public function getAll(){
       return $this->select();
    }
	/**
	 *添加数据 
	 * @return  
    */
    public function addData($data){
       return $this->data($data)->add();
    }
	/**
    * @return mixed 获得当前id的下级数据
    */
    public function getBottom($id){
       return $this->where("pid=".$id)->select();
    }
	/**
    * @return mixed 获得当前id的数据
    */
    public function getOne($id){
       return $this->where("id=".$id)->select();
    }
	/**
    * @return mixed 获得当前id的父级主键id
    */
    public function getUp($id){
    	$pid=$this->where("id=".$id)->getField("pid");
		return $pid;
       
    }
	/**
    * @return mixed 
    */
    public function updata($data){
		return $this->data($data)->save($data);
       
    }
	/**
	 * 将数据按照所属关系封装 
	 * @param $tree 传入的数组
	 * @param $rootId 传入的最顶级id：0
	 * @return mixed 递归返回的所有内容--为栏目ztree组合数据
	 * */  
	public function arr2tree2($tree, $rootId = 0) {  
	    $return = array();
		foreach($tree as $k=>$v){
			$tree[$k]['name']=$v['classify'];
			//$tree[$k]['open']=true;
			$tree[$k]['id']=$v['id'];
			$tree[$k]['pId']=$v['pid'];
		}
	    foreach($tree as $k=> $leaf) {
	        if($leaf['pid'] == $rootId) {
	            foreach($tree as $kk=> $subleaf) {
	                if($subleaf['pid'] == $leaf['id']) {
	                    $leaf['children'] = $this->arr2tree1($tree, $leaf['id']);  
	                    break;  
	                }  
	            }  
	            $return[] = $leaf;  
	        }  
	    } 
		foreach($return as $k=>$v){
			$return[$k]['open']=true;
		}
	    return $return;  
	}
	/**
	 * 将数据按照所属关系封装 
	 * @param $tree 传入的数组
	 * @param $rootId 传入的最顶级id：0
	 * @return mixed 递归返回的所有内容
	 * */  
	public function arr2tree1($tree, $rootId = 0) {  
	    $return = array();
	    foreach($tree as $k=> $leaf) {
	        if($leaf['pid'] == $rootId) {
	            foreach($tree as $kk=> $subleaf) {
	                if($subleaf['pid'] == $leaf['id']) {
	                    $leaf['children'] = $this->arr2tree1($tree, $leaf['id']);  
	                    break;  
	                }  
	            }  
	            $return[] = $leaf;  
	        }  
	    }  
	    return $return;  
	}  
	/**
	 * 与上面的arr2tree1函数共同使用，在页面生成数据树
	 * @param $parent 传入的数组
	 * @param $deep 传入的最顶级id：0
	 * @return mixed 递归返回的所有内容
	 */
	 
	public 	function getChildren($parent,$deep=0) {
	    foreach($parent as $row) {
	        $data[] = array("id"=>$row['id'], "classify"=>$row['classify'],"pid"=>$row['pid'],'deep'=>$deep);
	        if ($row['children']) {
	            $data = array_merge($data, $this->getChildren($row['children'], $deep+1));
	        }
	    }
	    return $data;
	}
}