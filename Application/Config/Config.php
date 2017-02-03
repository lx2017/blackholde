<?php
/*
   全局配置文件
*/
 return  array(
 	//需要初始化的文件
      "start_init"=>"Init.class.php",
      'HTTP_filter' =>"htmlspecialchars,addslashes,strip_tags",
      /*
            控制器默认结尾
      */
     // 'Action_pop'=>'.class.php',
      /**
         URL 请求连接采用何种模式，支持两种(/|?)
         如果单填写一种xxx/项目名称/模块名称/方法名称?name=xxx&age=xxx
         (/:?)
      */
      'Default_Url'=>array(
             'Project'=>'Home',
             'Module'=>'index'
             ),
      //'Url_Filter'=>':get_path_info'
      'Url_Intercept'=>array(
           'Home/a'=>'Member/Index/obj',
           'Member'=>'Home/Member/obj',
           'Home/Index/index'=>'Home/Member/index',
           'Member/test'=>'Home/Member/test',
           'Member/Index'=>'Member/Index/test'
      	),
       'URL_MODULE_MAP' => array(
              'hm'=>'Home'
       	),
      	//'Url_Intercept'=>'hello'
       'slave'=>array('a'=>'b'),
       'master' =>array(
               
               array(
                'address'=>'127.0.0.1',
               'username'=>'root',
               'password'=>'root',
               'port'=>3306,
               'database'=>'shara'
               )
        )
 	);
 function get_path_info()
 {

 	 return "hello,world";
 }
?>