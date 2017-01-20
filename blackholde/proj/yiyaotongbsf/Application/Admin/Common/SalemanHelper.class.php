<?php

namespace Admin\Common;

use Think\Upload;

/**
 * 该文件为县总，地总，省总，大区经理，总部的常用方法
 */
class SalemanHelper {

    public static function upload() {
        $setting = C('ATTACHMENT_UPLOAD');
        /* 调用文件上传组件上传文件 */
        $uploader = new Upload($setting, 'Local');
        $info = $uploader->upload($_FILES);
        if ($info) {
            foreach ($info as $file) {
                $file_name = $setting ['rootPath'] . $file ['savepath'] . $file ['savename'];
                $re[$file ['key']] = substr($file_name, 1);
            }
            return $re;
        } else {
            return false;
        }
    }

}
