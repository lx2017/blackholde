<?php
/**
 * Created by PhpStorm.
 * User: zhangwei
 * Date: 16/6/28
 * Time: 下午3:03
 */
namespace Doctor\Model\Login;

use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Doctor\Model\InitUserId
 */
class RegisterModel extends Model
{
    protected $tableName = "user";

    public function InsertData($data)
    {
        $lastInsId = $this->add($data);
        return $lastInsId;

    }
    /*根据手机查找用户*/
    public function FindByMobile($mobile){
       // $boot =$this->where("mobile = ".$mobile)->find();
        $boot = $this->where(array('mobile'=>$mobile))->field('id,mobile')->find();
        return $boot;
    }
    /*根据手机查找用户*/
    public function FindinfoByMobile($mobile){

        $boot = $this->where(array('mobile'=>$mobile))->find();
        return $boot;
    }

}