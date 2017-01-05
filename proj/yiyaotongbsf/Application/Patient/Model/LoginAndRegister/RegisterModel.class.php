<?php
/**
 * Created by PhpStorm.
 * User: jiaolele
 * Date: 16/11/28
 * Time: 下午3:03
 */
namespace Patient\Model\LoginAndRegister;

use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Patient\Model\InitUserId
 */
class RegisterModel extends Model
{
    protected $tableName = "patient";

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