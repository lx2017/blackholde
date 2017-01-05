<?php


/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

    /* 多级控制器*/
    'CONTROLLER_LEVEL' => 2,
    /*设置url不区分大小写*/
    // 'URL_CASE_INSENSITIVE'  =>  true,
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'ymt_', // 缓存前缀
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

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes' => '', //允许上传的文件MiMe类型
        'maxSize' => 2 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
        'exts' => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub' => true, //自动子目录保存文件
        'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'urlPath' => '/Uploads/Picture/',//url相对路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt' => '', //文件保存后缀，空则使用原后缀
        'replace' => false, //存在同名是否覆盖
        'hash' => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    'PICTURE_UPLOAD_DRIVER' => 'local',
    //本地上传文件驱动配置
    'UPLOAD_LOCAL_CONFIG' => array(),
    'UPLOAD_BCS_CONFIG' => array(
        'AccessKey' => '',
        'SecretKey' => '',
        'bucket' => '',
        'rename' => false
    ),
    'UPLOAD_QINIU_CONFIG' => array(
        'accessKey' => '__ODsglZwwjRJNZHAu7vtcEf-zgIxdQAY-QqVrZD',
        'secrectKey' => 'Z9-RahGtXhKeTUYy9WCnLbQ98ZuZ_paiaoBjByKv',
        'bucket' => 'ymt',
        'domain' => 'www.iymatou.com',
        'timeout' => 3600,
    ),


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
        '__FORM__'    =>  __ROOT__.'/Public/Admin/form',// 表单模块公共目录
    ),

    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'ymt_admin', //session前缀
    'COOKIE_PREFIX' => 'ymt_admin_', // Cookie前缀 避免冲突
    'VAR_SESSION_ID' => 'session_id',    //修复uploadify插件无法传递session_id的bug

    /* 后台错误页面模板 */
    'TMPL_ACTION_ERROR' => MODULE_PATH . 'View/Public/error.html', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS' => MODULE_PATH . 'View/Public/success.html', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE' => MODULE_PATH . 'View/Public/exception.html',// 异常页面的模板文件

    
    'show_page_trace'=>false,//去掉右下角的thinkphp的图标
		//县总，地总，省总，大区经理，总部相关配置
		'SALE_MANAGER'=>array(
				//数据新增
				'ADD_VALIDATE_FAIL_STATUS'=>2,//新增校验失败状态码
				'ADD_INSERT_SUCCESS_STATUS'=>1 ,//新增成功状态码
				'ADD_INSERT_FAIL_STATUS'=>1,//新增失败状态码
				'ADD_INSERT_SUCCESS_MSG'=>'数据插入成功',//新增成功提示信息
				'ADD_INSERT_FAIL_MSG'=>'数据插入失败',//新增失败提示信息
				'ADD_INSERT_REPEAT_MSG'=>'数据重复',// 新增数据重复提示信息
				'ADD_INSERT_REPEATL_STATUS'=>3,// 新增数据重复提示信息
				//数据更新
				'UPDATE_SUCCESS_STATUS'=>1,//数据修改成功状态码
				'UPDATET_SUCCESS_MSG'=>'修改数据成功',//修改数据成功提示信息
				'UPDATE_FAIL_STATUS'=>2,//修改数据失败状态码
				'UPDATET_FAIL_MSG'=>'修改数据失败',//修改数据失败提示信息
				//删除数据
				'DELETE_SUCCESS_STATUS'=>1, //删除成功状态码
				'DELETE_SUCCESS_MSG'=>' 删除数据成功',//删除成功提示信息
				'DELETE_FAIL_STATUS'=>2,//删除数据失败状态码
				'DELETEFAIL_MSG'=>'删除数据失败',//删除数据失败提示信息
				//角色常量定义
				'SALE_CLERK'=>5, //业务员
				'SALE_COUNTY'=>4, //县总
				'SALE_CITY'=>3,//地总
				'SALE_PROVINCE'=>2,//省总
				'SALE_MANAGER'=>1,//大区经理
				'SALE_HEAD'=>0,//总部
				//帐号初始密码
				'INIT_PASSWORD'=>'123456',
				//编辑数据时，前端传id进行校验
				'ID_VALIDATE_FAIL_STATUS'=>1,
				'ID_VALIDATE_FAIL_MSG'=>'用户id有误',
		
		)
);
