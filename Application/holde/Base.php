<?php
/*
   框架基类
*/

  class Base
  {
       /*
           加载控制器的参数
           @access protected
       */
       protected $param = array();
       /*
       式图解析类
       */
  	   protected static $view = NULL;
  	   /*
          获取客户端POST,GET数据
  	   */

       /*
        基础类的构造函数
       */
       public function __construct()
       {
          
             if(self::$view==NULL){
                 self::$view = new view();
            }
       }
  	   private  function param($name,$type="get")
  	   {
  	   	  
          $client_data = Follow::$Client_Object->_Query($type,$name);
          return $client_data;

       }
       public function get_param($name=NULL,$default=NULL)
       {

       	   $client_data = $this->param($name);
       	   if(empty($client_data) && $default!=NULL)
       	   {
               return $default;
       	   }
       	   return $client_data;

       }
       /*
           POST 请求补丁
       */
       protected function post_param($name=NULL,$default=NULL)
       {
          return Follow::$Client_Object->post_param($name,$default);
       }
       //判断请求是否为AJAX
       protected function Isajax()
       {
       	   return Follow::$Client_Object->ajaxJson();
       }
       //返回相应的格式
       /*
            $data 格式化数据
            $type:格式类型
       */
       protected function Return_data($data,$type='json')
       {
            switch(strtoupper($type))
            {
            	  case 'JSON':
            	        header('Content-Type:application/json; charset=utf-8');
            	        exit(json_encode($data));
            	        break;
            	  case 'XML':
            	        header('Content-Type:application/xml;charset=utf-8');
            	        exit(xml_encode($data));
            	        break;
            	  case 'JSONP':
            	        header('Content-Type:application/json;charset=utf-8');
            	        $handler  =   'callback';
            	        exit($handler.'('.json_encode($data).');');
            	        break;
            	  case 'EVAL':
            	        header('Content-Type:application/html;charset=utf-8');
            	        exit($data);
            	        break;
            	  default:
            	        //
            }
       }
  	  
  	   /*
           框架缓存机制
           缓存机制目前支持两种1文件缓存2MEMCACHE缓存
           默认为文件缓存
           $type:缓存类型
           $data:缓存数据
           $timeout:缓存时间
  	   */
       protected function Cache($data,$type,$config=array())
       {
       	  
          try
          {
              static $_instance = array();
              $guid = $type.guid($config);
              if(!isset($_instance[$guid]) && require_once(HOLDE_ROUTE."Cache.php"))
              {
                 $class = new Cache();
                 $_instance[$guid] = $class->getInstance($type,$config);
              }
              return $_instance[$guid];
            
          }catch(Exception $e)
          {

          }
       }
       /*
           框架模板分配变量
       */
        public function assign($name=NULL,$value=NULL)
        {
           
            self::$view->assign($name,$value);
        }
        public function __set($name=NULL,$value=NULL)
        {
            $this->assign($name,$value);
        }
        /*
            现实模板
        */
        public function display($templateFile="",$cache=FALSE,$dir="")
       {
          // $templateFile = ($templateFile=="")?get_class($this):$templateFile;
          
           self::$view->display($templateFile,$dir="",$action=get_class($this),$cache=FALSE);
       }
  }
?>