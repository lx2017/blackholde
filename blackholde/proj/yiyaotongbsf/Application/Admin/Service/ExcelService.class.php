<?php

/**
 * excel处理类
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/10/30
 * Time: 9:18
 */
namespace Admin\Service;
class ExcelService
{
    /**
     * 引入phpexcel文件
     */
    public function __construct()
    {
        set_time_limit(0);
        vendor("PHPExcel.PHPExcel");
    }

    /**
     * 读取excel数据
     * @param $filename string 文件名
     * @param $type int 类型:默认数据从第2行开始, 为1时,从第一行开始
     * @return array
     */
    public function read($filename,$type=0){
        //读取excel信息
        $extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
        if ($extension =='xlsx') {
            $objReader = new \PHPExcel_Reader_Excel2007();
        } else if ($extension =='xls') {
            $objReader = new \PHPExcel_Reader_Excel5();
        }
        $objReader->setReadDataOnly(true);
        $objPHPExcel = $objReader->load($filename);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        //判断类型
        $start = 2;
        if($type) $start = 1;
        //读取信息到数组中
        $excelData = array();
        for ($row = $start; $row <= $highestRow; $row++) {
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $excelData[$row][] =(string)$objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
        }
        return $excelData;
    }

    /**
     * 导出excel
     * @param array $data
     * @param array $config 配置表头
     * @return string
     */
    public function push($data,array $config){
        if(!$config) exit('请传入excel表头信息!');
        $titles = array_values($config);
        $keys = array_keys($config);
        date_default_timezone_set('Europe/London');
        $objPHPExcel = new \PHPExcel();
        //设置属性
        $objPHPExcel->getProperties()->setCreator("系统");
        //设置表头
        $key = null;
        foreach($titles as $kk=>$item){
            $key = $this->numberToLetter($kk);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key.'1', $item);
            //设置单元格宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension($key)->setWidth(20);
        }
        //设置数据
        foreach($data as $k => $v){
            $num=$k+2;
            foreach($keys as $ii=>$jj){
                $key = $this->numberToLetter($ii);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($key.$num, $v[$jj]);
            }
        }
        $objPHPExcel->getActiveSheet()->setTitle('信息');
        $objPHPExcel->setActiveSheetIndex(0);
        //保存到文件中
        $root = C('UPLOADIFY_EXCEL_CONF');
        $root = $root['rootPath'];
        $path = $root.date('YmdHis').rand(10,99).'.xls';
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($path);
        return $path;
    }

    /**
     * 数字转字母,0=>A
     * @param $num
     * @return mixed
     */
    private function numberToLetter($num){
        $data = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        if(isset($data[$num])){
            return $data[$num];
        }else{
            exit('表格太大了吧,z都装不下了');
        }
    }
}