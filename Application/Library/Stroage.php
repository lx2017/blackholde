<?php
/*
  分布文件存储类
*/
Class Stroage
{
	 /*
      文件存储句柄
	 */
	 static protected $handler;
	/*
      连接文件存储句柄
	*/
    static public function connect($type='File',$param=array())
    {
          try
          {
             $file=__LIBRARY__."/Stroage/".ucwords($type);
             echo $file;
          }catch(Exception $e)
          {

          }
    }
}
?>