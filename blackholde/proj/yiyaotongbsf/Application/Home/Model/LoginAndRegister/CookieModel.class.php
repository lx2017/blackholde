<?php
/**
 * Created by PhpStorm.
 * User: zhangwei
 * Date: 16/6/29
 *
 */
namespace Home\Model\LoginAndRegister;
use Think\Model;

/**
 * 初始化用户id
 * Class InitUserIdModel
 * @package Home\Model\InitUserId
 */
class CookieModel extends Model
{
    protected $tableName = "user_cookie";
    /**data 为数组
     *return array()
     */
    /*g根据id 生成随机数入库与 对应 返回当当前的用户随机id */
    public function creatnewid($id){
        $path = $_SERVER['HTTP_HOST'];
        $nowtime =time();
        $userdata = $this->where(array('userid'=>$id))->find();
        if(I('post.un-login')){
            $rememberuser =I('post.un-login');/*记住一个月密码*/
        }else{
            $rememberuser='';
        }

        if(count($userdata)>0){
            if($nowtime > $userdata['expire_time']){/**/
                $uniqid_id =uniqid();
                if(!empty($rememberuser)){
                    $expire_time = $nowtime+3600*24*30;/*一个月*/
                }else{
                    $expire_time = $nowtime+7200;/*2小时*/
                }
                $data['newid'] = $uniqid_id;
                $data['expire_time'] = $expire_time;
                $boot =$this->where("userid=$id")->save($data); // 根据条件更新记录

              //  $result = $this->where("userid=>$id")->setField(array('newid','expire_time'),array($uniqid_id,$expire_time));
            }else{
                $uniqid_id = $userdata['newid'];
            }
            setcookie('newid',$uniqid_id,$expire_time,'/',$path);
        }else{
            $uniqid_id =uniqid();
            if(!empty($rememberuser)){
                $expire_time = $nowtime+3600*24*30;/*一个月*/
                $data['expire_time'] = $nowtime+3600*24*30;/*一个月*/
            }else{
                $expire_time = $nowtime+7200;/*2小时*/
                $data['expire_time'] = $nowtime+7200;/*2小时*/
            }
            $data['newid'] =$uniqid_id;
            $data['userid'] =$id;
            $lastInsId = $this->add($data);
            if($lastInsId){
                setcookie('newid',$uniqid_id,$expire_time,'/',$path);
            }
        }

    }
    /*根据用户newid 查找用户id*/
    public function FindidBynewid($newid){
        $condition['newid']=$newid;
        $list = $this->where($condition)->find();
        return $list;
    }
}