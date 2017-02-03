<?php
/*
   模板文件替换
*/
class Template
{
	 //模板内容
	 public $content = "";
	 public $_tparam = array();
	 public function __construct($param=NULL)
	 {

	 }
	 public function run($content,$param=NULL)
     {
       $this->content = $content;
       echo $this->content;
        
     }
    

}
?>