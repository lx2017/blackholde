<?php
/**
    function:黑洞框架核心路由类库
    author :lx
*/
class Route
{

    public function __construct()
    {

    	 spl_autoload_register('Route::autoload');
    }
    public static function autoload($class)
    {


    }
}
?>
<!--
   思维设想：
       这里涉及到框架的路由动作，要做这几个动作
       第一：记录HTTP请求的详情，包括地址，时间，参数，一次进程启动和消除内存开销，采用文件的方式记录
       第二：项目的加载，根据访问的地址不同加载不同项目的代码，和初始化不同的配置文件
       对于不同项目的对象实例化动作就直接采用PHP 反射机制处理
       第三：做好异常处理，
       第四：做好扩展的加载动作，让类库可以轻松的加载不同的类库有不同的动作
   过程设想：
       对于HTTP 请求详情的记载可以独立写类库
       这里只单独做好对项目代码的实例化动作，从代码开始就加载HTTP请求类库，和异常处理类库


-->