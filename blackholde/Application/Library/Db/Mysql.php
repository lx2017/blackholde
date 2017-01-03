<?php
class Mysql
{
	private $config = array();
  private static $Driver_Object = NULL;
    
	public function __construct($config)
	{
        spl_autoload_register('Mysql::autoload');  



      
        if(self::$Driver_Object === NULL){
            self::$Driver_Object = new Driver($config);
        }
          
	}
  /*
   执行MYSQL 操作的表名
  */
  public function data($data = NULL)
  {

  }
    /**
    读取数据库的条件语句处理
    */
  public function where($where)
	{
      echo "<pre>";
      print_r($where);
      echo "</pre>";
      return $this; 
	}
	/*
     执行语句
    */
    public function query($sql)
    {
    
    }
   /*
    条件参数的过滤
   */
    public function where_filter($where)
    {

    }
    public function escapeString($str) {
        return str_ireplace("'", "''", $str);
    }
   
     /*
      连接魔术方法
     */
    protected function autoload($class)
     {
          try
          {
            
             if(self::$Driver_Object==NULL)
             {
                   $class_dir = LIBRARY_PATH."/".ucfirst($class).".php";

                   if(file_exists($class_dir))
                   {
                      require_once($class_dir);
                      
                   }
             }
          }catch(Exception $e)
          {


          }
     }
}
?>