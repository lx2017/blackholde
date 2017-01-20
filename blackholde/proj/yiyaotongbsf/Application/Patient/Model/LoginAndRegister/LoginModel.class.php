<?php
/**
 * Created by PhpStorm.
 * User: jiaolele
 * Date: 16/11/29
 *
 */
namespace Patient\Model\LoginAndRegister;
use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Patient\Model\InitUserId
 */
class LoginModel extends Model
{
    protected $tableName = "patient";
    /**data 为数组
    *return array()
    */
    public function FindByTiaoJian($data){
        $list = $this->where($data)->find();
        return $list;
    }
    /*根据手机查找用户*/
    public function FindByMobile($mobile){
        $boot =$this->where(array('mobile'=>$mobile))->find();
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