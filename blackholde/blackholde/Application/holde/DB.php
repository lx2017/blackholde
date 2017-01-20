<?php
/*
   function:连接数据资源的中间层，部分代码参考THINKPHP
*/
class DB
{
	  static private $_instance = array();//存放数据连接
	  static private $instance = NULL;//数据库的当前资源
	  /*
        function:获取数据连接配置参数，支持参数传入

	  */
      static function getConfig($optionConfig = array())
      {
         
          try
          {
          	 
             //$config = guid(md5(serialize($optionConfig)));
            $config = empty($optionConfig)?self::parseConfig($optionConfig):$optionConfig;
            $config = guid(md5(serialize($config)));
             if(!isset(self::$_instance[$config]) || empty(self::$_instance($config)))
             {

                $options = self::parseConfig($optionConfig);
                $type=(isset($options['type']) && !empty($options['type']))?$options['type']:'Mysql';
              
               if(is_file($database_file=LIBRARY_PATH."/Db/".ucfirst($type).".php") && require_once($database_file))
               {

                 $class = ucfirst($type);
                 if(class_exists($class))
                 {

                    self::$_instance[$config] = new $class($options);
                 }else{

                 	  throw new Exception("database error....");
                 }
               }

             }
          }catch(Exception $e)
          {

            //echo $e->getMessage();
          }
          self::$instance = self::$_instance[$config];
          return self::$instance;
      }
      /*
       产生具体解析方案
      */
      static private function parseConfig($config=array())
      {
      	  $return_config = array();//定义空数据组
          if(is_string($config))
          {
            $return_config = self::parseDNS($config);
          }else if(!empty($config) && is_array($config)){
            
            $return_config = array(
           	  //如果没有传入数据库类型默认为MYSQL
              'type'=> isset($config['db_type'])?$config['db_type']:'mysql',
              'address'=>isset($config['address'])?$config['address']:NULL,
              //数据库用户名，如果不存在就默认为NULL
              'username'=>isset($config['username'])?$config['username']:NULL,
              //数据库密码，如果不存在默认为NULL
              'password'=>isset($config['passowrd'])?$config['password']:NULL,
              //连接数据库名称
              'database'=>isset($config['database'])?$config['database']:NULL,
              //从数据库连接信息,如果不存在默认为空
              'slave'=>isset($config['slave'])?$config['slave']:NULL,
              //主数据库连接信息，如果不存在默认为NULL,如果与address同时配置组成主数据库
              'master'=>isset($config['master'])?$config['slave']:NULL,
              //数据编码默认为UTF-8
              'charset'=>isset($config['charset'])?$config['charset']:'utf8',

           	);
           
         }else{
            
             $layer = C('Config')?json_decode(C('Config'),true):array();
         	 $return_config = array(
         	 	//读取项目配置文件
         	 	//读取数据连接类型
                 'type'=>defined('db_type')?db_type:NULL,
                 //数据连接地址
                 'address'=>defined('address')?address:NULL,
                 //数据库用户名
                 'username'=>defined('username')?username:NULL,
                 //数据库密码
                 'password'=>defined('password')?password:NULL,
                 //连接数据
                 'database'=>defined('database')?database:NULL,
                 /*
                   数据库MASTER连接信息，如果项目配置文件定义就读取配置文件主数据库连接
                 */
                 'master'=>defined('master')?master:((isset($layer['master']))?$layer['master']:NULL),
                 /*
                   数据库SLAVE 连接信息,如果项目没有配置就默就读取配置文件从数据库连接信息
                 */
                 'slave'=>defined('slave')?slave:((isset($layer['slave']))?$layer['slave']:NULL),
                 //数据编码默认为UTF-8
                 'charset'=>defined('charset')?charset:'utf8',

         	 	);

         }
          return $return_config;
          

      }
      /*
      DNS PDO连接数据 解析
      */
      static private function parseDNS($config)
      {
      	 /*
            [scheme] => mysql
		    [host] => localhost
		    [port] => 3306
		    [user] => username
		    [pass] => passwd
		    [path] => /DbName
		    [query] => param1=val1&param2=val2
		    [fragment] => utf8
      	 */
         if(empty($config)) return FALSE;
         $info = parse_url($config);
         if(empty($info)) return FALSE;
         $return_config = array(
             'type'=>isset($info['scheme'])?$info['scheme']:'mysql',
             'address'=>isset($info['host'])?$info['host']:'localhost',
             'username'=>isset($info['user'])?$info['user']:NULL,
             'password'=>isset($info['pass'])?$info['pass']:NULL,
             'dbname'=>isset($info['path'])?$info['paht']:NULL,
             'charset'=>isset($info['fragment'])?$info['fragment']:'utf8',

         	);
         return $return_config;

      }
      /*
        
      */
     public function __call($method,$args)
     {
        
     }
}
?>