<?php
/**
  function:处理HTTP传送过来的数据
  author=>black
  这个类专门处理HTTP传送数据的过滤，POST，GET，REQUEST，PUT,DELETE数据传送方式
  过滤方式包括PHP内建函数的自动过滤和自定义函数的过滤
*/
Class Data
{
	private $input = NULL;
	private $client_input_data = NULL;
  public $Query_url =  NULL;
  public function __construct()
    {
        $this->Analysis_Url();
      
       
    }
    /*
      定义PHP 魔术方法 $methods 为访问方法,$Args 传递参数
      接收HTTP GET,POST请求数据
      如果存在就返回，如果不存在就返回NULL

    */
    protected  function Init($Args=NULL)
    {
           
              if($this->client_input_data===NULL)
              {

              	  return NULL;
              }

              /*
                    如果传入的参数为空位置没有传入参数，则返回全部的HTTP请求参数
              */
    	   	  if(is_null($Args) || empty($Args)){

    	   	  	     $client_data = $this->client_input_data;
    	   	  }else{

    	   	  /*
					如果传入 参数不为空,开始检查传入的参数的形式支持如下格式
					例如=>"name.age","name"
					如果比配为第一种格式就转换成数组的形式

    	   	  */
    	   	  if(!is_null($Args) && !empty($Args))
    	   	  {
    	   	  	  if(strpos($Args[0],",")) $Args = explode(",",$Args[0]);
    	   	      /*
                       array_keys=>获取HTTP 参数的键值
                       array_intersect=>把HTTP的键值和传入的参数取出数组的交集
                       $client_data=>定义空的数组，循环$intersect_keys 取出HTTP数组中的存在的值，把不存在的值销毁
                       返回结果
    	   	      */
    	   	      $intersect_keys = array_intersect(array_keys($this->client_input_data),$Args);
    	   	      $client_data = array();
    	   	      foreach($intersect_keys as $key=>$value)
    	   	      {
    	   	      	   if(isset($this->client_input_data[$value])) $client_data[$value] = $this->client_input_data[$value];
    	   	      	   unset($intersect_keys[$key]);
    	   	      }
    	   	      

    	   	  }
    	   	}
    	   	  if(TRUE===$this->Http_Data($client_data)) return $client_data;
    	   
    }
    /*
        数据的默认过滤操作
    */
    protected  function Client_filter($data = array())
    {
      if(empty($data)) return NULL;
      $filter_func = '';
      $result_client_data = array();
      if(!empty(C('Config')))
      {
      	   $config = json_decode(C('Config'),TRUE);
      	   if(!empty($config['HTTP_filter']) && isset($config['HTTP_filter']))
      	   {
      	   	   $filter_func = strstr($config['HTTP_filter'],",")?explode(",",$config['HTTP_filter']):$config['HTTP_filter'];
      	   	  
      	   }
      }

      if($filter_func!='' && is_string($filter_func))
      {
           $result_client_data = array_map($filter_func, $data);
      }else if($filter_func!='' && is_array($filter_func))
      {

      	 $result_client_data = array_reduce($filter_func,function($result,$filter){

      	 	   if(function_exists($filter))
      	 	   {
      	 	   	   $result=array_map($filter,$result);
      	 	   }
      	 	   unset($filter);
      	 	   return $result;
      	 },$data);
      	 
      }
     
     $this->client_input_data=$this->Reg_Http($result_client_data);

    }
    /**
      HTTP 协议传递过来的参数做基本的正则验证,已过滤XSSL 和注入攻击
    */
    protected function Reg_Http($data=NULL)
    {
      /**
        定义相关过滤的JS 代码
      */ 
      $filter_string=Array('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/','/script/','/javascript/','/vbscript/','/expression/','/applet/','/meta/','/xml/','/blink/','/link/','/style/','/embed/','/object/','/frame/','/layer/','/title/','/bgsound/','/base/','/onload/','/onunload/','/onchange/','/onsubmit/','/onreset/','/onselect/','/onblur/','/onfocus/','/onabort/','/onkeydown/','/onkeypress/','/onkeyup/','/onclick/','/ondblclick/','/onmousedown/','/onmousemove/','/onmouseout/','/onmouseover/','/onmouseup/','/onunload/');
      //if(!get_magic_quotes_gpc()) $data = addslashes($data);
      $result_data = NULL;//定义需要返回数据类型为NULL
      /*
              如果传入的数据不为空并且是数组类型就做如下动作
              先对数组做FOREACH 循环操作对数组中的每个值做JS 过滤
              如果传入的数据不为数组为字符串并且不为空
              就直接对值做JS 过滤操作
      */
      if(!empty($data) && is_array($data))
      {
      	 foreach($data as $key=>$value)
      	 {
      	 	   
      	 	   $result_data[$key] = preg_replace($filter_string,'',$value);
      	 }
      }else if(is_string($data) && $data!=NULL){
              
              $result_data = preg_replace($filter_string,'',$data);
         
      }
      /*
         如果对框架原有的JS 或者过滤操作不认可，没关系这里可以采用设计模式中责任链的做法
         调用Other_Filter 方法进行过滤处理
      */
      if($this->Other_Filter($result_data))  return $data;

    }
    protected function Other_Filter($data)
    {
          return TRUE;
    }
    private function Http_Data($data)
    {
    	  return TRUE;
    }
    /*
         判断HTTP请求是否为AJAX
         如果为AJAX 请求就返回TRUE,否则返回FALSE
    */
    public function ajaxJson()
    {
         
         
         if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"])=="xmlhttprequest"){ 
              return TRUE;
        }else{ 
             return FALSE;
        };

    }
    /*
       功能：接受客户端HTTP 请求发的数据流
       param:
             $method=>HTTP 请求方法目前支持,GET,POST,PUT.默认为NULL
       return:
             如果接受到相关数据就返回,如果没有就返回NULL
    */
    protected  function Analysis_Client($method=NULL)
    {
       /*
            三元运算符.如果调用该方法没有传递$method参数，就从
            CGI全局变量中读取请求方法，如果传递就用型参数据
       */
       $method = ($method==NULL)?$_SERVER['REQUEST_METHOD']:$method;//获取请求的方式

       $client_data = array();//定义空数组用于接收客户端HTTP请求数据
       /**
             把前面$method 变量转换为小写，这里特别说明如果未来要做相应的扩展处理
             例如要加HTTP DELETE方式
             switch($strtolower($method))
             {
                  case "delete":
                        coding....
                  case 'request':
                        coding.....
             }


       */

       switch(strtolower($method))
       {
           case "post":
              $client_data = &$_POST;
              break;
           case "get":
              $client_data = &$_GET;
              break;
           case "put":
             parse_str(file_get_contents('php://input'), $_PUT);
             $client_data = $_PUT;
              break;
        }
       /*
         如果没有接受到HTTP 数据就返回NULL
       */
        
       return (!empty($client_data))? $client_data: NULL;
       
    }
    /*
     解析URL 查询和访问控制器和方法
    */
    protected function Analysis_Url()
    {
       if ( ! isset($_SERVER['REQUEST_URI'],$_SERVER['SCRIPT_NAME']))
          {
            return NULL;
          }
          $Analysis_url = parse_url('http://dummy'.$_SERVER['REQUEST_URI']);
          $Analysis_query = isset($Analysis_url['query'])?$Analysis_url['query']:NULL;
          $Analysis_path = isset($Analysis_url['path'])?$Analysis_url['path']:NULL;
          
          if(strpos($Analysis_path,$_SERVER['SCRIPT_NAME']) === 0 && $Analysis_path !== $_SERVER['SCRIPT_NAME'])
          {
              
              $this->Query_url =  (string)substr($Analysis_path,strlen($_SERVER['SCRIPT_NAME']));
          }else if(strpos($Analysis_path,$_SERVER['SCRIPT_NAME']) === 0 && $Analysis_path === $_SERVER['SCRIPT_NAME'])
          {
             $this->Query_url = "Home/Index/index";
          }

    }
    /*
        这个方法是该类为数不多对外暴露的方法，用于获取HTTP 相关请求数据,默认为GET请求数据
        思路：||
            1->首先判断<<案例:$client_object->_Query("get.name"),get为请求方式,name为请求数据>> 如果为这种方式读取数据
            就分割数据$type=get,$name=name
            如果$client_object->Query();
            $type = "get"
            $name = NULL;
            如果$client_obect->Query("get")=>表示读取所有的GET数据,POST 同理
            2->
               根据type的不同读取相应的数据，在同一做过滤动作
               如果$name为NULL 就返回全部数据否则就方法$name对于的值

    **/
    public function _Query($type="get",$name=NULL,$data = array())
    {
        
       
        strpos($type,".") && list($type,$name) = explode(".",$type);
        
        if(!empty($this->Analysis_Client($type))) $this->input=$this->Analysis_Client($type);

       
        $this->Client_filter($this->input);
        if(NULL===$name) return $this->client_input_data;
        
        if(isset($this->client_input_data[$name]) && !empty($this->client_input_data[$name]))
        {
             
              return $this->client_input_data[$name];
        }

        return NULL;
     } 
     /*
        发现记录POST 数据接收有问题，这里对POST数据的接受做修复
     */
     public function post_param($name=NULL,$default=NULL)
     {
         $post_param  = &$_POST;
         if(empty($post_param)) return NULL;
         $this->Client_filter($post_param);
         $data = $this->client_input_data;

         if($name!=NULL && isset($this->client_input_data[$name]) && $name = $this->client_input_data[$name])
         {
            return !empty($name)?$name:(($default!=NULL)?$default:NULL);
         }else if($name==NULL && !empty($data))
         {
            return $data;
         }
         return !isset($this->client_input_data[$name])?(($default!=NULL)?$default:NULL):$this->client_input_data[$name];
          
     }   
}
/*
   该类的提供相应的HTTP数据请求类库，这里是做了常用的方法封装
   说明：
       private $input = NULL;=>客户端接受过来的数据,数据到了这里并没有做过滤处理
       public $client_input_data = NULL;=>同样是客户端的HTTP数据，但这里的数据做了相应的简单的HTTP数据处理
       public $Query_url =  NULL;=>URL 访问地址,路由功能使用

*/

?>
