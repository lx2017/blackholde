<?php

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
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

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/img',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__AUI__' => __ROOT__ . '/Public/' . MODULE_NAME . '/aui',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'ymt_patient', //session前缀
    'COOKIE_PREFIX' => 'ymt_patient_', // Cookie前缀 避免冲突
    'DEFAULT_DISTANCE'=>10,//默认附近的距离, km
    'DEFAULT_LNG'=>116.403875,//'默认经度
    'DEFAULT_LAT'=>39.915168,//'默认纬度
);
