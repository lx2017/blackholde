<?php

class Follow
{
       /*
            类对象存储池
       */
       private $Map_Object = array();
       private static $Instance_Object = NULL;
       public static $Client_Object = NULL;
       public function __construct()
       {

       	 if(self::$Instance_Object===NULL)  self::$Instance_Object = Start::getInstance();
       	 self::$Instance_Object->init();
       }
	   protected function App()
	   {
             
	   }
	   static  public function Start()
	   {
	   	   spl_autoload_register('Follow::autoload');

           self::$Client_Object = self::$Instance_Object->get_fun('Data');
           /*
               如果HTTP 域名扩展不存在的话就使用默认
           */
          $layer = C('Config')?json_decode(C('Config'),true):NULL;
          $Query_url = "";
         
          if((!isset($_SERVER['PATH_INFO'])) && !isset($layer['Url_Filter']))
           {
           
	           if(isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
	           {
	           	   
	           	   $route_arr =  array_slice(self::$Client_Object->_Query(), 0,3);
	           	   $url = "";
	           	   $route_url = array_reduce($route_arr,function($res,$data){
	           	   	   $res.= $data."/";
	           	   	   return $res;
	           	   },$url);
	           	  $Query_url = Rtrim($route_url,"/");

	           }else{
	           	 
	           	   if(isset($layer['Default_Url']) && !empty($layer['Default_Url']))
	           	   {
                     extract($layer['Default_Url']);
                     $Query_url = "";
                     $Query_url.=(isset($Project) && !empty($Porject))?$Project:'Home';
                     $Query_url.=(isset($Module) && !empty($Module))?"/".$Module:'/Index';
                     $Query_url.=(isset($Func) && !empty($Func))?"/".$Func:"/index";
                     
	           	   }

	           }
	           
           }else if(!isset($_SERVER['PATH_INFO']) && !empty($layer['Url_Filter'])){
               $Filter_func = explode(",",$layer['Url_Filter']);
               foreach($Filter_func as $key_func=>$val_func)
               {
                 if(0===strpos($val_func,":"))
                 {

                 	   $Query_url = call_user_func(substr($val_func,1));
                 	   break;
                 }else if(!$_SERVER[$val_func]){
                 	 $Query_url=(0 === strpos($_SERVER[$val_func],$_SERVER['SCRIPT_NAME']))?
                        substr($_SERVER[$val_func], strlen($_SERVER['SCRIPT_NAME']))   :  $_SERVER[$val_func];;
                        break;
                 }
               }
              
           }else{
           	      $Query_url = self::$Client_Object->Query_url;

           }

           $Query_url=empty($url=self::Url_Intercept($Query_url,$layer))?Ltrim($Query_url,"/"):ltrim($url,"/");
          
           
           self::getModule($Query_url,isset($layer['URL_MODULE_MAP'])?$layer['URL_MODULE_MAP']:NULL,isset($layer['Default_Url'])?$layer['Default_Url']:NULL);

          try
          {
          	 if(isset($_SERVER['Project_Module']) && !empty($_SERVER['Project_Module']))
          	 {
          	 	  /*
                   初步加载公共文件的里面的PHP 文件
          	 	  */
          	      $common_dir = BLACK_PATH.$_SERVER['Project_Module']."/Common";
          	      if(is_dir($common_dir) && $scandir = scandir($common_dir))
          	      {
          	      	  foreach($scandir as $file)
          	      	  {

          	      	  	     if($file!="." && $file!="..")
          	      	  	     {

          	      	  	     	 require_once $common_dir."/".$file;
          	      	  	     }
          	      	  	     unset($file);
          	      	  }
          	      unset($common_dir,$scanir);
          	      }
          	      /*
                    加载模块下相应的配置文件
          	      */
                 $config_dir = BLACK_PATH.$_SERVER['Project_Module']."/Config";
                 if(is_dir($config_dir) && $scandir=scandir($config_dir))
                 {
                 	foreach($scandir as $file)
                 	{
                 		  if($file!="." && $file!="..")
                 		  {
                 		  	  require_once $config_dir."/".$file;
                 		  }
                 		  unset($file);
                 	}
                 	unset($config_dir,$scandir);
                 }
                
          	   }


          }catch(Exception $e)
          {
               echo $e->getMessage();

          }
         
          $Controller = self::getController($_SERVER['Project_Module'],$Query_url);
          if(FALSE!==$Controller)
          {

             $fun_obj = ($Controller==$Query_url)?"index":array_slice(explode("/",$Query_url),-1)[0];
             if($fun_obj==$Controller) $fun_obj = "index";
             self::invokeAction($Controller,$fun_obj);
          }

	   }
	   public static function invokeAction($module,$action)
	   {
	   	    try
	   	    {
                if(!preg_match('/^[A-Za-z](\w)*$/',$action)){
                    throw new Exception("控制器异常");
                }
                $method =   new ReflectionMethod($module, $action);
                if($method->isPublic() && !$method->isStatic())
                {
                	  $class = new ReflectionClass($module);
                	  $instance = $class->newInstanceArgs();
                	  if($class->hasMethod('_before_'.$action)) {
						$before =   $class->getMethod('_before_'.$action);
						$before->invoke($instance);
					}
				
                
				$method->invoke($instance);
					 
				if($class->hasMethod('_after_'.$action))
				{
					$after =   $class->getMethod('_after_'.$action);
					if($after->isPublic()) {
						$after->invoke($instance);
					}
				}

                }
               
	   	    }catch(Exception $e)
	   	    {
	   	    	  echo $e->getMessage();
	   	    }
	   }
       static public function getController($Project,$Query_url)
       {
       	    /*
             如果
       	    */

            $Action_url = BLACK_PATH.$Project."/Action";
           
            $Action_Module=(!strpos($Query_url,"/")|| strcasecmp($Project,$Query_url)==0)?"Index":current(array_slice(explode("/",$Query_url),1));

            if(defined('Action_pop') && Action_pop!=NULL)
            {
              $Text = Action_pop;
            }else if(($layer = C('Config')?json_decode(C('Config'),true):NULL) && isset($layer['Action_pop'])){
              $Text = $layer['Action_pop'];
            }else{
              $Text = ".php";
            }
            $Module_url = $Action_url."/".$Action_Module.$Text;
            unset($Text);
            if(is_file($Module_url) && is_readable($Module_url) && file_exists($Module_url) && require_once($Module_url))
            {
            	return $Action_Module;
            }
                return FALSE;
         }
	   static public  function autoload($class)
	   {
	   	  echo $class;
	   }
       
	   static public function getModule($var,$layer=NULL,$Default_Url=NULL)
	   { 
	   	   if(empty($layer)) return FALSE;

	   	   $Project = strpos($var,"/")?explode("/",$var)[0]:$var;
	   	   
           try
           {
               if(is_dir(BLACK_PATH.$Project) && (!isset($_SERVER['Project_Module']) || empty($_SERVER['Project_Module'])))
               {
               	     $_SERVER['Project_Module'] = $Project;
               }else{
               	    echo "Project Module error....";
               	    exit;
               	  //抛出相应的异常
               }
          }catch(Exception $e)
           {


           }

	   	   
    
	   }
	   /*
         URL 转向
	   */
	   static private function Url_Intercept($url,$layer=NULL)
	   {

            if($layer===NULL)  return $url;

            if(!empty($layer['Url_Intercept']) && isset($layer['Url_Intercept']))
            {
              $Url_Filter = $layer['Url_Intercept'];
              try
              {
                $Client_Url = ltrim($url,"/");

                if(!empty($Url_Filter) && is_array($Url_Filter))
                {
                 
                  $Client_Get_Data=self::$Client_Object->_Query();
                  if(!empty($Client_Get_Data) && !isset($_SERVER['PATH_INFO']))
                  {
                 
                  $Client_string = $Client_Url;
                  }else{
                 
                  $Client_string = $url;
                  }
                  
                  $Client_string = implode(array_slice(explode("/",trim($Client_string,"/")),0,3),"/");
                 
                  if(isset($Url_Filter[trim($Client_string,"/")]) && !empty($Url_Filter[trim($Client_string,"/")]))
                  {

                  	  return $Url_Filter[trim($Client_string,"/")];
                  }
                  
                  
                }
              }catch(Exception $e)
              {
                 echo $e->getMessage();
              }
            }
	   }
}

?>