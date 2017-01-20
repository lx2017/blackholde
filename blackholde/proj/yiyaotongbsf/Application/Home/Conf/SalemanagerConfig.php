<?php
return array(
		//县总，地总，省总，大区经理，总部相关配置
		'SALE_MANAGER'=>array(
				//数据新增
				'ADD_VALIDATE_FAIL_STATUS'=>2,//新增校验失败状态码
				'ADD_INSERT_SUCCESS_STATUS'=>1 ,//新增成功状态码
				'ADD_INSERT_FAIL_STATUS'=>2,//新增失败状态码
				'ADD_INSERT_SUCCESS_MSG'=>'操作成功',//新增成功提示信息
				'ADD_INSERT_FAIL_MSG'=>'操作失败',//新增失败提示信息
				'ADD_INSERT_REPEAT_MSG'=>'数据重复',// 新增数据重复提示信息
				'ADD_INSERT_REPEATL_STATUS'=>3,//新增失败状态码
				//数据更新
				'UPDATE_SUCCESS_STATUS'=>1,//数据修改成功状态码
				'UPDATET_SUCCESS_MSG'=>'操作成功',//修改数据成功提示信息
				'UPDATE_FAIL_STATUS'=>2,//修改数据失败状态码
				'UPDATET_FAIL_MSG'=>'操作失败',//修改数据失败提示信息
				//删除数据
				'DELETE_SUCCESS_STATUS'=>1, //删除成功状态码
				'DELETE_SUCCESS_MSG'=>' 操作成功',//删除成功提示信息
				'DELETE_FAIL_STATUS'=>2,//删除数据失败状态码
				'DELETEFAIL_MSG'=>'操作失败',//删除数据失败提示信息
				//角色常量定义
				'CAREER_DEPARTMENT'=>7,
				'SALE_DEPARTMENT'=>6,
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
		
		),
	
		//销售管理者的状态
		'SALEMANAGER_STATUS'=>array(
			'NORMAL'=>0,
			'FORBIDDEN'=>1,
			'DELETED'=>2
		),
		'SALEMANAGER_CODE' => array(
				200 => 'success',
				2000 => '异常错误',
				2001 => '用户id错误',
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
				2028 => 'session失效',
		),
		//诊所类型
		'SALEMANAGER_CLINIC_TYPE' => array(
				'NORMAL'=>'0',
				'AIM'=>'1',
		),
		//诊所是否删除
		'SALEMANAGER_CLINIC_IDDELETE' => array(
				'NORMAL'=>'1',
				'DELETE'=>'0',
		),
		//申请类型
		'SALEMANAGER_APPLAY'=>array(
				'NORMAL_METTING'=>'2',
				'CIRCLE_METTING'=>'1',
				'SPEARK_METTING'=>'3',
				'LEARN_METTING'=>'4',
				'HERO_METTING'=>'5',
		),
		//申请状态
		'SALEMANAGER_APPLAY_STATUS'=>array(
			'APPROVING'=>'0',	
			'AGREE'=>'1',
			'REFUSE'=>'2',
		),
		//诊所默认密码
		'SALEMANAGER_CLINIC_PASSWORD'=>'88888888',
		'SALEMANAGER_APPLY_NAME' => array(
				1 => '圆桌会',
				2 => '普通申请',
				3 => '会议申请',
				4 => '卫星会',
				5 => '讲坛会',
				6 => '群英会',
				7 => '培训会',
				8=> '离职申请'
				
		),
		'SALEMANAGER_ORDER_STATUS'=>array(
				0=>'待处理',
				1=>'已完成',
				2=>'退货',
		),
		'SALEMANAGER_PAGE_NUM'=>10,
);