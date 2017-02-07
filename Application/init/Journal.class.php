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
	 private function is_Runtime($filename=NULL,$path=NULL)
	 {
        if($filename!=NULL)
        {
        	   //$file = __ROOT__."/".$filename;
        	   if($path==NULL)
        	   {
        	   	  $file = __ROOT__."/".$filename;
        	   }else{
        	   	  $file = $path."/".$filename;
        	   }
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
     
     public function Create_Module(){
      try{

	      $file = $this->return_dir()."/".$_SERVER['Project_Module'];
	      if(!is_dir($file))
	      {
               if(TRUE==$this->is_Runtime($_SERVER['Project_Module'],$path=$this->return_dir()))
               {
                 return $file;

               }else{
                 throw new Exception("缓存记录创建失败");

               }
	      }
	      return $file;
	  }catch(Exception $e)
	  {
           echo $e->getMessage();
	  }
      
     }
     public function Record()
     {
     	//生成相应的目录
     	$file_dir=$this->Create_Module();
     	$string="";
     	$string.="[".date('c',time())."]\r\n";
        $string.="url=".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\r\n";
        $string.="method=".$_SERVER['REQUEST_METHOD']."\r\n";
        $string.="param=".http_build_query($_REQUEST)."\r\n\r\n";
        if(FALSE!==$file_dir)
        {
        	$log = $file_dir."/".date("Y-m-d").".log";
        	
             $handler=fopen($log, "a")or die("Unable to open file!");
          
           fwrite($handler,$string);
           fclose($handler);
        
        }

     }
}
?>