<?php
/**
 * Created by hbuilder.
 * User: yuxiang
 * Date: 16/6/16
 * Time: 下午14:00
 */
namespace Admin\Model\Article;

use Think\Model;

/**
 * 图文管理中用到的功能模块模型
 * Class ArticleModel
 * @package Admin\Model\Article
 */
class ArticleModel extends Model
{
	protected $tableName = 'article';
	protected $error;
	
	protected $_validate=array(
		array('title','require','名称必须'),
		array('column_title','','栏目名称已经存在',0,'unique',1)
	);
	
	/**
	 * 编辑--包括上传图片缩略完整--未用js插件--停用
	 * */
	public function edit222(){
		$data=I("post.");
		if(empty($data)){
			$this->error="数据为空";
		}
		$upload = new \Think\Upload();// 实例化上传类
	  	$upload->savePath  = './Article/'; // 设置附件上传（子）目录
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
				//$this->error=$upload->getError();
			}else{// 上传成功 获取上传文件信息
				foreach($info as $file){
				$file['savepath'].$file['savename'];
				 //生成缩略图,按照原图的比例生成一个最大为50*50的缩略图并保存为thumb.jpg
				$image = new \Think\Image();
				$a=$image->open('./Uploads'.ltrim( $file['savepath'].$file['savename'],'.'));
				$image->thumb(50, 50)->save('./Uploads'.ltrim( $file['savepath']."Thumb_".$file['savename'],'.'));
		
			}
		}
		$filename="http://".$_SERVER['HTTP_HOST'].__ROOT__.'/Uploads'.ltrim( $file['savepath'].$file['savename'],'.');
		
		$filenameThumb="http://".$_SERVER['HTTP_HOST'].__ROOT__.'/Uploads'.ltrim( $file['savepath']."Thumb_".$file['savename'],'.');
		
		$data['thumb']=$filenameThumb;
		$data['picture']=$filename;
		if(!$this->create($data)){
			exit($this->error=$this->getError());
		}
		if(!$this->create($data)){
			exit($this->getError());
		}
		return $this->save($data);
	}
	/**
	 * 编辑
	 * */
	public function edit(){
		$data=I("post.");
		if(empty($data)){
			$this->error="数据为空";
		}
		if(!$this->create($data)){
			exit($this->getError());
		}
		return $this->save($data);
	}
	/**
	 * 添加
	 */
	public function addData(){
		$data=I("post.");
		if(empty($data)){
			$this->error="数据为空";
		}
		$data['add_time']=date("Y-m-d H:i:s",$data['add_time']);
		if(!$this->create($data)){
			exit($this->error=$this->getError());
		}
		return $this->add($data);
	}
	/**
	 * 添加--包括上传图片缩略完整--未用js插件--停用
	 * */
	public function addData111(){
		$data=I("post.");
		if(empty($data)){
			$this->error="数据为空";
		}
		$upload = new \Think\Upload();// 实例化上传类
	  	$upload->savePath  = './Article/'; // 设置附件上传（子）目录
		$info   =   $upload->upload();
		if(!$info) {// 上传错误提示错误信息
				//$this->error=$upload->getError();
			}else{// 上传成功 获取上传文件信息
				foreach($info as $file){
				 //生成缩略图,按照原图的比例生成一个最大为50*50的缩略图并保存为thumb.jpg
				$image = new \Think\Image();
				$a=$image->open('./Uploads'.ltrim( $file['savepath'].$file['savename'],'.'));
				$image->thumb(50, 50)->save('./Uploads'.ltrim( $file['savepath']."Thumb_".$file['savename'],'.'));
		
			}
		}
		$filename="http://".$_SERVER['HTTP_HOST'].__ROOT__.'/Uploads'.ltrim( $file['savepath'].$file['savename'],'.');
		
		$filenameThumb="http://".$_SERVER['HTTP_HOST'].__ROOT__.'/Uploads'.ltrim( $file['savepath']."Thumb_".$file['savename'],'.');
		
		$data['category_id']=$data['category_id'];
		$data['add_time']=date("Y-m-d H:i:s",$data['add_time']);
		$data['thumb']=$filenameThumb;
		$data['picture']=$filename;
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
	/** 
	 * @param $data 传入的数组
	 * @param $tid 传入的最顶级id：0
	 * @return mixed 递归返回column的所有内容
	 * */
	public function getSon($data,$tid=0){
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
	 * @return mixed 递归返回的所有内容
	 * */  
	public function arr2tree1($tree, $rootId = 0) {  
	    $return = array();  
	    foreach($tree as $leaf) {  
	        if($leaf['column_pid'] == $rootId) {  
	            foreach($tree as $subleaf) {  
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