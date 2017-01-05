<?php

define('UC_AUTH_KEY','a&lzA->:yZt9RT#0j!C.4`%WbuhFg85?{(V[i]BQ');
/**
* 系统配文件
* 所有系统级别的配置
*/
return array(
/* 模块相关配置 */
'AUTOLOAD_NAMESPACE' => array('Addons' => YMTFRAMEWORK_ADDON_PATH), //扩展模块列表
'DEFAULT_MODULE'     => 'Home',
'MODULE_DENY_LIST'   => array('Common'),


/* 系统数据加密设置 */
'DATA_AUTH_KEY' => 'a&lzA->:yZt9RT#0j!C.4`%WbuhFg85?{(V[i]BQ', //默认数据加密KEY

/* 调试配置 */
'SHOW_PAGE_TRACE' => false,

/* 用户相关设置 */
'USER_MAX_CACHE'     => 1000, //最大缓存用户数
'USER_ADMINISTRATOR' => 1, //管理员用户ID
'USER_ADMINGROUP'=>1,

/* URL配置 */
'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
'URL_MODEL'            => 2, //URL模式
//'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
//'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

/* 全局过滤配置 */
'DEFAULT_FILTER' => 'urldecode,htmlspecialchars', //全局过滤函数

/* 数据库配置 */
'DB_TYPE'   => 'mysql', // 数据库类型
'DB_HOST'   => '115.28.159.211', // 服务器地址
'DB_NAME'   => 'yiyaotong', // 数据库名
'DB_USER'   => 'yiyaotong',
'DB_PWD'    => 'yiyao@#$123',  // 密码
//    'DB_HOST'   => 'localhost', // 服务器地址
//    'DB_NAME'   => 'yiyaotong', // 数据库名
//    'DB_USER'   => 'root', // 用户名
//    'DB_PWD'    => '123',  // 密码

'DB_PORT'   => '3306', // 端口
'DB_PREFIX' => 'ymt_', // 数据库表前缀

/* 文档模型配置 (文档模型核心配置，请勿更改) */
'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),

'LOAD_EXT_CONFIG' => array('ciiConfig','ymtConfig'),
//分页配置
'PAGE_CONFIG' => array(
    'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
    'prev'   => '上一页',
    'next'   => '下一页',
    'first'  => '首页',
    'last'   => '尾页',
    'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
),
//上传配置
'UPLOADIFY_FILE_CONF' => array(
    'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
    'exts' => 'zip,rar,7z,doc,docx,pdf', //允许上传的文件后缀
    'u_exts' => '', //允许上传的文件后缀---uploadify用------类型多了会卡(固不要,后台验证)
    'rootPath' => './Uploads/Temp/', //暂存目录
    'truePath' => './Uploads/Download/', //保存根路径
    'savePath' => '', //保存路径
    'subName' =>  '',
),
'UPLOADIFY_IMG_CONF' => array(
    'maxSize' => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
    'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
    'u_exts' => '*.jpg;*.gif;*.png;*.jpeg', //允许上传的文件后缀---uploadify用------类型多了会卡
    'rootPath' => './Uploads/Temp/', //暂存目录
    'truePath' => './Uploads/Picture/', //保存根路径
    'savePath' => '', //保存路径
    'subName' => '',
),
'UPLOADIFY_EXCEL_CONF' => array(
    'maxSize' => 5 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
    'exts' => 'xlsx', //允许上传的文件后缀
    'u_exts' => '*.xlsx', //允许上传的文件后缀---uploadify用------类型多了会卡
    'rootPath' => './Uploads/Temp/excel/', //暂存目录
    'savePath' => '', //保存路径
    'subName' => '',
),
    //环信配置
'HX_CONFIG' => array(
    'org_name'=>'1195161205178663',
    'app_name'=>'medicinepatient',
    'client_id'=>'YXA6affkALt1Eea_FS2LTKjtCw',
    'client_secret'=>'YXA67ZRFLqNiDcXcYQv_GRBTzCzevFQ',
    'file_dir'=>'Uploads/huanxin/down/',//资源存放目录
    'cursor_dir'=>'Uploads/huanxin/txtfile/',
),
//极光推送
'JPUSH_CONFIG'=>array(
    'key'=>'67bab77c05864e4d08d1aeea',
    'secret'=>'1429575f1dc897399ad7f080',
),
'DEFAULT_PASSWORD'=>123456,
'MY_CACHE_TIME'=>600,//10分钟
);
