<?php

class View
{
	  //模板分配的变量
	  public  $_tVal = array();
	  //模板名称
	  public $theme = NULL;
    //模板文件夹的名称
    public $view = "View";
    //模板文件后缀名称
    public $view_suffix=".html";
    //是否支持原始PHP
    public $type = FALSE;
    //监听事件
    public $hook_object = NULL;
    //模板输出的类型
    public $contenttype = 'text/html';

	  public function __construct()
	  {
        if($this->hook_object == NULL)
        {
           
           try
           {
             if(!is_file(__LIBRARY__."/Hook.php")) throw new Exception("核心文件加载出错");
             include_once(__LIBRARY__."/Hook.php");
           }catch(Exception $e)
           {
             echo $e->getMessage();
           }
        }
	  }
	  /*
       模板分配变量
       param $name:变量名称
             $value:变量值
	  */
	  public function assign($name,$value)
	  {
	  	   try
	  	   {
                if($name==NULL || $name==NULL)
                {
                	  throw new Exception("分配变量出错");
                }
                if(isset($this->_tVal[$name]) && !empty($this->_tVal[$name]))
                {
                	if(is_array($value)) array_merge($this->_tVal[$name],$value);
                	$this->_tVal[$name] = $value;
                }
                $this->_tVal[$name] = $value;

	  	   }catch(Exception $e)
	  	   {
                echo $e->getMessage();
                exit();
	  	   }
	  	   
	  }
	  /*
           输出模板
           @param:$templateFile=>模板的名称
           @param:$Module=>对应的模块名称
           @param:$cache=>是否需要缓存
     */
       public function display($templateFile,$Module=NULL,$action,$cache=FALSE)
       {
            $template_file = $this->Location_Template($templateFile,$Module,$action);
            try
            {
               if(!is_file($template_file))
               {
                    throw new Exception("模板文件不存在");
                    die();
               }
              $message_file = pathInfo($template_file);
              $file_dir=__LIBRARY__."/Template";
              ob_start();
              ob_implicit_flush(0);

              /*
                如果模板后缀PHP结尾的话表示支持PHP原始
              */
              if(strtolower($message_file['extension'])=='html' || $this->type==TRUE)
              {
                    if(is_dir($file_dir))
                    {
                        $handler =  opendir($file_dir);
                        while(($filename = readdir($handler))!==FALSE)
                        {
                            if($filename!="." && $filename!="..")
                            {
                                include_once($file_dir."/".$filename);
                            }
                        }
                    }
                    
                    extract($this->_tVal, EXTR_OVERWRITE);
               
                    include_once($template_file);
                    $content = ob_get_clean();
                    
              }

              
            }catch(Exception $e)
            {
               echo $e->getMessage();
            }
            /*
                 开始输出模板内容
            */
           
            header('Cache-control: max-age=300');  // 页面缓存控制
            header('X-Powered-By:blackholde');
            echo $content;
       }
       /*
          定位模板的位置
          @param:$Template=>模板名称
          @param:$Module=>加载的MODULE名称
          @param:$action=>加载的ACTION 名称
          使用说明
              定位文件  支持跨 ACTION调用模板文件例如
              $this->display('Memeber@index');
              Memeber表示需要调用调用的ACTIOIN，index表示需要加载的Memeber下的index模板文件
              $this->display('Home/Member@index');

       */
       private function Location_Template($Template,$module,$action)
       {
         //例如 $this->display();
         $module = ($module==NULL)?$_SERVER['Project_Module']:$module;
         if(''==$Template)
         {
            if(!isset($_SERVER['function']) || empty($_SERVER['function']))
            {
                 $_SERVER['function'] = "index";
            }
            $Template = $_SERVER['function'];
            
         }else{
             if(strpos($Template,'@'))
             {
                 list($action,$Template) = explode('@',$Template);

                 //如果说ACTION 还包含层级关系
                 if(strpos($action,'/'))
                 {
                      list($module,$action) = explode('/',$action);
                 }
             }
            }
        $template_file = $module."/".$this->view."/".$action."/".$Template.$this->view_suffix;
        return $template_file;
         
       }
       
       
}
?>