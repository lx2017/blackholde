<?php
/*
   功能：处理框架原始初始化数据内库

*/
 
   function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 无参数时获取所有
    if (empty($name)) {
        return $_config;
    }
    // 优先执行设置获取或赋值
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 二维数组设置和获取支持
        $name = explode('.', $name);
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 批量设置
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
        return null;
    }
    return null; // 避免非法参数
}

class Init
{
    
   public $object = NULL;
   static $Object_Map = array();
   public function Start()
   {
     
      $this->APP();
      $this->load_file();

   }
   /*
       定义相关常量
   */
   final function APP()
   {
     
      //定义项目运行缓存文件
      defined('BLACK_RUNTIME_PATH') or define('BLACK_RUNTIME_PATH',BLACK_PATH.'Runtime/');
      //定义框架全局配置文件
      defined('BLACK_CONFIG_PATH') or define('BLACK_CONFIG_PATH',BLACK_PATH.'Application/Config/');
      //定义核心内库文件路径
      defined('BLACK_LIBRARAY_PATH') or define('BLACK_LIBRARAY_PATH',BLACK_PATH.'Application/Library/');

   }
   public function load_file()
   {

       //首先加载框架全局配置文件
       if(is_dir(BLACK_CONFIG_PATH) && $config_files = scandir(BLACK_CONFIG_PATH))
       {
           array_reduce($config_files,function($result,$filename){
            if($filename!='.' && $filename!='..')
            {
               $config = include_once BLACK_CONFIG_PATH.$filename;
               $file_pathinfo = pathinfo($filename);
               C($file_pathinfo['filename'],json_encode($config));
            }
           });
       }

       if(is_dir(BLACK_LIBRARAY_PATH."Url/") && $url_files = scandir(BLACK_LIBRARAY_PATH.'Url/'))
       {
            array_reduce($url_files,function($result,$filesname){

                 if($filesname!='.' && $filesname!='..')
                 {
                    $Object_Name = pathinfo($filesname)['filename'];
                   
                    include_once BLACK_LIBRARAY_PATH."Url/".$filesname;
                    if(FALSE===$this->Set_Map(pathinfo($Object_Name)['filename']))
                    {
                         //抛出异常...
                    }


                 }
            });
       }

   }
   public function Set_Map($Class)
   {
      
      try{
       if(!empty($Class) && !isset(self::$Object_Map[md5($Class)]))
       {
             $class = new ReflectionClass($Class);
             self::$Object_Map[md5($Class)] = $class->newInstanceArgs();

       }
         return TRUE;
      }catch(Exception $e)
      {
         return FALSE;
      }
   }
   public function Get_Map($Class='')
   {
       if(''===$Class)
       {
          return self::$Object_Map;
       }
       if(!isset(self::$Object_Map[MD5($Class)]))
       {
           return self::$Object_Map[MD5($Class)];
       }
       return NULL;
   }
 }
  ?>