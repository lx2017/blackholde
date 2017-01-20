SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `ymt_ucenter_member`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_ucenter_member`;
CREATE TABLE `ymt_ucenter_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` char(16) NOT NULL COMMENT '登录名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '邮箱',
  `mobile` char(15) NOT NULL COMMENT '手机',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='员工表';
-- ----------------------------
--  Table structure for `ymt_member`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_member`;
CREATE TABLE `ymt_member` (
  `uid` int(10) unsigned NOT NULL COMMENT '员工ID',
  `nickname` char(16) NOT NULL DEFAULT '' COMMENT '昵称',
  `sex` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` date NOT NULL DEFAULT '0000-00-00' COMMENT '生日',
  `qq` char(10) NOT NULL DEFAULT '' COMMENT 'qq号',
  `score` mediumint(8) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '会员状态',
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='员工纪录表';


-- ----------------------------
--  Table structure for `ymt_module`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_module`;
CREATE TABLE `ymt_module` (
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` varchar(50) NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `key` varchar(50) NOT NULL COMMENT '标识',
  `type` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1:菜单；2操作',
  PRIMARY KEY (`key`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_module`
-- ----------------------------
INSERT INTO `ymt_module` VALUES ('员工管理', '0', '0', '', '0', '', '', '0', 'EMPLOYEEMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('员工信息', 'EMPLOYEEMANAGER', '0', 'Employee/User/index', '0', '', '', '0', 'EMPLOYEELIST', '1');
INSERT INTO `ymt_module` VALUES ('新增', 'EMPLOYEELIST', '0', 'Employee/User/add', '0', '添加新用户', '', '0', 'EMPLOYEEADD', '2');
INSERT INTO `ymt_module` VALUES ('员工行为', 'EMPLOYEEMANAGER', '2', 'Employee/User/action', '0', '', '行为管理', '0', 'EMPLOYEEACTION', '1');
INSERT INTO `ymt_module` VALUES ('新增', 'EMPLOYEEACTION', '0', 'Employee/User/addaction', '0', '', '', '0', 'EMPLOYEEACTIONADD', '2');
INSERT INTO `ymt_module` VALUES ('编辑', 'EMPLOYEEACTION', '0', 'User/editaction', '0', '', '', '0', 'EMPLOYEEACTIONEDIT', '2');
INSERT INTO `ymt_module` VALUES ('保存', 'EMPLOYEEACTION', '0', 'User/saveAction', '0', '\"用户->用户行为\"保存编辑和新增的用户行为', '', '0', 'EMPLOYEEACTIONSAVE', '2');
INSERT INTO `ymt_module` VALUES ('变更行为状态', 'EMPLOYEEACTION', '0', 'User/setStatus', '0', '\"用户->用户行为\"中的启用,禁用和删除权限', '', '0', 'EMPLOYEEACTIONSETSTATUS', '2');
INSERT INTO `ymt_module` VALUES ('禁用会员', 'EMPLOYEEACTION', '0', 'User/changeStatus?method=forbidUser', '0', '\"用户->用户信息\"中的禁用', '', '0', 'EMPLOYEEACTIONFORBID', '2');
INSERT INTO `ymt_module` VALUES ('启用会员', 'EMPLOYEEACTION', '0', 'User/changeStatus?method=resumeUser', '0', '\"用户->用户信息\"中的启用', '', '0', 'EMPLOYEEACTIONRESUME', '2');
INSERT INTO `ymt_module` VALUES ('删除会员', 'EMPLOYEEACTION', '0', 'User/changeStatus?method=deleteUser', '0', '\"用户->用户信息\"中的删除', '', '0', 'EMPLOYEEACTIONDELETE', '2');
INSERT INTO `ymt_module` VALUES ('权限管理', 'EMPLOYEEMANAGER', '1', 'Employee/AuthManager/index', '0', '', '员工管理', '0', 'AUTHMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('新增', 'AUTHMANAGER', '0', 'Employee/AuthManager/createGroup', '0', '创建新的用户组', '', '0', 'AUTHCREATEGROUP', '2');
INSERT INTO `ymt_module` VALUES ('修改', 'AUTHMANAGER', '0', 'Employee/AuthManager/editGroup', '0', '编辑用户组名称和描述', '', '0', 'AUTHEDITGROUP', '2');
INSERT INTO `ymt_module` VALUES ('删除', 'AUTHMANAGER', '0', 'AuthManager/deleteGroup', '0', '删除用户组', '', '0', 'AUTHGROUPDELETE', '2');
INSERT INTO `ymt_module` VALUES ('授权', 'AUTHMANAGER', '0', 'AuthManager/group', '0', '\"后台 \\ 用户 \\ 用户信息\"列表页的\"授权\"操作按钮,用于设置用户所属用户组', '', '0', 'AUTHMANAGERGROUP', '2');
INSERT INTO `ymt_module` VALUES ('访问授权', 'AUTHMANAGER', '0', 'Employee/AuthManager/access', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"访问授权\"操作按钮', '', '0', 'AUTHACCESS', '2');
INSERT INTO `ymt_module` VALUES ('成员授权', 'AUTHMANAGER', '0', 'Employee/AuthManager/user', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"成员授权\"操作按钮', '', '0', 'AUTHEMPLOYEE', '2');
INSERT INTO `ymt_module` VALUES ('解除授权', 'AUTHMANAGER', '0', 'Employee/AuthManager/removeFromGroup', '0', '\"成员授权\"列表页内的解除授权操作按钮', '', '0', 'AUTHREMOVEFROMGROUP', '2');
INSERT INTO `ymt_module` VALUES ('保存成员授权', 'AUTHMANAGER', '0', 'Employee/AuthManager/addToGroup', '0', '\"成员授权\"里右下角的\"添加\"按钮)', '', '0', 'AUTHADDTOGROUP', '2');
INSERT INTO `ymt_module` VALUES ('分类授权', 'AUTHMANAGER', '0', 'Employee/AuthManager/category', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"分类授权\"操作按钮', '', '0', 'AUTHCATEGORY', '2');
INSERT INTO `ymt_module` VALUES ('保存分类授权', 'AUTHMANAGER', '0', 'AuthManager/addToCategory', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0', 'AUTHMANAGERADDCATE', '2');
INSERT INTO `ymt_module` VALUES ('模型授权', 'AUTHMANAGER', '0', 'AuthManager/modelauth', '0', '\"后台 \\ 用户 \\ 权限管理\"列表页的\"模型授权\"操作按钮', '', '0', 'AUTHMANAGERADDMODEL', '2');
INSERT INTO `ymt_module` VALUES ('保存模型授权', 'AUTHMANAGER', '0', 'AuthManager/addToModel', '0', '\"分类授权\"页面的\"保存\"按钮', '', '0', 'AUTHMANAGERSAVEMODEL', '2');
INSERT INTO `ymt_module` VALUES ('启用', 'EMPLOYEELIST', '0', 'Employee/User/resumeEmp', '0', '', '', '0', 'EMPLOYEERESUME', '2');
INSERT INTO `ymt_module` VALUES ('禁用', 'EMPLOYEELIST', '0', 'Employee/User/forbidEmp', '0', '', '', '0', 'EMPLOYEEFORBID', '2');
INSERT INTO `ymt_module` VALUES ('删除', 'EMPLOYEELIST', '0', 'Employee/User/deleteEmp', '0', '', '', '0', 'EMPLOYEEDELETE', '2');
INSERT INTO `ymt_module` VALUES ('系统管理', '0', '5', '', '0', '', '', '0', 'SYSTEMMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('网站设置', 'SYSTEMMANAGER', '0', 'System/WebConfig/base', '0', '', '', '0', 'WEBCONFIG', '1');
INSERT INTO `ymt_module` VALUES ('系统设置', 'WEBCONFIG', '0', 'System/WebConfig/system', '0', '', '', '0', 'SYSTEMCONFIG', '2');
INSERT INTO `ymt_module` VALUES ('数据库管理', '0', '6', '', '0', '', '', '0', 'DBMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('数据库备份', 'DBMANAGER', '0', 'Db/Db/export', '0', '', '', '0', 'DBEXPORT', '1');
INSERT INTO `ymt_module` VALUES ('数据库还原', 'DBMANAGER', '0', 'Db/Db/import', '0', '', '', '0', 'DBIMPORT', '1');
INSERT INTO `ymt_module` VALUES ('立即备份', 'DBEXPORT', '0', 'Db/Db/doExport', '0', '', '', '0', 'DBBACKNOW', '2');
INSERT INTO `ymt_module` VALUES ('优化表', 'DBEXPORT', '0', 'Db/Db/optimize', '0', '', '', '0', 'TABLEOPTIMIZE', '2');
INSERT INTO `ymt_module` VALUES ('修复表', 'DBEXPORT', '0', 'Db/Db/repair', '0', '', '', '0', 'TABLEREPAIR', '2');
INSERT INTO `ymt_module` VALUES ('还原', 'DBIMPORT', '0', 'Db/Db/doImport', '0', '', '', '0', 'DBIMPORTOPERATE', '2');
INSERT INTO `ymt_module` VALUES ('删除', 'DBIMPORT', '0', 'Db/Db/del', '0', '', '', '0', 'DBBACKDELETE', '2');
INSERT INTO `ymt_module` VALUES ('扩展管理', '0', '0', '', '0', '', '', '0', 'EXTENDMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('钩子管理', 'EXTENDMANAGER', '1', 'Extend/Hooks/index', '0', '', '', '0', 'HOOKMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('新增', 'HOOKMANAGER', '0', 'Extend/Hooks/add', '0', '', '', '0', 'ADDHOOK', '2');
INSERT INTO `ymt_module` VALUES ('修改', 'HOOKMANAGER', '0', 'Extend/Hooks/edit', '0', '', '', '0', 'EDITHOOK', '2');
INSERT INTO `ymt_module` VALUES ('删除', 'HOOKMANAGER', '0', 'Extend/Hooks/del', '0', '', '', '0', 'DELHOOK', '2');
INSERT INTO `ymt_module` VALUES ('插件管理', 'EXTENDMANAGER', '0', 'Extend/Addons/index', '0', '', '', '0', 'ADDONSMANAGER', '1');
INSERT INTO `ymt_module` VALUES ('快速创建', 'ADDONSMANAGER', '0', 'Extend/Addons/create', '0', '', '', '0', 'ADDADDON', '2');
INSERT INTO `ymt_module` VALUES ('设置', 'ADDONSMANAGER', '0', 'Extend/Addons/config', '0', '', '', '0', 'ADDONCONFIG', '2');
INSERT INTO `ymt_module` VALUES ('禁用', 'ADDONSMANAGER', '0', 'Extend/Addons/disable', '0', '', '', '0', 'ADDONDISABLE', '2');
INSERT INTO `ymt_module` VALUES ('启用', 'ADDONSMANAGER', '0', 'Extend/Addons/enable', '0', '', '', '0', 'ADDONENABLE', '2');
INSERT INTO `ymt_module` VALUES ('卸载', 'ADDONSMANAGER', '0', 'Extend/Addons/uninstall', '0', '', '', '0', 'ADDONUNINSTALL', '2');
INSERT INTO `ymt_module` VALUES ('安装', 'ADDONSMANAGER', '0', 'Extend/Addons/install', '0', '', '', '0', 'ADDONINSTALL', '2');

-- ----------------------------
--  Table structure for `ymt_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_auth_group`;
CREATE TABLE `ymt_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户组id,自增主键',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) NOT NULL DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除',
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_auth_group`
-- ----------------------------
INSERT INTO `ymt_auth_group` VALUES ('1', 'admin', '1', '超级管理员组', '拥有最高的权限', '1', '1,2,7,8,9,10,11,12,13,14,15,16,17,18,20,21,22,23,24,25,28,29,30,31,32,33,34,35,36,37,38,39,40,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,89,90,91,92,93,94,95,100,102,103');
-- ----------------------------
--  Table structure for `ymt_auth_group_module`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_auth_group_module`;
CREATE TABLE `ymt_auth_group_module` (
  `group_id` int(11) NOT NULL COMMENT '角色id',
  `module_key` varchar(50) NOT NULL COMMENT '系统模块id',
  PRIMARY KEY (`group_id`,`module_key`),
  UNIQUE KEY `index_role_system` (`group_id`,`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_auth_group_module`
-- ----------------------------

INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHACCESS');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHADDTOGROUP');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHCREATEGROUP');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHEDITGROUP');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHEMPLOYEE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHGROUPDELETE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'AUTHREMOVEFROMGROUP');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEEADD');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEEDELETE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEEFORBID');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEELIST');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEEMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EMPLOYEERESUME');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'SYSTEMMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'WEBCONFIG');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'SYSTEMCONFIG');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBEXPORT');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBIMPORT');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBBACKNOW');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'TABLEOPTIMIZE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'TABLEREPAIR');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBIMPORTOPERATE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DBBACKDELETE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'EXTENDMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'HOOKMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDHOOK');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'DELHOOK');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONSMANAGER');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDADDON');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONCONFIG');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONDISABLE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONENABLE');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONUNINSTALL');
INSERT INTO `ymt_auth_group_module` VALUES ('1', 'ADDONINSTALL');


-- ----------------------------
--  Table structure for `ymt_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_auth_group_access`;
CREATE TABLE `ymt_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  PRIMARY KEY (`uid`,`group_id`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- ----------------------------
--  Records of `ymt_auth_group_access`
-- ----------------------------
INSERT INTO `ymt_auth_group_access` VALUES ('1', '1');

-- ----------------------------
--  Table structure for `ymt_config`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_config`;
CREATE TABLE `ymt_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_config`
-- ----------------------------
INSERT INTO `ymt_config` VALUES ('1', 'WEB_SITE_TITLE', '1', '网站标题', '1', '', '网站标题前台显示标题', '1378898976', '1465279839', '1', '后台管理系统', '0');
INSERT INTO `ymt_config` VALUES ('2', 'WEB_SITE_DESCRIPTION', '2', '网站描述', '1', '', '网站搜索引擎描述', '1378898976', '1379235841', '1', '猿码头后台管理系统', '1');
INSERT INTO `ymt_config` VALUES ('3', 'WEB_SITE_KEYWORD', '2', '网站关键字', '1', '', '网站搜索引擎关键字', '1378898976', '1381390100', '1', '猿码头', '8');
INSERT INTO `ymt_config` VALUES ('10', 'WEB_SITE_ICP', '1', '网站备案号', '1', '', '如“京ICP备14013455号-6”', '1378900335', '1379235859', '1', '', '9');
INSERT INTO `ymt_config` VALUES ('11', 'DOCUMENT_POSITION', '3', '文档推荐位', '2', '', '文档推荐位，推荐到多个位置KEY值相加即可', '1379053380', '1379235329', '1', '1:列表页推荐\r\n2:频道页推荐\r\n4:网站首页推荐', '3');
INSERT INTO `ymt_config` VALUES ('12', 'DOCUMENT_DISPLAY', '3', '文档可见性', '2', '', '文章可见性仅影响前台显示，后台不收影响', '1379056370', '1379235322', '1', '0:所有人可见\r\n1:仅注册会员可见\r\n2:仅管理员可见', '4');
INSERT INTO `ymt_config` VALUES ('23', 'OPEN_DRAFTBOX', '4', '是否开启草稿功能', '2', '0:关闭草稿功能\r\n1:开启草稿功能\r\n', '新增文章时的草稿功能配置', '1379484332', '1379484591', '1', '1', '1');
INSERT INTO `ymt_config` VALUES ('24', 'DRAFT_AOTOSAVE_INTERVAL', '0', '自动保存草稿时间', '2', '', '自动保存草稿的时间间隔，单位：秒', '1379484574', '1386143323', '1', '60', '2');
INSERT INTO `ymt_config` VALUES ('25', 'LIST_ROWS', '0', '后台每页记录数', '2', '', '后台数据每页显示记录数', '1379503896', '1380427745', '1', '10', '10');
INSERT INTO `ymt_config` VALUES ('26', 'USER_ALLOW_REGISTER', '4', '是否允许用户注册', '3', '0:关闭注册\r\n1:允许注册', '是否开放用户注册', '1379504487', '1379504580', '1', '1', '3');
INSERT INTO `ymt_config` VALUES ('28', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '4', '', '路径必须以 / 结尾', '1381482411', '1381482411', '1', './Data/', '5');
INSERT INTO `ymt_config` VALUES ('29', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '4', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1381482488', '1381729564', '1', '20971520', '7');
INSERT INTO `ymt_config` VALUES ('30', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '4', '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1381713345', '1381729544', '1', '1', '9');
INSERT INTO `ymt_config` VALUES ('31', 'DATA_BACKUP_COMPRESS_LEVEL', '4', '数据库备份文件压缩级别', '4', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1381713408', '1381713408', '1', '9', '10');
INSERT INTO `ymt_config` VALUES ('33', 'ALLOW_VISIT', '3', '不受限控制器方法', '0', '', '', '1386644047', '1386644741', '1', '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:user/submitPassword\r\n9:user/submitNickname\r\n10:file/uploadpicture', '0');
INSERT INTO `ymt_config` VALUES ('34', 'DENY_VISIT', '3', '超管专限控制器方法', '0', '', '仅超级管理员可访问的控制器方法', '1386644141', '1386644659', '1', '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', '0');
INSERT INTO `ymt_config` VALUES ('35', 'REPLY_LIST_ROWS', '0', '回复列表每页条数', '2', '', '', '1386645376', '1387178083', '1', '10', '0'), ('36', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '4', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165553', '1', '', '12');
-- ----------------------------
--  Table structure for `ymt__user_cookie`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_user_cookie`;
CREATE TABLE `ymt_user_cookie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL COMMENT '用户id 与tb_user 的id关联',
  `newid` varchar(50) DEFAULT NULL COMMENT '生成随机的id ，存入cookie中，与userid一对一关系',
  `expire_time` int(11) DEFAULT NULL COMMENT '失效时间',
  `user_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户类型0:用户；1员工',
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_newid` (`newid`),
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `ymt_addons`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_addons`;
CREATE TABLE `ymt_addons` (
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='插件表';

-- ----------------------------
--  Records of `ymt_addons`
-- ----------------------------
INSERT INTO `ymt_addons` VALUES ('EditorForAdmin', '后台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"1\",\"editor_height\":\"500px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1383126253', '0');
INSERT INTO `ymt_addons` VALUES('SiteStat', '站点统计信息', '统计站点的基础信息', '1', '{\"title\":\"\\u7cfb\\u7edf\\u4fe1\\u606f\",\"width\":\"1\",\"display\":\"1\",\"status\":\"0\"}', 'thinkphp', '0.1', '1379512015', '0');
INSERT INTO `ymt_addons` VALUES('Editor', '前台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"2\",\"editor_height\":\"300px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1379830910', '0');
INSERT INTO `ymt_addons` VALUES('Attachment', '附件', '用于文档模型上传附件', '1', 'null', 'thinkphp', '0.1', '1379842319', '1');
INSERT INTO `ymt_addons` VALUES ('SocialComment', '通用社交化评论', '集成了各种社交化评论插件，轻松集成到系统中。', '1', '{\"comment_type\":\"1\",\"comment_uid_youyan\":\"\",\"comment_short_name_duoshuo\":\"\",\"comment_data_list_duoshuo\":\"\"}', 'thinkphp', '0.1', '1380273962', '0');


-- ----------------------------
--  Table structure for `ymt_hooks`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_hooks`;
CREATE TABLE `ymt_hooks` (
  `name` varchar(40) NOT NULL COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_hooks`
-- ----------------------------
INSERT INTO `ymt_hooks` VALUES ('pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('documentEditForm', '添加编辑表单的 扩展内容钩子', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('documentDetailAfter', '文档末尾显示', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('documentDetailBefore', '页面内容前显示用钩子', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('documentSaveComplete', '保存文档数据后的扩展钩子', '2', '0');
INSERT INTO `ymt_hooks` VALUES ('documentEditFormContent', '添加编辑表单的内容显示钩子', '1', '0');
INSERT INTO `ymt_hooks` VALUES ('adminArticleEdit', '后台内容编辑页编辑器', '1', '1378982734');
INSERT INTO `ymt_hooks` VALUES ('AdminIndex', '首页小格子个性化显示', '1', '1465282874');
INSERT INTO `ymt_hooks` VALUES ('topicComment', '评论提交方式扩展钩子。', '1', '1380163518');
INSERT INTO `ymt_hooks` VALUES ('app_begin', '应用开始', '2', '1384481614');
-- ----------------------------
--  Table structure for `ymt_hooks_addons`
-- ----------------------------
DROP TABLE IF EXISTS `ymt_hooks_addons`;
CREATE TABLE `ymt_hooks_addons` (
  `hook_name` varchar(40) NOT NULL COMMENT '钩子名称',
  `addon_name` varchar(40) NOT NULL COMMENT '插件名称',
  `sort` int(11) NOT NULL COMMENT '排序',
  PRIMARY KEY (`hook_name`,`addon_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `ymt_hooks_addons`
-- ----------------------------
INSERT INTO `ymt_hooks_addons` VALUES ('pageFooter', 'ReturnTop', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('documentEditForm', 'Attachment', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('documentDetailAfter', 'Attachment', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('documentDetailAfter', 'SocialComment', '1');
INSERT INTO `ymt_hooks_addons` VALUES ('documentSaveComplete', 'Attachment', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('documentEditFormContent', 'Editor', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('adminArticleEdit', 'EditorForAdmin', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('AdminIndex', 'SiteStat', '0');
INSERT INTO `ymt_hooks_addons` VALUES ('AdminIndex', 'SystemInfo', '1');
INSERT INTO `ymt_hooks_addons` VALUES ('AdminIndex', 'DevTeam', '2');
INSERT INTO `ymt_hooks_addons` VALUES ('topicComment', 'Editor', '0');
