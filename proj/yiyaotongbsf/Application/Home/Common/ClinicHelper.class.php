<?php
/**
 * 诊所模块工具方法
 */
namespace Home\Common;
use Think\Upload;

class ClinicHelper{

    /*
     * 上传图片
     */
    public static function upload($config)
    {
        $setting = C ( 'CLINIC.'.$config);
        $uploader = new Upload ( $setting, 'Local' );
        $info = $uploader->upload ( $_FILES );
        if ($info) {
            foreach($info as $file){
                $file_name = $setting ['urlPath'] .$file ['savepath'] .$file ['savename'];
                return $file_name;
            }
        }else{
            return false;
        }
    }

    /*
     * 上传excel
     */
    private function uploadExcel()
    {
        $setting = C ( 'TEMP_EXCEL_UPLOAD' );
        $uploader = new Upload ( $setting, 'Local' );
        $info = $uploader->upload ( $_FILES );
        if ($info) {
            foreach($info as $file){
                $file_name = $setting ['rootPath'] .$file ['savepath'] .$file ['savename'];
                return $file_name;
            }
        }else{
            return false;
        }
    }

    public static function excelRead($start=1,$highest){
        //$file=$this->uploadExcel();echo $file;exit;
        $file='./Uploads/Temp/excel/2016-11-14/test2.xls';
        vendor('PHPExcel.PHPExcel');
        $Obj = new \PHPExcel_Reader_Excel2007;
        if(!$Obj->canRead($file)){
            $Obj = new \PHPExcel_Reader_Excel5;
            if(!$Obj->canRead($file)){
                echo 'no Excel';
                return ;
            }
        }
        $Obj->setReadDataOnly(true);
//读取demo.xls文件
        $phpExcel = $Obj->load($file);
//获取当前活动sheet
        $objWorksheet = $phpExcel->getActiveSheet();
//获取行数
        $highestRow = $objWorksheet->getHighestRow();
//获取列数
        $highestColumn = $highest?$highest:$objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
//循环输出数据
        $data = array();
        for($row = $start; $row <= $highestRow; ++$row) {
            for($col = 0; $col < $highestColumnIndex; ++$col) {
                $val = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
                $data[$row-$start][$col] = trim($val);
            }
        }
        return $data;

    }
}
