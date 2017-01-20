<?php
/**
 * 环信即时通信
 * Created by PhpStorm.
 * auth: dower
 * Date: 2016/11/03 0026
 * Time: 下午 15:31
 */

class Huanxin
{
    private $client_id;
    private $client_secret;
    private $org_name;
    private $app_name;
    private $file_dir;
    private $cursor_dir;
    private $url;
    public $http_code;//http状态码---可做超时重试机制等
    /**
     * 初始化配置
     * @param $options array 配置信息
     */
    public function __construct($options = array())
    {
        $options = $options ?: C('HX_CONFIG');
        $this->client_id = isset ($options ['client_id']) ? $options ['client_id'] : '';
        $this->client_secret = isset ($options ['client_secret']) ? $options ['client_secret'] : '';
        $this->org_name = isset ($options ['org_name']) ? $options ['org_name'] : '';
        $this->app_name = isset ($options ['app_name']) ? $options ['app_name'] : '';
        $this->file_dir = isset ($options ['file_dir']) ? $options ['file_dir'] : '';
        $this->cursor_dir = isset ($options ['cursor_dir']) ? $options ['cursor_dir'] : '';
        if (!empty ($this->org_name) && !empty ($this->app_name)) {
            $this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
        }
    }

    /**
     *获取token
     */
    public function getToken()
    {
        //缓存taken
        $tokenResult = S('hx_taken');
        if (empty($tokenResult)) {
            $options = array(
                "grant_type" => "client_credentials",
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret
            );
            $body = json_encode($options);
            $url = $this->url . 'token';
            $tokenResult = $this->postCurl($url, $body, $header = array());
            if ($tokenResult) {
                $exp = $tokenResult['expires_in'] - 2;
                if ($exp) S('hx_taken', $tokenResult, $exp);
            }
        }
        return "Authorization:Bearer " . $tokenResult['access_token'];
    }

    /**
     * 授权注册
     * @param $username
     * @param $password
     * @return mixed
     */
    public function createUser($username, $password)
    {
        $url = $this->url . 'users';
        $options = array(
            "username" => $username,
            "password" => $password
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header);
        return $result;
    }

