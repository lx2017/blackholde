<?php
/*
  事件监听类
  根据相应的功能一步一步扩展
*/
class Hook
{
	/*
            用于 存在相应的插件
	*/
	static private $tags = array();
	/*
      导入相应的插件信息
      @param:$tags=>插件名称
      @param:$resvie=>是否覆盖  TRUE表示要覆盖,FALSE表示不需要覆盖
	*/
    static public function import($tags,$resvie=TRUE)
    {
       try
       {
       	  
          if(!is_file(__LIBRARY__."/".$tags.".php")) throw new Exception("文件加载出错");
          //
          if(!isset(self::$tags[guid($tags)]) || empty(self::$tags[guid($tags)]))
          {

               include_once(__LIBRARY__."/".$tags.".php");
               self::$tags[guid($tags)] = new $tags($resvie);
          }
       }catch(Exception $e)
       {
             echo $e->getMessage();
       }
       return self::$tags[guid($tags)];

    }
	/*
      function:监听相应的插件
      @param:tag=>插件名称
      @param:param=>传入插件的相应参数
	*/
    static function listen($tag,$param=NULL)
    {
           if(!isset(self::$tags[guid($tag)]))
           {
              self::import('Template',$param);
           }
           //return self::$tags[guid($tag)];
    }
}
?>