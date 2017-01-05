<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
/* 微博配置信息 */
define("WB_AKEY", '3541069178');
define("WB_SKEY", '7ebbd7e72e955e59cb9494d9b815e209');
//define( "WB_CALLBACK_URL" , 'http://www.ci.com/weibo_login/weibo_login/index' );
define("WB_CALLBACK_URL", 'http://www.register.com/index.php/Home/LoginAndRegister/Login/WbLogin_Act');
/* weiboend */

/* 微信配置信息 */
define("WX_appID", 'wxb11f51fa78a7582e');
define("WX_appsecret", '8b749497aecf5a37a70d9cc900931f7e');
//$re_url ="http://www.3laohu.com/code.php";
//$re_url ="http://www.ci.com/weixin_login/weixin_login/get_code";
$re_url = "http://www.register.com/index.php/Home/LoginAndRegister/Login/loginByWX";
/* weixinend */


$re_url = urlencode($re_url);
define("WX_URL", $re_url);
return array(
    /* URL模式 */
    'URL_MODEL' => 1,
    /* 多级控制器 */
    'CONTROLLER_LEVEL' => 2,
    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',
    /* 主题设置 */
    'DEFAULT_THEME' => 'default', // 默认模板主题名称
    // 'DEFAULT_THEME' =>  '',  // 默认模板主题名称

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'onethink_', // 缓存前缀
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 编辑器图片上传相关配置 */
    'EDITOR_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Editor/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__SaleManager__' => '/Public/Home/SaleManager', // 销售管理（包括县总，省总等）默认的/Public 替换规则
        '__SALEMAN__' => '/Public/Home/Saleman', // 业务员默认替换/Public规则
    ),
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'ymt_home', //session前缀
    'COOKIE_PREFIX' => 'ymt_home_', // Cookie前缀 避免冲突

    /**
     * 附件相关配置
     * 附件是规划在插件中的，所以附件的配置暂时写到这里
     * 后期会移动到数据库进行管理
     */
    'ATTACHMENT_DEFAULT' => array(
        'is_upload' => true,
        'allow_type' => '0,1,2', //允许的附件类型 (0-目录，1-外链，2-文件)
        'driver' => 'Local', //上传驱动
        'driver_config' => null, //驱动配置
    ), //附件默认配置
    'ATTACHMENT_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Attachment/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //附件上传配置（文件上传类配置
    'LOAD_EXT_CONFIG' => array('SalemanagerConfig','CLINIC'=>'clinic','saleman'),
);
