<?php

namespace Home\Controller\UserCenter;
use Home\Controller\BaseController;
use Home\Controller\UserCenter\FileuploadController;
use Home\Controller\UserCenter\ImguploadController;
use Home\Library\UpImage\Fileuploadutil;
use Home\Model\UserCenter\UserCenterModel;
use Home\Model\LoginAndRegister\CookieModel;
class ImgcropController extends BaseController
{
    public function crop()
    {
        $dao = new Fileuploadutil();
        $result = $dao->crop($_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'], 'avatar_file');

        if ($result) {
            echo json_encode(array('code' => 1, 'head_path' =>$dao->getCropUploadPath(),'img_path'=>$dao->getCropUploadPath(),'msg' => '成功', 'state' => 200));
        } else {
            echo json_encode(array('code' => 0, 'msg' => $dao->getErrorMsg()));
        }
    }
    public function cropwap()
    {
        $dao = new Fileuploadutil();
        $result = $dao->crop($_POST['x'], $_POST['y'], $_POST['width'], $_POST['height'], 'avatar_file');
        if ($result) {
            $user = new UserCenterModel();
            $cook = new CookieModel();
            $headpic =$dao->getCropUploadPath();
            $data['headpic'] =$headpic;
            $newid = $_COOKIE['newid'];/*获取用户newid*/
            if($newid){
                $list =$cook->FindidBynewid($newid);/*用户id*/
                $userid =$list['userid'];
                $boot =$user->UpInfo($userid,$data);
            }
            echo json_encode(array('code' => 1, 'head_path' =>$dao->getCropUploadPath(),'img_path'=>$dao->getCropUploadPath(),'msg' => '成功', 'state' => 200));
        } else {
            echo json_encode(array('code' => 0, 'msg' => $dao->getErrorMsg()));
        }
    }
}
