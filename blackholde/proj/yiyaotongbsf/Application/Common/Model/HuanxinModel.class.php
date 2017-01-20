<?php
/**
 * 环信工具
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/8 0008
 * Time: 下午 14:18
 */

namespace Common\Model;

class HuanxinModel
{
    public $hx = null;
    public $error = null;

    public function __construct()
    {
        vendor("Huanxin.Huanxin");//引入环信接口库
        $this->hx = new \Huanxin();
    }

    /**
     * 创建环信用户
     * @param $username
     * @param $password
     * @return bool
     */
    public function createUser($username, $password){
        $rst = $this->hx->createUser($username,$password);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 批量注册环信用户
     * @param $options
     * @return bool
     */
    public function createUsers($options){
        if(count($options)>50){
            $this->error = '批量添加不能大于50人';
            return false;
        }
        $rst = $this->hx->createUsers($options);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 检测该用户是否存在
     * @param $username
     * @return bool
     */
    public function getUser($username){
        $rst = $this->hx->getUser($username);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 删除环信用户
     * @param $username
     * @return bool
     */
    public function deleteUser($username){
        $rst = $this->hx->deleteUser($username);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 修改环信用户
     * @param $username
     * @param $newpassword
     * @return bool
     */
    public function modifyUser($username,$newpassword){
        $rst = $this->hx->resetPassword($username, $newpassword);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 添加好友
     * @param $username
     * @param $friend_name
     * @return mixed
     */
    public function addFriend($username, $friend_name){
        $rst = $this->hx->addFriend($username, $friend_name);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 删除好友
     * @param $username
     * @param $friend_name
     * @return mixed
     */
    public function deleteFriend($username, $friend_name){
        $rst = $this->hx->addFriend($username, $friend_name);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 获得用户的离线消息数
     * @param $username
     * @return mixed
     */
    public function getOfflineCount($username){
        $rst = $this->hx->getOfflineMessages($username);
        if(is_array($rst) && !isset($rst['error'])){
            return $rst['data'][$username];
        }
        return false;
    }

    /**
     * 上传图片或文件
     * @param $filePath
     * @return mixed false或array('uuid'=>'','share_secret'=>'')
     */
    public function uploadFile($filePath){
        $rst = $this->hx->uploadFile($filePath);
        if(is_array($rst) && !isset($rst['error'])){
            return array(
                'uuid'=>$rst['entities'][0]['uuid'],
                'share_secret'=>$rst['entities'][0]['share-secret'],
            );
        }
        return false;
    }

    /**
     * 下载图片或文件
     * @param $uuid
     * @param $shareSecret
     * @return mixed 文件路径或false
     */
    public function downloadFile($uuid, $shareSecret){
        $rst = $this->hx->downloadFile($uuid, $shareSecret);
        if(is_array($rst)){
            return false;
        }
        $conf = C('HX_CONFIG');
        return $conf['file_dir'].$rst;
    }

    /**
     * 下载图片缩略图
     * @param $uuid
     * @param $shareSecret
     * @return mixed 文件路径或false
     */
    public function downloadThumbnail($uuid, $shareSecret){
        $rst = $this->hx->downloadThumbnail($uuid, $shareSecret);
        if(is_array($rst)){
            return false;
        }
        $conf = C('HX_CONFIG');
        return $conf['file_dir'].$rst;
    }

    /**
     * 发送文本消息
     * @param $target array 接收对象数组
     * @param $content
     * @param string $from
     * @param string $target_type
     * @param array $ext 扩展信息
     * @return bool
     */
    public function sendText($target, $content, $from = "admin", $target_type='users', $ext=null){
        $rst = $this->hx->sendText($from, $target_type, $target, $content, $ext);
        if(is_array($rst) && !isset($rst['error'])){
            return true;
        }
        return false;
    }

    /**
     * 发送图文信息
     * @param $path string 图片路径
     * @param $data array 额外数据
     * @param $target array 目标对象
     * @param $content string 发送内容
     * @param $from string 发送人
     * @return array|bool
     */
    public function sendImgAndText($path,$data,$target,$content,$from){
        //发送图片
        $rst = $this->uploadFile($path);
        if($rst){
            //发送文字信息,带上图片信息扩展信息
            $ext = array_merge($rst,$data);
            $rst = $this->sendText($target,$content,$from,'users',$ext);
            if($rst){
                return $ext;
            }
        }
        return false;
    }
}