    /**
     * 批量注册
     * @param $options
     * @return mixed
     */
    public function createUsers($options)
    {
        $url = $this->url . 'users';
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header);
        return $result;
    }

    /**
     * 重置用户密码
     * @param $username
     * @param $newpassword
     * @return mixed
     */
    public function resetPassword($username, $newpassword)
    {
        $url = $this->url . 'users/' . $username . '/password';
        $options = array(
            "newpassword" => $newpassword
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header, "PUT");
        return $result;
    }

    /**
     * 获取单个用户
     * @param $username
     * @return mixed
     */
    public function getUser($username)
    {
        $url = $this->url . 'users/' . $username;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        return $result;
    }

    /**
     * 获取批量用户-不分页
     * @param int $limit
     * @return mixed
     */
    public function getUsers($limit = 0)
    {
        if (!empty($limit)) {
            $url = $this->url . 'users?limit=' . $limit;
        } else {
            $url = $this->url . 'users';
        }
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        return $result;
    }

    /**
     * 获取批量用户-分页
     * @param int $limit
     * @param string $cursor
     * @return mixed
     */
    public function getUsersForPage($limit = 0, $cursor = '')
    {
        $url = $this->url . 'users?limit=' . $limit . '&cursor=' . $cursor;

        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, "GET");
        if (!empty($result["cursor"])) {
            $cursor = $result["cursor"];
            $this->writeCursor("userfile.txt", $cursor);
        }
        return $result;
    }

    /**
     * 创建目录
     * @param $dir
     * @return bool
     */
    private function mkdir($dir){
        if(is_dir($dir)){
            return true;
        }
        if(mkdir($dir, 0777, true)){
            return true;
        } else {
            exit("目录 {$dir} 创建失败！");
        }
    }

    /**
     * 写入cursor
     * @param $filename
     * @param $content
     */
    public function writeCursor($filename, $content)
    {
        //判断文件夹是否存在，不存在的话创建
        if (!file_exists($this->cursor_dir)) {
            $this->mkdir($this->cursor_dir);
        }
        $myfile = @fopen($this->cursor_dir . $filename, "w+") or die("Unable to open file!");
        @fwrite($myfile, $content);
        fclose($myfile);
    }

    /**
     * 读取cursor
     * @param $filename
     * @return string
     */
    public function readCursor($filename)
    {
        //判断文件夹是否存在，不存在的话创建
        if (!file_exists($this->cursor_dir)) {
            $this->mkdir($this->cursor_dir);
        }
        $file = $this->cursor_dir . $filename;
        $fp = fopen($file, "a+");//这里这设置成a+
        if ($fp) {
            while (!feof($fp)) {
                //第二个参数为读取的长度
                $data = fread($fp, 1000);
            }
            fclose($fp);
        }
        return $data;
    }

    /**
     * 删除单个用户
     * @param $username
     * @return mixed
     */
    public function deleteUser($username)
    {
        $url = $this->url . 'users/' . $username;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;
    }

    /**
     * 批量删除用户
     * @param $limit 300-500
     * @return mixed
     */
    public function deleteUsers($limit)
    {
        $url = $this->url . 'users?limit=' . $limit;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;

    }

    /**
     * 修改用户昵称
     * @param $username
     * @param $nickname
     * @return mixed
     */
    public function editNickname($username, $nickname)
    {
        $url = $this->url . 'users/' . $username;
        $options = array(
            "nickname" => $nickname
        );
        $body = json_encode($options);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header, 'PUT');
        return $result;
    }

    /**
     * 添加好友
     * @param $username
     * @param $friend_name
     * @return mixed
     */
    public function addFriend($username, $friend_name)
    {
        $url = $this->url . 'users/' . $username . '/contacts/users/' . $friend_name;
        $header = array($this->getToken(), 'Content-Type:application/json');
        $result = $this->postCurl($url, '', $header, 'POST');
        return $result;
    }

    /**
     * 删除好友
     * @param $username
     * @param $friend_name
     * @return mixed
     */
    public function deleteFriend($username, $friend_name)
    {
        $url = $this->url . 'users/' . $username . '/contacts/users/' . $friend_name;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;

    }

    /**
     * 查看好友
     * @param $username
     * @return mixed
     */
    public function showFriends($username)
    {
        $url = $this->url . 'users/' . $username . '/contacts/users';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;

    }

    /**
     * 查看用户黑名单
     * @param $username
     * @return mixed
     */
    public function getBlacklist($username)
    {
        $url = $this->url . 'users/' . $username . '/blocks/users';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;

    }

    /**
     * 加入黑名单
     * @param $username
     * @param $usernames
     * @return mixed
     */
    public function addUserForBlacklist($username, $usernames)
    {
        $url = $this->url . 'users/' . $username . '/blocks/users';
        $body = json_encode($usernames);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $body, $header, 'POST');
        return $result;

    }

    /**
     * 移除黑名单
     * @param $username
     * @param $blocked_name
     * @return mixed
     */
    public function deleteUserFromBlacklist($username, $blocked_name)
    {
        $url = $this->url . 'users/' . $username . '/blocks/users/' . $blocked_name;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'DELETE');
        return $result;

    }

    /**
     * 查看用户是否在线
     * @param $username
     * @return mixed
     */
    public function isOnline($username)
    {
        $url = $this->url . 'users/' . $username . '/status';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;

    }

    /**
     * 查看用户离线消息数
     * @param $username
     * @return mixed
     */
    public function getOfflineMessages($username)
    {
        $url = $this->url . 'users/' . $username . '/offline_msg_count';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;

    }

    /**
     * 查看某条消息的离线状态----deliverd 表示此用户的该条离线消息已经收到
     * @param $username
     * @param $msg_id
     * @return mixed
     */
    public function getOfflineMessageStatus($username, $msg_id)
    {
        $url = $this->url . 'users/' . $username . '/offline_msg_status/' . $msg_id;
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;

    }

    /**
     * 禁用账户
     * @param $username
     * @return mixed
     */
    public function deactiveUser($username)
    {
        $url = $this->url . 'users/' . $username . '/deactivate';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header);
        return $result;
    }

    /**
     * 解禁用户账号
     * @param $username
     * @return mixed
     */
    public function activeUser($username)
    {
        $url = $this->url . 'users/' . $username . '/activate';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header);
        return $result;
    }

    /**
     * 强制用户下线
     * @param $username
     * @return mixed
     */
    public function disconnectUser($username)
    {
        $url = $this->url . 'users/' . $username . '/disconnect';
        $header = array($this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        return $result;
    }

    /**
     * 上传图片或文件
     * @param $filePath
     * @return mixed
     */
    public function uploadFile($filePath)
    {
        $url = $this->url . 'chatfiles';
        $file = file_get_contents($filePath);
        $body['file'] = $file;
        $header = array('enctype:multipart/form-data', $this->getToken(), "restrict-access:true");
        $result = $this->postCurl($url, $body, $header, 'XXX');
        return $result;

    }

    /**
     * 下载文件或图片
     * @param $uuid
     * @param $shareSecret
     * @return string
     */
    public function downloadFile($uuid, $shareSecret)
    {
        $url = $this->url . 'chatfiles/' . $uuid;
        $header = array("share-secret:" . $shareSecret, "Accept:application/octet-stream", $this->getToken());
        $result = $this->postCurl($url, '', $header, 'GET');
        $filename = md5(time() . mt_rand(10, 99)) . ".png"; //新图片名称
        if (!file_exists($this->file_dir)) {
            $this->mkdir($this->file_dir);
        }
        $file = @fopen($this->file_dir . $filename, "w+");//打开文件准备写入
        @fwrite($file, $result);//写入
        fclose($file);//关闭
        return $filename;
    }

    /**
     * 下载图片缩略图
     * @param $uuid
     * @param $shareSecret
     * @return string
     */
    public function downloadThumbnail($uuid, $shareSecret)
    {
        $url = $this->url . 'chatfiles/' . $uuid;
        $header = array("share-secret:" . $shareSecret, "Accept:application/octet-stream", $this->getToken(), "thumbnail:true");
        $result = $this->postCurl($url, '', $header, 'GET');
        $filename = md5(time() . mt_rand(10, 99)) . "th.png"; //新图片名称
        if (!file_exists($this->file_dir)) {
            $this->mkdir($this->file_dir);
        }
        $file = @fopen($this->file_dir . $filename, "w+");//打开文件准备写入
        @fwrite($file, $result);//写入
        fclose($file);//关闭
        return $filename;
    }

    /**
     * 发送文本消息
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $content
     * @param $ext
     * @return mixed
     */
    public function sendText($from = "admin", $target_type, $target, $content, $ext=null)
    {
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "txt";
        $options['msg'] = $content;
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    /**
     * 发送透传消息
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $action
     * @param $ext
     * @return mixed
     */
    public function sendCmd($from = "admin", $target_type, $target, $action, $ext)
    {
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "cmd";
        $options['action'] = $action;
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    /**
     * 发图片消息
     * @param $filePath
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $filename
     * @param $ext
     * @return mixed
     */
    public function sendImage($filePath, $from = "admin", $target_type, $target, $filename, $ext)
    {
        $result = $this->uploadFile($filePath);
        $uri = $result['uri'];
        $uuid = $result['entities'][0]['uuid'];
        $shareSecret = $result['entities'][0]['share-secret'];
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "img";
        $options['url'] = $uri . '/' . $uuid;
        $options['filename'] = $filename;
        $options['secret'] = $shareSecret;
        $options['size'] = array(
            "width" => 480,
            "height" => 720
        );
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        //$b=json_encode($body,true);
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    /**
     * 发语音消息
     * @param $filePath
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $filename
     * @param $length
     * @param $ext
     * @return mixed
     */
    public function sendAudio($filePath, $from = "admin", $target_type, $target, $filename, $length, $ext)
    {
        $result = $this->uploadFile($filePath);
        $uri = $result['uri'];
        $uuid = $result['entities'][0]['uuid'];
        $shareSecret = $result['entities'][0]['share-secret'];
        $url = $this->url . 'messages';
        $body['target_type'] = $target_type;
        $body['target'] = $target;
        $options['type'] = "audio";
        $options['url'] = $uri . '/' . $uuid;
        $options['filename'] = $filename;
        $options['length'] = $length;
        $options['secret'] = $shareSecret;
        $body['msg'] = $options;
        $body['from'] = $from;
        $body['ext'] = $ext;
        $b = json_encode($body);
        $header = array($this->getToken());
        //$b=json_encode($body,true);
        $result = $this->postCurl($url, $b, $header);
        return $result;
    }

    /**
     * 发视频消息
     * @param $filePath
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $filename
     * @param $length
     * @param $thumb
     * @param $thumb_secret
     * @param $ext
     * @return mixed
     */
    public function sendVedio($filePath,$from="admin",$target_type,$target,$filename,$length,$thumb,$thumb_secret,$ext){
        $result=$this->uploadFile($filePath);
        $uri=$result['uri'];
        $uuid=$result['entities'][0]['uuid'];
        $shareSecret=$result['entities'][0]['share-secret'];
        $url=$this->url.'messages';
        $body['target_type']=$target_type;
        $body['target']=$target;
        $options['type']="video";
        $options['url']=$uri.'/'.$uuid;
        $options['filename']=$filename;
        $options['thumb']=$thumb;
        $options['length']=$length;
        $options['secret']=$shareSecret;
        $options['thumb_secret']=$thumb_secret;
        $body['msg']=$options;
        $body['from']=$from;
        $body['ext']=$ext;
        $b=json_encode($body);
        $header=array($this->getToken());
        //$b=json_encode($body,true);
        $result=$this->postCurl($url,$b,$header);
        return $result;
    }
    
    /**
     * 发文件消息
     * @param $filePath
     * @param string $from
     * @param $target_type
     * @param $target
     * @param $filename
     * @param $length
     * @param $ext
     * @return mixed
     */
    public function sendFile($filePath,$from="admin",$target_type,$target,$filename,$length,$ext){
        $result=$this->uploadFile($filePath);
        $uri=$result['uri'];
        $uuid=$result['entities'][0]['uuid'];
        $shareSecret=$result['entities'][0]['share-secret'];
        $url=$GLOBALS['base_url'].'messages';
        $body['target_type']=$target_type;
        $body['target']=$target;
        $options['type']="file";
        $options['url']=$uri.'/'.$uuid;
        $options['filename']=$filename;
        $options['length']=$length;
        $options['secret']=$shareSecret;
        $body['msg']=$options;
        $body['from']=$from;
        $body['ext']=$ext;
        $b=json_encode($body);
        $header=array(getToken());
        //$b=json_encode($body,true);
        $result=postCurl($url,$b,$header);
        return $result;
    }
    
    /**
     * 获取app中的所有群组----不分页
     * @param int $limit
     * @return mixed
     */
    public function getGroups($limit=0){
        if(!empty($limit)){
            $url=$this->url.'chatgroups?limit='.$limit;
        }else{
            $url=$this->url.'chatgroups';
        }

        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,"GET");
        return $result;
    }
    
    /**
     * 获取app中的所有群组---分页
     * @param int $limit
     * @param string $cursor
     * @return mixed
     */
    public function getGroupsForPage($limit=0,$cursor=''){
        $url=$this->url.'chatgroups?limit='.$limit.'&cursor='.$cursor;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,"GET");

        if(!empty($result["cursor"])){
            $cursor=$result["cursor"];
            $this->writeCursor("groupfile.txt",$cursor);
        }
        //var_dump($GLOBALS['cursor'].'00000000000000');
        return $result;
    }
    
    /**
     * 获取一个或多个群组的详情
     * @param $group_ids
     * @return mixed
     */
    public function getGroupDetail($group_ids){
        $g_ids=implode(',',$group_ids);
        $url=$this->url.'chatgroups/'.$g_ids;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 创建一个群组
     * @param $options
     * @return mixed
     */
    public function createGroup($options){
        $url=$this->url.'chatgroups';
        $header=array($this->getToken());
        $body=json_encode($options);
        $result=$this->postCurl($url,$body,$header);
        return $result;
    }
    
    /**
     * 修改群组信息
     * @param $group_id
     * @param $options
     * @return mixed
     */
    public function modifyGroupInfo($group_id,$options){
        $url=$this->url.'chatgroups/'.$group_id;
        $body=json_encode($options);
        $header=array($this->getToken());
        $result=$this->postCurl($url,$body,$header,'PUT');
        return $result;
    }
    
    /**
     * 删除群组
     * @param $group_id
     * @return mixed
     */
    public function deleteGroup($group_id){
        $url=$this->url.'chatgroups/'.$group_id;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 获取群组中的成员
     * @param $group_id
     * @return mixed
     */
    public function getGroupUsers($group_id){
        $url=$this->url.'chatgroups/'.$group_id.'/users';
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 群组单个加人
     * @param $group_id
     * @param $username
     * @return mixed
     */
    public function addGroupMember($group_id,$username){
        $url=$this->url.'chatgroups/'.$group_id.'/users/'.$username;
        $header=array($this->getToken(),'Content-Type:application/json');
        $result=$this->postCurl($url,'',$header);
        return $result;
    }
    
    /**
     * 群组批量加人
     * @param $group_id
     * @param $usernames
     * @return mixed
     */
    public function addGroupMembers($group_id,$usernames){
        $url=$this->url.'chatgroups/'.$group_id.'/users';
        $body=json_encode($usernames);
        $header=array($this->getToken(),'Content-Type:application/json');
        $result=$this->postCurl($url,$body,$header);
        return $result;
    }
    
    /**
     * 群组单个减人
     * @param $group_id
     * @param $username
     * @return mixed
     */
    public function deleteGroupMember($group_id,$username){
        $url=$this->url.'chatgroups/'.$group_id.'/users/'.$username;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 群组批量减人
     * @param $group_id
     * @param $usernames
     * @return mixed
     */
    public function deleteGroupMembers($group_id,$usernames){
        $url=$this->url.'chatgroups/'.$group_id.'/users/'.$usernames;
        //$body=json_encode($usernames);
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 获取一个用户参与的所有群组
     * @param $username
     * @return mixed
     */
    public function getGroupsForUser($username){
        $url=$this->url.'users/'.$username.'/joined_chatgroups';
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 群组转让
     * @param $group_id
     * @param $options
     * @return mixed
     */
    public function changeGroupOwner($group_id,$options){
        $url=$this->url.'chatgroups/'.$group_id;
        $body=json_encode($options);
        $header=array($this->getToken());
        $result=$this->postCurl($url,$body,$header,'PUT');
        return $result;
    }
    
    /**
     * 查询一个群组黑名单用户名列表
     * @param $group_id
     * @return mixed
     */
    public function getGroupBlackList($group_id){
        $url=$this->url.'chatgroups/'.$group_id.'/blocks/users';
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 群组黑名单单个加人
     * @param $group_id
     * @param $username
     * @return mixed
     */
    public function addGroupBlackMember($group_id,$username){
        $url=$this->url.'chatgroups/'.$group_id.'/blocks/users/'.$username;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header);
        return $result;
    }
   
    /**
     * 群组黑名单批量加人
     * @param $group_id
     * @param $usernames
     * @return mixed
     */
    public function addGroupBlackMembers($group_id,$usernames){
        $url=$this->url.'chatgroups/'.$group_id.'/blocks/users';
        $body=json_encode($usernames);
        $header=array($this->getToken());
        $result=$this->postCurl($url,$body,$header);
        return $result;
    }
    
    /**
     * 群组黑名单单个减人
     * @param $group_id
     * @param $username
     * @return mixed
     */
    public function deleteGroupBlackMember($group_id,$username){
        $url=$this->url.'chatgroups/'.$group_id.'/blocks/users/'.$username;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 群组黑名单批量减人
     * @param $group_id
     * @param $usernames
     * @return mixed
     */
    public function deleteGroupBlackMembers($group_id,$usernames){
        $url=$this->url.'chatgroups/'.$group_id.'/blocks/users';
        $body=json_encode($usernames);
        $header=array($this->getToken());
        $result=$this->postCurl($url,$body,$header,'DELETE');
        return $result;
    }
    
    /**
     * 创建聊天室
     * @param $options
     * @return mixed
     */
    public function createChatRoom($options){
        $url=$this->url.'chatrooms';
        $header=array($this->getToken());
        $body=json_encode($options);
        $result=$this->postCurl($url,$body,$header);
        return $result;
    }
    
    /**
     * 修改聊天室信息
     * @param $chatroom_id
     * @param $options
     * @return mixed
     */
    public function modifyChatRoom($chatroom_id,$options){
        $url=$this->url.'chatrooms/'.$chatroom_id;
        $body=json_encode($options);
        $result=$this->postCurl($url,$body,$header,'PUT');
        return $result;
    }
    
    /**
     * 删除聊天室
     * @param $chatroom_id
     * @return mixed
     */
    public function deleteChatRoom($chatroom_id){
        $url=$this->url.'chatrooms/'.$chatroom_id;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 获取app中所有的聊天室
     * @return mixed
     */
    public function getChatRooms(){
        $url=$this->url.'chatrooms';
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,"GET");
        return $result;
    }
    
    /**
     * 获取一个聊天室的详情
     * @param $chatroom_id
     * @return mixed
     */
    public function getChatRoomDetail($chatroom_id){
        $url=$this->url.'chatrooms/'.$chatroom_id;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 获取一个用户加入的所有聊天室
     * @param $username
     * @return mixed
     */
    public function getChatRoomJoined($username){
        $url=$this->url.'users/'.$username.'/joined_chatrooms';
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'GET');
        return $result;
    }
    
    /**
     * 聊天室单个成员添加
     * @param $chatroom_id
     * @param $username
     * @return mixed
     */
    public function addChatRoomMember($chatroom_id,$username){
        $url=$this->url.'chatrooms/'.$chatroom_id.'/users/'.$username;
        //$header=array($this->getToken());
        $header=array($this->getToken(),'Content-Type:application/json');
        $result=$this->postCurl($url,'',$header);
        return $result;
    }
    
    /**
     * 聊天室批量成员添加
     * @param $chatroom_id
     * @param $usernames
     * @return mixed
     */
    public function addChatRoomMembers($chatroom_id,$usernames){
        $url=$this->url.'chatrooms/'.$chatroom_id.'/users';
        $body=json_encode($usernames);
        $header=array($this->getToken());
        $result=$this->postCurl($url,$body,$header);
        return $result;
    }
    
    /**
     * 聊天室单个成员删除
     * @param $chatroom_id
     * @param $username
     * @return mixed
     */
    public function deleteChatRoomMember($chatroom_id,$username){
        $url=$this->url.'chatrooms/'.$chatroom_id.'/users/'.$username;
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 聊天室批量成员删除
     * @param $chatroom_id
     * @param $usernames
     * @return mixed
     */
    public function deleteChatRoomMembers($chatroom_id,$usernames){
        $url=$this->url.'chatrooms/'.$chatroom_id.'/users/'.$usernames;
        //$body=json_encode($usernames);
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,'DELETE');
        return $result;
    }
    
    /**
     * 导出聊天记录----不分页
     * @param $ql
     * @return mixed
     */
    public function getChatRecord($ql){
        if(!empty($ql)){
            $url=$this->url.'chatmessages?ql='.$ql;
        }else{
            $url=$this->url.'chatmessages';
        }
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,"GET");
        return $result;
    }
    
    /**
     * 导出聊天记录---分页
     * @param $ql
     * @param int $limit
     * @param $cursor
     * @return mixed
     */
    public function getChatRecordForPage($ql,$limit=0,$cursor){
        if(!empty($ql)){
            $url=$this->url.'chatmessages?ql='.$ql.'&limit='.$limit.'&cursor='.$cursor;
        }
        $header=array($this->getToken());
        $result=$this->postCurl($url,'',$header,"GET");
        $cursor=$result["cursor"];
        $this->writeCursor("chatfile.txt",$cursor);
        //var_dump($GLOBALS['cursor'].'00000000000000');
        return $result;
    }
    
    /**
     * $this->postCurl方法
     * @param $url
     * @param $body
     * @param $header
     * @param string $type
     * @return mixed
     */
    public function postCurl($url,$body,$header,$type="POST"){
        //1.创建一个curl资源
        $ch = curl_init();
        //2.设置URL和相应的选项
        curl_setopt($ch,CURLOPT_URL,$url);//设置url
        //1)设置请求头
        //array_push($header, 'Accept:application/json');
        //array_push($header,'Content-Type:application/json');
        //array_push($header, 'http:multipart/form-data');
        //设置为false,只会获得响应的正文(true的话会连响应头一并获取到)
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt ( $ch, CURLOPT_TIMEOUT,5); // 设置超时限制防止死循环
        //设置发起连接前的等待时间，如果设置为0，则无限等待。
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,5);
        //将curl_exec()获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //2)设备请求体
        if (count($body)>0) {
            //$b=json_encode($body,true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);//全部数据使用HTTP协议中的"POST"操作来发送。
        }
        //设置请求头
        if(count($header)>0){
            curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
        }
        //上传文件相关设置
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);// 从证书中检查SSL加密算

        //3)设置提交方式
        switch($type){
            case "GET":
                curl_setopt($ch,CURLOPT_HTTPGET,true);
                break;
            case "POST":
                curl_setopt($ch,CURLOPT_POST,true);
                break;
            case "PUT"://使用一个自定义的请求信息来代替"GET"或"HEAD"作为HTTP请									                     求。这对于执行"DELETE" 或者其他更隐蔽的HTT
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
                break;
            case "DELETE":
                curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
                break;
        }


        //4)在HTTP请求中包含一个"User-Agent: "头的字符串。-----必设

        curl_setopt($ch, CURLOPT_USERAGENT, 'SSTS Browser/1.0');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

        curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
        //5)


        //3.抓取URL并把它传递给浏览器
        $res=curl_exec($ch);
        $this->http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);//http状态码
        $result=json_decode($res,true);
        //4.关闭curl资源，并且释放系统资源
        curl_close($ch);
        if(empty($result))
            return $res;
        else
            return $result;

    }
}
?>