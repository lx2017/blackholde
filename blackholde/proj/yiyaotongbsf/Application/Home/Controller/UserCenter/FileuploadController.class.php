<?php
namespace Home\Controller\UserCenter;
use Home\Controller\BaseController;
use Home\Controller\ImgcropController;
use Home\Controller\ImguploadController;
use Home\Library\UpImage\Fileuploadutil;
class FileuploadController extends BaseController
{

    public function file_Upload()
    {
        $result = $this->upload('file');
        if ($result) {
            echo json_encode(array('code' => 1, 'msg' => $this->getUploadPath(), 'id' => $_REQUEST['id']));
        } else {
            echo json_encode(array('code' => 0, 'msg' => $this->getErrorMsg(), 'id' => $_REQUEST['id']));
        }
    }


}

