<?php
/**
 * Created by hbuilder.
 * User: yuxiang
 * Date: 16/6/16
 * Time: 下午14:00
 */
namespace Admin\Model\User;

use Think\Model;

/**
 * 前台用户模块模型
 * Class UserModel
 * @package Admin\Model\User
 */
class UsersModel extends Model
{
	protected $tableName = 'user';
	protected $error;
	
	protected $_validate=array(
		//array('title','require','名称必须'),
		//array('column_title','','栏目名称已经存在',0,'unique',1)
	);
	
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
	
	
	
	
}