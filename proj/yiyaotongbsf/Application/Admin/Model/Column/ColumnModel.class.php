<?php
/**
 * Created by hbuilder.
 * User: yuxiang
 * Date: 16/6/16
 * Time: 下午14:00
 */
namespace Admin\Model\Column;

use Think\Model;

/**
 * 栏目管理中用到的功能模块模型
 * Class ColumnModel
 * @package Admin\Model\Column
 */
class ColumnModel extends Model
{
	protected $tableName = 'column';
	protected $error;
	
	protected $_validate=array(
		//array('column_id','require','栏目编号必须'),
		array('column_title','require','栏目名称必须'),
		array('column_title','','栏目名称已经存在',0,'unique',1),
	);
	
	/**
	 * 编辑
	 * */
	public function edit(){
		$data=$_POST;
		$data['column_id']=intval($_POST['column_id']);
		$data['column_pid']=intval($_POST['column_pid']);
		if(!$this->create($data)){
			exit($this->getError());
		}
		return $this->save($data);
		
	}
	
	/**
	 * 添加
	 * */
	public function addData(){
		$data=I("post.");
		if(empty($data)){
			$this->error="数据为空";
		}
		
		if(!$this->create($data)){
			exit($this->error=$this->getError());
		}
		return $this->add($data);
		
	}
	
	
    /**
     * @return mixed 返回column的所有内容
     */
    public function getAll(){
       return $this->select();
    }
	/**
	 * @return mixed 递归返回column的所有内容
	 */
	public function getTree($data,$tid){
       return $this->getSon($data,$tid);
    }
	
	/**
	 * @param $id 传入的最顶级id：0
	 * @return mixed 递归返回column的所有内容--也可用作返回它下一级的数据，仅限他的下一级
	 */
	public function getTop($id){
       return $this->where('column_pid='.$id)->select();
    }
	
	public function getSon($data,$tid){
		static $temp = array();
		foreach ($data as $v) {
			if ($v['column_pid'] == $tid) {
				$temp[] =$v;
				$this->getSon($data,$v['column_id']);
			}
		}
		
		return $temp;	
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
			$tree[$k]['name']=$v['column_title'];
			//$tree[$k]['open']=true;
			$tree[$k]['id']=$v['column_id'];
			$tree[$k]['pId']=$v['column_pid'];
		}
	    foreach($tree as $k=> $leaf) {
	        if($leaf['column_pid'] == $rootId) {
	            foreach($tree as $kk=> $subleaf) {
	                if($subleaf['column_pid'] == $leaf['column_id']) {
	                    $leaf['children'] = $this->arr2tree1($tree, $leaf['column_id']);  
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
	        if($leaf['column_pid'] == $rootId) {
	            foreach($tree as $kk=> $subleaf) {
	                if($subleaf['column_pid'] == $leaf['column_id']) {
	                    $leaf['children'] = $this->arr2tree1($tree, $leaf['column_id']);  
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
	        $data[] = array("column_id"=>$row['column_id'], "column_title"=>$row['column_title'],"column_pid"=>$row['column_pid'],'deep'=>$deep);
	        if ($row['children']) {
	            $data = array_merge($data, $this->getChildren($row['children'], $deep+1));
	        }
	    }
	    return $data;
	}
	
	
	
}