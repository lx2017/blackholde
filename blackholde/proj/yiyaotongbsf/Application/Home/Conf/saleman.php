<?php

return array(
//     'TMPL_PARSE_STRING' => array(
//         '__SALEMAN__' => '/Public/Home/Saleman', // 更改默认的/Public 替换规则
//     ),
    'DEF_ACCOUNT_PWD' => '88888888', //诊所账号默认密码
    'ACCOUNT_KEY' => 'clinicAccountPwd', //诊所账号密码默认秘钥
    'DEF_LIMIT' => 3, //每页默认显示数量
    'DEF_TRIP_CATE' => array('today', 'future', 'complete', 'cancel'), //行程默认分类；today：今日未完成，future：以后未完成，complete：已完成，cancel：已取消
    'DEF_SIGN_CATE' => array('can_sign', 'nearby', 'already_sign'), //签到诊所列表分页；can_sign：可签到，nearby：附近的，already_sign：已签到
    'DEF_APPLY_CATE' => array(
        1 => '圆桌会',
        2 => '普通申请',
        3 => '会议申请',
        4 => '卫星会',
        5 => '讲坛会',
        6 => '群英会',
        7 => '培训会'
    ),
    'SING_DISTANCE' => 0.1, //可以签到距离（单位KM）
    'NEAR_DISTANCE' => 1, //附近诊所距离
    'CODE_LIST' => array(
        200 => 'success',
        2000 => '异常错误',
        2001 => '诊所账户手机号无效',
        2002 => '诊所名称无效',
        2003 => '诊所管理员无效',
        2004 => '诊所账号已存在',
        2005 => '诊所账号不存在',
        2006 => '诊所账号操作失败',
        2007 => '诊所负责人操作失败',
        2008 => '诊所信息操作失败',
        2009 => '诊所ID无效',
        2010 => '诊所不存在',
        2011 => '业务员ID无效',
        2012 => '诊所不存在',
        2013 => '诊所负责人不存在',
        2014 => '业务员已存在',
        2015 => '业务员不存在',
        2016 => '行程操作失败',
        2017 => '行程ID无效',
        2018 => '状态无效',
        2019 => '行程分类无效',
        2020 => '经纬度无效',
        2021 => '业务员角色无效',
        2022 => '县总暂无业务员',
        2023 => '诊所不属于当前业务员或县总',
        2024 => '当前诊所已签到',
        2025 => '签到操作失败',
        2026 => '申请支持类型无效',
        2027 => '申请支持操作失败',
        2028 => '操作数据无效',
        2029 => '业务员操作无效',
    ),
);




