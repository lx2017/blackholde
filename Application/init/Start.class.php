<?php
/*
   项目启动加载和初始化文件
   功能: 检测PHP 相关变量和相关环境
         初始化相关系统常量
         定义全局变量.......
    个人认为这个类承担着黑洞项目的整体初始化功能，所以Start类整体FINAL
    类的设计来讲采用的单利模式
*/

final class Start
{
	  /*
            定义类对象变量，用于存储Start对象，
	  */

      private static  $object = NULL;
      private static  $_instance = NULL;
      private static  $_function_result = "";
      private static  $Extend_Object = array();
      private function  __construct(){}
      /*
        定义getInstance 获取类对象
        param=>ClassName:为类名

      **/
      public static function getInstance($ClassName= "Init",$args = NULL)
      {
          if(!(self::$object instanceof self))
      	  {

       	  	  self::$object = new self;
      	  }
      	
      	  return self::$object;

      }
     /**
      启动方法
     */
      public function init()
      {
         if(is_dir(INIT_PATH) && $Init_filenames=scandir(INIT_PATH))
         {
               $init_array = array_reduce($Init_filenames,function($result,$filename){
               if($filename!="." && $filename!==".." && ($init_filename=explode(".",$filename)[0]) != get_class($this))
               {
                try{
                 require_once(INIT_PATH.$filename);
                 $Config_object = new ReflectionClass($init_filename);
                 $instance = $Config_object->newInstanceArgs();
                 if(TRUE==$Config_object->hasMethod('Get_Map')  && TRUE==$Config_object->hasMethod('Start'))
                 {
                      $instance->Start();
                      if(($data=$instance->Get_Map())!=NULL)
                      {
                           self::$Extend_Object = array_merge(self::$Extend_Object,$data);
                      }

                 }else{

                  throw new Exception("xxxxxx");
                  //异常类暂定
                 }
                }catch(Exception $e)
                {
                   echo $e->getMessage();
                }
                 
                 
               }
           });
          
         }
        
      }
    /**
     function:使用初始化方法
    */
     public function get_fun($class)
     {

        if(!empty(self::$Extend_Object) && isset(self::$Extend_Object[md5($class)]))
        {
             return self::$Extend_Object[Md5($class)];
        }
        return NULL;
     }
      
     
}
?>