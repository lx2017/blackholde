<?php
class Journal
{
	 //日志运行存储文件
	 private $Runtime = "Runtime";
	 /*
      类库制动运行方法
	 */
	 public function Start()
	 {
	 	try
	 	{
          if(FALSE==$this->is_Runtime($this->Runtime)){
          	 throw new Exception("运行文件创建失败");
          }

        }
        catch(Exception $e)
        {
              echo $e->getMessage();
        }
	 }
	 /*
      判断日志运行存储文件是否存在
	 */
	 private function is_Runtime($filename=NULL)
	 {
        if($filename!=NULL)
        {
        	   $file = __ROOT__."/".$filename;
        	   if(!is_dir($file))
        	   {
        	   	  mkdir($file, 0777);
        	   }
        	   return TRUE;
        }
        return FALSE;
	 }
     /*
       返回日志运行文件目录
     */
     public function return_dir()
     {
     	  return __ROOT__."/".$this->Runtime;
     }
     /*
       组织URL
     */
     public function Create_Module(){
      $module_file = $this->return_dir()."/".$_SERVER['Project_Module'];
      echo "<pre>";
      print_r($_SERVER);
      echo "</pre>";
     }
}
?>