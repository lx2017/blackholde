<?php
class Mysql
{
	private $config = array();
  private static $Driver_Object = NULL;
  //查询语句或者执行语句的条件表达式
  protected $options  =   array();
  // 数据库表名称
  protected $model = NULL;
	public function __construct($config)
	{
        spl_autoload_register('Mysql::autoload');  
        if(self::$Driver_Object === NULL){
            self::$Driver_Object = new Driver($config);
        }
          
	}
  /*
   执行MYSQL 操作的表名
   @param data 数据名称
  */
  public function data($data = NULL)
  {
     if($this->model==NULL)
     {
         $this->model = $data;
     }
  }
    /**
    读取数据库的条件语句处理,这里仿THINKPHP3.2
    @param where:条件语句
    @param parse:预处理语句
    */
  public function where($where,$parse=NULL)
	{
      if(is_string($where) && !is_null($parse))
      {
         
          if(is_string($parse) && !is_null($parse))
          {
               $parse = array_shift(func_get_args());
          }
          $parse = array_map(array($this,'escapeString'),$parse);
          $where =   vsprintf($where,$parse);
      }
      if(is_string($where) && '' != $where){
          $map    =   array();
          $map['_string']   =   $where;
          $where  =   $map;
        } 
     if(isset($this->options['where']))
     {
         $this->options['where'] =  array_merge($this->options['where'],$where);
     }else{
         $this->options['where'] = $where;
     }
     return $this;
	}
	 
  /*
    SQL 连接语句的拼接
    @param:join 为字符串
    @param:type 连接类型
    说明:这里的JOIN参数只支持字符串类型
  */
    public function join($join,$type="INNER")
    {
      try
      {

         if(!is_string($join))
         {
             throw new Exception("JOIN 操作只支持字符串");
             die();
         }
         $join = trim($join," ");//对字符串左右的空格进行过滤
         //对JOIN SQL 语句进行简单的拼接
         $join    =   (false !== stripos($join,'JOIN'))? $join :$type.' JOIN '.$join;
         if(!isset($this->options['join']))
         {
             $this->options['join'] = array();
         }

         $this->options['join'][] = $join;
        
         return $this;
         
      }catch(Exception $e)
      {
        echo $e->getMessage();
        die();
      }
    }

   /*
    条件参数的过滤
   */
    public function where_filter()
    {
            echo "<pre>";
            print_r($this->options['join']);
            echo "</pre>";
    }
    public function escapeString($str) {
        return addslashes(str_ireplace("'", "''", $str));
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
     /*
       MYSQL SQL 参数过滤
     */
    
}
?>