<?php


namespace Admin\Controller\Usercenter;
use Admin\Controller\AdminController;
use Admin\Model\Userinfo\UserinfoModel;
use Admin\Model\Userinfo\TagsModel;
use Home\Model\UserCenter\UserCenterModel;

/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */
class UsercenterController extends AdminController
{

    /*用户管理入口*/
    public function index()
    {
        $nickname = I('nickname');
        $key = I('key');
        $dao =new UserCenterModel();
        $tag =new TagsModel();
        if(I('value')){
            $value =I('value');
            $userlist =$dao->Allbackusersbyseach($value,0 );
        }else{
            $userlist =$dao->Allusers();
        }

        $alltags = $tag->Alltags();
        $this->assign('userlist',$userlist);
        $this->assign('alltags',$alltags);
        $this->display('Userinfo/userlist');
    }
    /*用户密码重置*/
    public function resetpassword(){
        $password =I('password');
        $password =think_ucenter_md5($password, UC_AUTH_KEY);
        $userid =I('post.userid');
        $dao =new UserCenterModel();
        $data['password']=$password;
        $boot =$dao->UpInfo($userid,$data);
        if($boot){
           echo 'ok';
        }else{
           echo 'no';
        }
    }
    /*加入黑名单*/
    public function addbackname(){
        $userid =I('post.userid');
        $dao =new UserCenterModel();
        $data['status'] =1;
        $boot =$dao->UpInfo($userid,$data);
        if($boot){
         echo 'ok';die;
        }else{
         echo 'no';die;
        }
    }
    /*给用户添加标签*/
    public function addtags(){
        $userid =I('post.userid');
        $tags =I('post.tags');
        $dao =new UserCenterModel();
        $data['tags'] =$tags;
        $boot =$dao->UpInfo($userid,$data);
        if($boot){
            echo 'ok';die;
        }else{
            echo 'no';die;
        }
    }
    /*黑名单列表*/
    public function listbackname(){
        $dao =new UserCenterModel();
        $tag =new TagsModel();
        $alltags = $tag->Alltags();

        if(I('value')){
            $value =I('value');
            $userlist =$dao->Allbackusersbyseach($value,1 );
        }else{
            $userlist =$dao->Allbackusers();
        }
        $this->assign('alltags',$alltags);
        $this->assign('userlist',$userlist);
        $this->display('Userinfo/backuserlist');
    }
    /*移出黑名单*/
    public function delbackname(){
        $userid =I('post.userid');
        $dao =new UserCenterModel();
        $data['status'] =0;
        $boot =$dao->UpInfo($userid,$data);
        if($boot){
            echo 'ok';
        }else{
            echo 'no';
        }
    }
    /*添加多个黑名单*/
    public function morebackname(){
        $userid =I('post.userid');

        $dao =new UserCenterModel();
        $data['status'] =1;
        $boot =$dao->AddbackInfo($userid,$data);
        if($boot){
            echo 'ok';die;
        }else{
            echo 'no';die;
        }
    }
    /*批量删除多个黑名单*/
    public function delmorebackname(){
        $userid =I('post.userid');
        $dao =new UserCenterModel();
        $data['status'] =0;
        $boot =$dao->AddbackInfo($userid,$data);
        if($boot){
            echo 'ok';die;
        }else{
            echo 'no';die;
        }
    }
    /*批量添加标签*/
    public function moretags(){
        $userid =I('post.userid');
        $tags =I('post.tag');
        $dao =new UserCenterModel();
        $data['tags'] =$tags;
        $boot =$dao->AddbackInfo($userid,$data);
        if($boot){
            echo 'ok';die;
        }else{
            echo 'no';die;
        }
    }
}
