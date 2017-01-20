<?php
class Driver
{

      private  static $master_object = NULL;//主服务器连接
      private  $slave_object = NULL;//从服务器连接
      private  $PDOStatement = NULL;//SQL 语句操作集合
      private $Config_data = array(
            'address'=>'localhost',//数据连接地址
            'username'=>'',//数据用户名
            'password'=>'',//数据库密码
            'database'=>'',//数据库名称
            'master'=> array(),//主数据库
            'slave'=> array(),//从数据库
            'charset'=>  'utf8',      // 数据库编码默认采用utf8  

      	);
      protected $options = array(
        PDO::ATTR_CASE              =>  PDO::CASE_LOWER,
        PDO::ATTR_ERRMODE           =>  PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS      =>  PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES =>  false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
      );//MYSQL PDO 连接参数

	  public function __construct($config=array())
	  {
            try
            {
             
               $config = array_merge($this->Config_data,$config);
               $this->Analysis($config);
            }catch(Exception $e)
            {
               //echo $e->getMessage();
               die();
            }
	  }
	  /*
        连接数据库
        param:config->连接参数
        linkNums->连接

	  */
      protected function Connection($dsn,$username,$password,$flag='master')
      {
          try
          {
               /*
                 这里开始连接MYSQL PDO 数据库
               */
          static $connection = array();
          $guid_key = guid($dsn);

          if(!isset($connection[$guid_key]) && empty($connection[$guid_key]))
          {

           $connection[$guid_key]= new PDO($dsn,$username,$password,$this->options);
          }
          return $connection[$guid_key];
          }catch(Exception $e)
          {
                echo $e->getMessage();

          }
      }
	  /*
       分布式服务器,主服务器
	 */
		protected function Master_Distribution($config_data = array())
		{
            if(empty($config_data)) throw new Exception("主服务器配置为空");
            //这里随机读取主服务器配置数组，如果需要自定义参数的话，这里很方便扩展
            $config = $config_data[array_rand($config_data)];
            $dsn = "";
            if(!isset($config['address']) || empty($config['address']))
            {
            	 throw new Exception("主服务器地址错误");
            }
            if(!isset($config['database']) || empty($config['database']))
            {
            	 throw new Exception("主服务器数据名称填写错误");
            }
           $dsn = "mysql:dbname:".$config['database'].";host=".$config['address'].";".((isset($config['port']))?$config['port']:"3306");

            self::$master_object=$this->Connection($dsn,$config['username'],$config['password']);

           
            
		}
	/*
      分布式服务器，分服务器
	*/
	    protected function Slave_Distribution($config_data = array())
	    {

        
	    }
	 /*
       分析连接参数
	 */
       protected function Analysis($Config_data=array())
       {
             try
             {
             	//如果数据的配置文件中不存在主数据库和从数据的配置及默认数据的配置，
             	//抛出异常处理
                if(!isset($Config_data['master']) && !isset($Config_data['slave']) && !isset($Config_data['address']))
                {
                	throw new Exception("error....");
                	die();
                }
                //如果配置主数据库，并且不为空的情况下
                if(isset($Config_data['master']) && !empty($Config_data['master']))
                {
                  $this->Master_Distribution($Config_data['master']);
                }
                //如果配置的从数据库，并且不为空的情况下
                if(isset($Config_data['slave']) && !empty($Config_data['slave']))
                {
                 $this->Slave_Distribution($Config_data['slave']);
                }
               //如果主服务器和从服务器没有配置的情况下，就直接连接默认配置参数
                if((!isset($Config_data['slave']) && empty($Config_data['slave'])) && (!isset($Config_data['master']) && empty($Config_data['master'])))
                {
                 //$this->Connection($Config_data);
                }

             }catch(Exception $e)
             {

             }
       }
       //获取主服务器的资源
      public function get_master_object()
      {
      	 
      	return self::$master_object;
      }
      //获取从服务器的资源
      public function get_slave_object()
      {
      	  return $this->slave_object;
      }
      /*
       执行QUERY 操作，数据库的查询查询
      */
       public function query($sql)
       {

       }
       /*
          返回数据的结果集

       */
        protected function getResult()
        {


        }
	  /*
       释放资源
	  */
       public function __destory()
       {


       }
}
?>