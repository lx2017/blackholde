<?php
/**
 * Created by PhpStorm.
 * User: zhangwei
 * Date: 16/6/29
 *
 */
namespace Doctor\Model\Login;
use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Doctor\Model\InitUserId
 */
class LoginModel extends Model
{
    protected $tableName = "doctor";
    /**data 为数组
    *return array()
    */
    public function FindByTiaoJian($data){
        $list = $this->where($data)->find();
        return $list;
    }
    /*根据手机查找用户*/
    public function FindByMobile($mobile){
        $boot =$this->where(array('mobile'=>$mobile,'status'=>0))->find();
        return $boot;
    }
    /*根据用户名查找用户*/
    public function FindByName($name){
        $boot =$this->where("third_name = '".$name."'")->find();
        return $boot;
    }
    /*根据用户id找用户*/
    public function FindById($id){
        $boot =$this->where("id = $id")->find();
        return $boot;
    }
    /*查找微博用户是否存在*/
    public function SelectWb($name){
        $boot =$this->where("third_name = '".$name."'")->find();
        return $boot;
    }
    /*判断用户是否登录*/
    public function Is_login(){
        $newid=$_COOKIE['newid'];
        if($newid){
            return true;
        }else{
            return false;
        }
    }

}