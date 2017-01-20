<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/8
 * Time: 上午11:19
 */
namespace Admin\Model\Userinfo;
use Think\Model;
use Home\Model\UserCenter;

/**
 * 员工和组的关系
 * Class AuthGroupAccessModel
 * @package Admin\Model\Employee
 */
class TagsModel extends Model
{
    protected $tableName = "lable";
    /*用户标签*/
    public function Alltags(){
        $boot =$this->select();
        return $boot;
    }

}
