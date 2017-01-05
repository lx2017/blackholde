<?php
namespace Home\Controller\UserCenter;
use Home\Controller\BaseController;
use Home\Controller\UserCenter\FileuploadController;
use Home\Controller\UserCenter\ImgcropController;
use Home\Library\UpImage\Fileuploadutil;
class ImguploadController extends BaseController
{
    public function createThumbImg()
    {
        $result = $this>uploadAndMkThumb(300, 300, 'file');
        if ($result) {
            echo json_encode(array('code' => 1,'full_thumb_path'=>$this->getThumbUploadPath() ,'thumb_path' => $this->getThumbUploadPath(), 'msg' => '成功', 'img_path' => $this->getUploadPath(), 'id' => 1,'full_img_path'=>$this->getUploadPath()));
        } else {
            echo json_encode(array('code' => 0, 'msg' => $this->getErrorMsg()));
        }

    }
}
