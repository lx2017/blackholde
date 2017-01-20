<?php
/*
  PHP 缓存机制
*/

class Cache
{
   private $handler = NULL;//连接句柄
   private $option_config = array();//相应的配置
   private static $_Instance = NULL;
  
   public  function getInstance($type,$options = array())
   {
     static $_instance = array();//定义相关的静态数组
     $guid_str = $type.guid($options);
     if(!isset($_instance[$guid_str]))
     {
     	   $_instance[$guid_str] = $this->connect($type,$options);
     }
     $this->handler=$_instance[$guid_str];
     
   }
   public function connect($type,$options)
   {


   }
   protected function _get($name)
   {
   	   $this->get($name);
   }
   protected function _set($name,$value)
   {
   	   $this->set($name,$value);
   }
   public function __call($method,$args)
   {
   	     try
   	     {

   	     }catch(Exception $e)
   	     {


   	     }
   }

}
?>