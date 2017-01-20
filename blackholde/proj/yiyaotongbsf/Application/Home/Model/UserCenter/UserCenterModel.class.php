<?php
/**
 * Created by PhpStorm.
 * User: zhangwei
 * Date: 16/7/4
 *
 */
namespace Home\Model\UserCenter;
use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Home\Model\InitUserId
 */
class UserCenterModel extends Model
{
    protected $tableName = "user";

    /**data 为数组
     *return id
     * 修改数据入库
     */
    public function UpInfo($id,$data){
        $boot = $this->where("id=$id")->save($data);
        //echo $this->getLastSql();
        return $boot;
    }

    /*检测邮箱用户是否存在*/
    public function SelectEmail($email,$mobile){
       $boot = $this->where("mobile not in ($mobile) and email = '".$email."'")->find();
        return $boot;
    }
    /*邮箱code和有效期入库*/
    public function InsertemailCode($mobile,$data){
        $boot = $this->where(array("mobile=$mobile"))->save($data);
        return $boot;
    }
    /*激活邮箱并入库*/
    public function CheckEmailCode($email,$code){
        $emailtime =time();
        $result = $this->where(array('emailcode'=>$code))->find();

        if($result){
            $emailcode_period =$result['emailcode_period'];

            if($emailtime > $emailcode_period ){
                $result['yn'] = 'no';/*失效*/
            }else{
                $data['email']=$email;
                $boot = $this->where(array("emailcode='".$code."'"))->save($data);
                if($boot !== false){
                    $result['yn'] ='yes';
                }
            }
        }else{
            $result='';
        }
        return $result;

    }
    /*根据用户id查询用户信息*/
    public function FindByUserid($id){
        $boot =$this->where(array('id'=>$id))->find();
        return $boot;
    }
    /*查询用户原始密码是否正确*/
    public function SelectPassword($password,$mobile){
        $boot =$this->where(array('password'=>$password,'mobile'=>$mobile))->find();
        return $boot;
    }
    /*更新用户手机号*/
    public function UpuserMobile($mobile,$newmobile){
        $data['mobile'] =$newmobile;
        $result = $this->where("mobile =$mobile")->save($data); // 根据条件更新记录
        return $result;
    }
    /*更新用户手机号*/
    public function UpusernewMobile($id,$newmobile){
        $data['mobile'] =$newmobile;
        $result = $this->where("id =$id")->save($data); // 根据条件更新记录
        return $result;
    }
    /*解绑第三方用户信息*/
    public function DelthirdName($name){
        $result = $this->where(array("third_name ='".$name."'"))->setField('third_name','');
        return $boot;
    }
    /*查询所有相关信息*/
    public function Allusers(){
        $boot =$this->where('status= 0')->select();
        return $boot;
    }
    /*所有黑名单类表*/
    public function Allbackusers(){
        $boot =$this->where('status= 1')->select();
        return $boot;
    }
    /*黑名单查询列表*/
    public function Allbackusersbyseach($value,$status){
        $where = "status =$status and nickname like '%".$value."%'";
        $boot =$this->where($where)->select();
        return $boot;
    }
    /*添加多个黑名单*/
    public function AddbackInfo($id,$data){
        $boot = $this->where("id in ($id)")->save($data);
        return $boot;
    }
    /*查看手机是否存在*/
    public function Checkismobile($id,$mobile){
        $boot = $this->where("id not in ($id) and mobile =$mobile")->select();
        return $boot;
    }
    /*查看原始密码是否正确*/
    public function FindidBypassword($password,$id){
        $boot =$this->where("id =$id and password = '".$password."'")->select();
        return $boot;
    }


}