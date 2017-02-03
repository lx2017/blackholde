<?php
/**
   PHP 框架 1.0版本
   name=>黑洞
   author=>鲁旭
   这个页面是项目的入口文件，主要功能做检查PHP运行环境，和相关环境变量的定义，以及文件路径的定义
   首先开始检查PHP的运行环境，PHP版本高于5.3
*/

 header("Content-Type: text/html; charset=UTF-8");
 if(version_compare(PHP_VERSION,'5.3.0','<')) {
 /*
     如果PHP 的版本低于5.3就结束PHP进程
 */
 	   die('require PHP > 5.3.0 !');
}
//项目的跟目录
define("__ROOT__",$_SERVER['CONTEXT_DOCUMENT_ROOT']);
//定义核心文件库,黑洞内核代码放在Application文件夹目录下
define("__CORE__",__ROOT__."/Application");
define("__LIBRARY__",__ROOT__."/Application/Library");
//定义项目公共文件目录
define("__PUBLIC__","./Public");

defined('BLACK_PATH')     or define('BLACK_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
//初始化文件
defined("INIT_PATH") or define("INIT_PATH","./Application/init/");

defined("LIBRARY_PATH") or define("LIBRARY_PATH",BLACK_PATH."Application/Library");

defined("HOLDE_ROUTE") or define("HOLDE_ROUTE",BLACK_PATH."Application/holde/");

//开始加载黑洞框架的核心启动程序
$start_file = __CORE__."/init/start.class.php";
/*
   判断启动初始化文件是否存在，如果存在就加载引入，如果不存在就结束PHP 进程并且提示错误 
*/
//黑洞框架路由文件
$follow_file = HOLDE_ROUTE."Follow.php";
//黑洞框架基类文件
$base_file = HOLDE_ROUTE."Base.php";

file_exists($start_file)?require_once($start_file):exit("核心启动文件出错");
file_exists($follow_file)?require_once($follow_file):exit("路由文件加载出错");
file_exists($base_file)?require_once($base_file):exit("基类文件加载出错");
try{


$follow_object = new Follow();
$follow_object::Start();
}catch(Exception $e)
{
	//系统错误
}

?>