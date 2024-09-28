SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cw_admin
-- ----------------------------
DROP TABLE IF EXISTS `cw_admin`;
CREATE TABLE `cw_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '超管: [0=否, 1=是]',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '管理名称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录的IP',
  `multipoint_login` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '多处登录: [1=是, 0=否]',
  `disable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用: [0=否, 1=是]',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- Table structure for cw_admin_dept
-- ----------------------------
DROP TABLE IF EXISTS `cw_admin_dept`;
CREATE TABLE `cw_admin_dept` (
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理ID',
  `dept_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID',
  PRIMARY KEY (`admin_id`,`dept_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门关联表';

-- ----------------------------
-- Table structure for cw_admin_jobs
-- ----------------------------
DROP TABLE IF EXISTS `cw_admin_jobs`;
CREATE TABLE `cw_admin_jobs` (
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理ID',
  `jobs_id` int(10) unsigned NOT NULL COMMENT '岗位ID',
  PRIMARY KEY (`admin_id`,`jobs_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='岗位关联表';

-- ----------------------------
-- Table structure for cw_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `cw_admin_role`;
CREATE TABLE `cw_admin_role` (
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`admin_id`,`role_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色关联表';

-- ----------------------------
-- Table structure for cw_admin_session
-- ----------------------------
DROP TABLE IF EXISTS `cw_admin_session`;
CREATE TABLE `cw_admin_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `terminal` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '平台类型: 1=PC管理后台, 2=Mobile手机管理后台',
  `token` varchar(32) NOT NULL DEFAULT '' COMMENT '令牌的值',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `admin_id_client` (`admin_id`,`terminal`) USING BTREE COMMENT '一个用户在一个终端只有一个token',
  UNIQUE KEY `token` (`token`) USING BTREE COMMENT 'token是唯一的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理会话表';

-- ----------------------------
-- Table structure for cw_ai_search_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_ai_search_record`;
CREATE TABLE `cw_ai_search_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `channel` varchar(100) NOT NULL DEFAULT '' COMMENT '通道: tiangong',
  `model` varchar(60) NOT NULL DEFAULT '' COMMENT '模型: [search=简易,copilot=增强,research=研究]',
  `type` varchar(60) NOT NULL DEFAULT '' COMMENT '类型: [all=全网,doc=文档,scholar=学术]',
  `ask` varchar(800) NOT NULL DEFAULT '' COMMENT '提问问题',
  `context` text COMMENT '上下文',
  `markdown` text COMMENT 'markdown',
  `results` text COMMENT '结果数据',
  `price` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗费用',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '来源IP',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='AI搜索记录表';

-- ----------------------------
-- Table structure for cw_article
-- ----------------------------
DROP TABLE IF EXISTS `cw_article`;
CREATE TABLE `cw_article` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `cid` int(10) NOT NULL COMMENT '文章分类',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '文章简介',
  `abstract` text COMMENT '文章摘要',
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '文章图片',
  `author` varchar(255) NOT NULL DEFAULT '' COMMENT '文章作者',
  `content` text COMMENT '文章内容',
  `click_virtual` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟浏览量',
  `click_actual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际浏览量',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示: [1=是, 0=否]',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序编号',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章内容表';

-- ----------------------------
-- Table structure for cw_article_cate
-- ----------------------------
DROP TABLE IF EXISTS `cw_article_cate`;
CREATE TABLE `cw_article_cate` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章分类id',
  `name` varchar(90) DEFAULT NULL COMMENT '分类名称',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) DEFAULT '1' COMMENT '是否显示:1-是;0-否',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章分类表';

-- ----------------------------
-- Table structure for cw_article_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_article_collect`;
CREATE TABLE `cw_article_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `article_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文章ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收藏状态 0-未收藏 1-已收藏',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章收藏表';

-- ----------------------------
-- Table structure for cw_card_code
-- ----------------------------
DROP TABLE IF EXISTS `cw_card_code`;
CREATE TABLE `cw_card_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) NOT NULL COMMENT '卡密编号',
  `type` tinyint(1) NOT NULL COMMENT '类型：1-会员套餐；2-充值套餐；3-对话次数；4-绘画次数',
  `relation_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联套餐（充值、会员套餐）',
  `balance` int(11) NOT NULL DEFAULT '0' COMMENT '电力值',
  `card_num` int(11) NOT NULL COMMENT '卡密数量',
  `valid_start_time` int(11) NOT NULL COMMENT '有效开始时间',
  `valid_end_time` int(11) NOT NULL COMMENT '有效结束时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `rule_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '生成规则：1-批次编号+随机字母；2-批次编号+随机数字；',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sn` (`sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='卡密';

-- ----------------------------
-- Table structure for cw_card_code_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_card_code_record`;
CREATE TABLE `cw_card_code_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) NOT NULL COMMENT '卡密编号',
  `card_id` int(11) NOT NULL COMMENT '卡密id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0-未使用，1-已使用',
  `user_id` int(11) DEFAULT NULL COMMENT '使用的用户id',
  `use_time` int(11) DEFAULT NULL COMMENT '使用时间',
  `package_snapshot` text COMMENT '套餐快照',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sn` (`sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='卡密兑换记录';

-- ----------------------------
-- Table structure for cw_chat_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_chat_category`;
CREATE TABLE `cw_chat_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '类目名称',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态: [1=开启, 0=关闭]',
  `image` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT '图标',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='示例分类表';

-- ----------------------------
-- Table structure for cw_chat_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_chat_record`;
CREATE TABLE `cw_chat_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '记录分类',
  `other_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创作的ID',
  `chat_model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对话模型ID',
  `ask` text NOT NULL COMMENT '提问',
  `reply` text NOT NULL COMMENT '回复',
  `channel` varchar(64) NOT NULL DEFAULT '' COMMENT '模型渠道',
  `model` varchar(128) NOT NULL DEFAULT '' COMMENT '对话模型',
  `tokens` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗的tokens',
  `price` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗的费用',
  `correlation` text COMMENT '相关问题',
  `files_plugin` text COMMENT '图片理解',
  `type` int(1) NOT NULL COMMENT '记录类型: [1=对话, 2=创作,3-角色]',
  `creation_type` tinyint(11) NOT NULL DEFAULT '1' COMMENT '创作类型:[1-常规,2-扩写,3-简写,4-续写,5-改写-正式得体,6-改写-严肃庄重,7-改写-轻松,8-改写-热情]',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示: [1=是的, 0=否的]',
  `censor_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态: [0=未审核, 1=合规, 2=不合规, 3=疑似, 4=审核失败]',
  `censor_result` text COMMENT '审核结果',
  `censor_num` int(2) unsigned NOT NULL DEFAULT '0' COMMENT '审核次数',
  `extra` text COMMENT '预留字段',
  `flows` text COMMENT 'tokens信息',
  `reply_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '回复类型：1-模型回复；2-默认回复',
  `ip` varchar(255) DEFAULT NULL COMMENT '访客ip地址',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `category_id` (`category_id`) USING BTREE COMMENT '分类索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='对话记录表';

-- ----------------------------
-- Table structure for cw_chat_record_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_chat_record_category`;
CREATE TABLE `cw_chat_record_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '对话分类名称',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='对话分类表';

-- ----------------------------
-- Table structure for cw_chat_record_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_chat_record_collect`;
CREATE TABLE `cw_chat_record_collect` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `records_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对话记录ID',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='对话收藏表';

-- ----------------------------
-- Table structure for cw_chat_sample
-- ----------------------------
DROP TABLE IF EXISTS `cw_chat_sample`;
CREATE TABLE `cw_chat_sample` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类目id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `content` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL COMMENT '状态: [1=是, 0=否]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='对话示例表';

-- ----------------------------
-- Table structure for cw_config
-- ----------------------------
DROP TABLE IF EXISTS `cw_config`;
CREATE TABLE `cw_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '名称',
  `value` text COMMENT '值',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='全局配置表';

-- ----------------------------
-- Table structure for cw_creation_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_creation_category`;
CREATE TABLE `cw_creation_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `image` varchar(128) NOT NULL DEFAULT '' COMMENT '分类图标',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '类目名称',
  `sort` int(10) NOT NULL COMMENT '排序编号',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '启用状态: [1=开启, 0=关闭]',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='创作类别表';

-- ----------------------------
-- Table structure for cw_creation_model
-- ----------------------------
DROP TABLE IF EXISTS `cw_creation_model`;
CREATE TABLE `cw_creation_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '名称',
  `image` varchar(64) NOT NULL COMMENT '图标',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类别',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1-开启，0-关闭',
  `content` text COMMENT '主题内容',
  `tips` text COMMENT '提示文字',
  `context_num` int(5) unsigned NOT NULL DEFAULT '2' COMMENT '上下文总数',
  `n` int(5) unsigned NOT NULL DEFAULT '1' COMMENT '最大回复',
  `top_p` decimal(2,1) unsigned NOT NULL DEFAULT '0.9' COMMENT '随机属性',
  `presence_penalty` decimal(2,1) unsigned NOT NULL DEFAULT '0.5' COMMENT '话题属性',
  `frequency_penalty` decimal(2,1) unsigned NOT NULL DEFAULT '0.5' COMMENT '重复属性',
  `temperature` decimal(2,1) unsigned NOT NULL DEFAULT '0.6' COMMENT '词汇属性',
  `max_tokens` int(5) unsigned NOT NULL DEFAULT '150' COMMENT '最大回复',
  `form` text NOT NULL COMMENT '表单数据',
  `virtual_use_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟使用',
  `system` varchar(255) DEFAULT NULL COMMENT '全局指令',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='创作模型表';

-- ----------------------------
-- Table structure for cw_creation_model_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_creation_model_collect`;
CREATE TABLE `cw_creation_model_collect` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `creation_id` int(10) unsigned NOT NULL COMMENT '创作ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='创作收藏表';

-- ----------------------------
-- Table structure for cw_decorate_page
-- ----------------------------
DROP TABLE IF EXISTS `cw_decorate_page`;
CREATE TABLE `cw_decorate_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '10' COMMENT '页面类型: [1=首页装修, 2=悬浮菜单]',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '页面名称',
  `data` text COMMENT '页面数据',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='装修页面表';

-- ----------------------------
-- Table structure for cw_dept
-- ----------------------------
DROP TABLE IF EXISTS `cw_dept`;
CREATE TABLE `cw_dept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '部门名称',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `leader` varchar(64) NOT NULL DEFAULT '' COMMENT '负责人',
  `mobile` varchar(16) NOT NULL DEFAULT '' COMMENT '联系电话',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '部门状态: [0=停用, 1=正常]',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门管理表';

-- ----------------------------
-- Table structure for cw_dev_crontab
-- ----------------------------
DROP TABLE IF EXISTS `cw_dev_crontab`;
CREATE TABLE `cw_dev_crontab` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '定时任务名称',
  `type` tinyint(1) NOT NULL COMMENT '类型 1-定时任务',
  `system` tinyint(4) DEFAULT '0' COMMENT '是否系统任务 0-否 1-是',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `command` varchar(64) NOT NULL COMMENT '命令内容',
  `params` varchar(64) DEFAULT '' COMMENT '参数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 1-运行 2-停止 3-错误',
  `expression` varchar(64) NOT NULL COMMENT '运行规则',
  `error` varchar(256) DEFAULT NULL COMMENT '运行失败原因',
  `last_time` int(10) DEFAULT NULL COMMENT '最后执行时间',
  `time` varchar(64) DEFAULT '0' COMMENT '实时执行时长',
  `max_time` varchar(64) DEFAULT '0' COMMENT '最大执行时长',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='计划任务表';

-- ----------------------------
-- Table structure for cw_dev_pay_config
-- ----------------------------
DROP TABLE IF EXISTS `cw_dev_pay_config`;
CREATE TABLE `cw_dev_pay_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '模版名称',
  `pay_way` tinyint(1) NOT NULL COMMENT '支付方式: [1=余额支付, 2=微信支付, 3=支付宝支付]',
  `config` text COMMENT '支付配置',
  `icon` varchar(250) NOT NULL COMMENT '图标',
  `remark` varchar(250) NOT NULL COMMENT '备注',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付方式表';

-- ----------------------------
-- Table structure for cw_dev_pay_way
-- ----------------------------
DROP TABLE IF EXISTS `cw_dev_pay_way`;
CREATE TABLE `cw_dev_pay_way` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_config_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付配置ID',
  `scene` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付场景: [1=微信小程序, 2=微信公众号, 3=H5, 4=PC, 5=APP]',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认支付: [0=否, 1=是]',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '配置状态: [0=关闭, 1=开启]',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付配置表';

-- ----------------------------
-- Table structure for cw_dict_data
-- ----------------------------
DROP TABLE IF EXISTS `cw_dict_data`;
CREATE TABLE `cw_dict_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) NOT NULL COMMENT '数据名称',
  `value` varchar(255) NOT NULL COMMENT '数据值',
  `type_id` int(10) NOT NULL COMMENT '字典类型id',
  `type_value` varchar(255) NOT NULL COMMENT '字典类型',
  `sort` int(10) DEFAULT '0' COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典数据表';

-- ----------------------------
-- Table structure for cw_dict_type
-- ----------------------------
DROP TABLE IF EXISTS `cw_dict_type`;
CREATE TABLE `cw_dict_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '字典名称',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '字典类型名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='字典类型表';

-- ----------------------------
-- Table structure for cw_distribution_apply
-- ----------------------------
DROP TABLE IF EXISTS `cw_distribution_apply`;
CREATE TABLE `cw_distribution_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `name` varchar(12) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(32) NOT NULL COMMENT '手机号码',
  `audit_remark` varchar(255) DEFAULT NULL COMMENT '审核备注',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1-待审核；2-审核通过；3-审核不通过',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='分销申请表';

-- ----------------------------
-- Table structure for cw_distribution_order
-- ----------------------------
DROP TABLE IF EXISTS `cw_distribution_order`;
CREATE TABLE `cw_distribution_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_type` tinyint(1) NOT NULL COMMENT '订单类型：1-充值订单；2-会员订单；',
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `order_sn` varchar(255) NOT NULL COMMENT '订单编号',
  `order_amount` decimal(10,2) unsigned NOT NULL COMMENT '实付金额',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `user_id` int(11) NOT NULL COMMENT '下单用户ID',
  `first_user_id` int(11) NOT NULL COMMENT '一级分销商用户ID',
  `first_ratio` decimal(10,2) unsigned NOT NULL COMMENT '一级分销佣金比例',
  `first_reward` decimal(10,2) unsigned NOT NULL COMMENT '一级分销奖励',
  `second_user_id` int(11) DEFAULT NULL COMMENT '二级分销商用户ID',
  `second_ratio` decimal(10,2) unsigned DEFAULT NULL COMMENT '二级分销佣金比例',
  `second_reward` decimal(10,2) unsigned DEFAULT NULL COMMENT '二级分销奖励',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='分销订单表';

-- ----------------------------
-- Table structure for cw_draw_lora
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_lora`;
CREATE TABLE `cw_draw_lora` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover` varchar(100) NOT NULL COMMENT '模型封面',
  `title` varchar(50) NOT NULL COMMENT '模型名称',
  `model_name` varchar(100) NOT NULL COMMENT '模型标识',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD微调模型表';

-- ----------------------------
-- Table structure for cw_draw_model
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_model`;
CREATE TABLE `cw_draw_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '模型标题',
  `model_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '模型名称',
  `cover` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '模型封面',
  `category_id` int(10) NOT NULL COMMENT '模型分类',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(1) NOT NULL COMMENT '状态:[0=开启,1=关闭]',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画模型表';

-- ----------------------------
-- Table structure for cw_draw_model_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_model_category`;
CREATE TABLE `cw_draw_model_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '分类名称',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(1) NOT NULL COMMENT '状态:[0=正常,1=禁用]',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画模型分类表';

-- ----------------------------
-- Table structure for cw_draw_model_lora_relation
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_model_lora_relation`;
CREATE TABLE `cw_draw_model_lora_relation` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `lora_id` int(10) NOT NULL COMMENT '微调模型ID',
  `model_id` int(10) NOT NULL COMMENT '主要模型ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画模型分类关联表';

-- ----------------------------
-- Table structure for cw_draw_prompt
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_prompt`;
CREATE TABLE `cw_draw_prompt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `model` varchar(100) NOT NULL DEFAULT 'sd' COMMENT '绘画模型',
  `prompt` varchar(255) NOT NULL DEFAULT '' COMMENT '中文关键词',
  `prompt_en` varchar(255) NOT NULL DEFAULT '' COMMENT '英文关键词',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画提示词库表';

-- ----------------------------
-- Table structure for cw_draw_prompt_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_prompt_category`;
CREATE TABLE `cw_draw_prompt_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT 'sd' COMMENT '绘画模型',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级id',
  `level` tinyint(10) unsigned NOT NULL DEFAULT '0' COMMENT '层级',
  `name` varchar(32) NOT NULL COMMENT '类目名称',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画提示词分类';

-- ----------------------------
-- Table structure for cw_draw_prompt_example
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_prompt_example`;
CREATE TABLE `cw_draw_prompt_example` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(100) NOT NULL DEFAULT 'sd' COMMENT '绘画模型',
  `prompt` text NOT NULL COMMENT '中文关键词',
  `prompt_en` text NOT NULL COMMENT '英文关键词',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画提示词示例表';

-- ----------------------------
-- Table structure for cw_draw_records
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_records`;
CREATE TABLE `cw_draw_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '绘图类型 1-文生图  2-图生图 3-选中放大 4-选中变换',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '动作 绘图动作',
  `prompt` text NOT NULL COMMENT '提示词',
  `prompt_en` text COMMENT '提示词译文',
  `prompt_desc` text NOT NULL COMMENT '提示词详情',
  `prompt_other` varchar(300) DEFAULT NULL COMMENT '提示词其他参数',
  `image` varchar(255) DEFAULT '' COMMENT '本地图片地址',
  `image_base` varchar(255) DEFAULT '' COMMENT '垫图地址',
  `image_mask` text COMMENT '重绘区域base64',
  `image_url` varchar(500) DEFAULT NULL COMMENT '绘图服务返回地址',
  `image_id` varchar(255) DEFAULT NULL COMMENT '绘图服务图片id',
  `scale` varchar(20) DEFAULT NULL COMMENT '图片比例',
  `task_id` varchar(200) DEFAULT '0' COMMENT '绘图服务任务id',
  `origin_task_id` varchar(100) DEFAULT '' COMMENT '父级任务ID',
  `thumbnail` varchar(500) DEFAULT '' COMMENT '缩略图',
  `notify_snap` text COMMENT '回调快照信息',
  `able_actions` varchar(255) DEFAULT NULL COMMENT '可操作动作',
  `use_tokens` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消耗tokens',
  `fail_reason` varchar(600) DEFAULT NULL COMMENT '失败原因',
  `model` varchar(100) DEFAULT '' COMMENT '绘画模型',
  `version` varchar(255) DEFAULT '' COMMENT '绘画版本',
  `negative_prompt` varchar(300) DEFAULT NULL COMMENT '绘画中不想出现的参数',
  `style` varchar(255) DEFAULT '' COMMENT '绘画风格',
  `quality` varchar(30) DEFAULT '' COMMENT '图片质量',
  `engine` varchar(255) DEFAULT 'midjourney' COMMENT '绘画引擎',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0-未启动 1-执行中 2-失败 3-成功',
  `censor_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0-未审核；1-合规；2-不合规；3-疑似；4-审核失败；',
  `ip` varchar(255) NOT NULL COMMENT '访客ip地址',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  `complex_params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT '高级参数',
  `loras` text COMMENT '微调模型',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `task_id` (`task_id`) USING BTREE COMMENT '任务索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画记录表';

-- ----------------------------
-- Table structure for cw_draw_records_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_records_collect`;
CREATE TABLE `cw_draw_records_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `square_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广场ID',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绘画记录收藏';

-- ----------------------------
-- Table structure for cw_draw_square
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_square`;
CREATE TABLE `cw_draw_square` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operate_id` int(11) NOT NULL COMMENT '操作人ID',
  `source` tinyint(1) NOT NULL COMMENT '来源：1-官方；2-用户；',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `records_id` int(11) NOT NULL DEFAULT '0' COMMENT '绘画分享来源ID',
  `prompts` text CHARACTER SET utf8mb4 NOT NULL COMMENT '英文提示词',
  `prompts_cn` text CHARACTER SET utf8mb4 COMMENT '中文提示词',
  `image` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '图片',
  `thumbnail` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '缩略图',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示：1-是；0-否；',
  `is_slice` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为切图 [0-否 1-是]',
  `verify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0-待审核；1-审核通过；2-审核不通过；',
  `verify_result` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '审核结果',
  `avatar` varchar(200) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '头像',
  `nickname` varchar(32) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '用户昵称',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='绘画广场表';

-- ----------------------------
-- Table structure for cw_draw_task
-- ----------------------------
DROP TABLE IF EXISTS `cw_draw_task`;
CREATE TABLE `cw_draw_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `action` varchar(255) NOT NULL DEFAULT '' COMMENT '动作  generate upsample variation',
  `task_id` varchar(30) NOT NULL DEFAULT '' COMMENT '任务id',
  `notify_hook` varchar(255) DEFAULT '' COMMENT '回调地址',
  `notify_msg` text COMMENT '回调错误信息',
  `image` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `prompt` text COMMENT '提示词-中文',
  `prompt_desc` text COMMENT '完整描述',
  `index` int(1) unsigned DEFAULT '0' COMMENT '图片索引',
  `image_base` varchar(255) DEFAULT NULL COMMENT '垫图图片地址',
  `image_msg_id` varchar(255) DEFAULT '' COMMENT '消息id，基于消息id进行图片变换操作',
  `image_msg_hash` text COMMENT '消息hash,基于消息hash进行图片变换操作',
  `image_url` varchar(600) DEFAULT NULL COMMENT '图片地址',
  `msg_id` varchar(255) DEFAULT NULL COMMENT '图片消息id',
  `msg_hash` text COMMENT '图片消息hash',
  `request_snap` text COMMENT '请求快照',
  `response_snap` text COMMENT 'mj响应快照',
  `token` varchar(255) DEFAULT '' COMMENT '绘画token',
  `fail_reason` text COMMENT '失败原因',
  `progress` int(3) DEFAULT '0' COMMENT '任务进度',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '任务状态：0-未处理 1-已提交 2-执行中 3-成功 4-失败',
  `submit_time` int(11) unsigned DEFAULT NULL COMMENT '提交时间',
  `start_time` int(11) unsigned DEFAULT NULL COMMENT '开始执行时间',
  `finish_time` int(11) unsigned DEFAULT NULL COMMENT '完成时间',
  `create_time` int(11) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `task_id` (`task_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='SD绘画任务表';

-- ----------------------------
-- Table structure for cw_email_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_email_log`;
CREATE TABLE `cw_email_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '场景ID',
  `email` varchar(255) NOT NULL COMMENT '邮箱',
  `content` varchar(255) NOT NULL COMMENT '发送内容',
  `code` varchar(32) DEFAULT NULL COMMENT '验证码',
  `is_verify` tinyint(1) unsigned DEFAULT '0' COMMENT '是否已验证: [0=否, 1=是]',
  `check_num` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '验证次数',
  `send_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态: [0=发送中, 1=发送成功, 2=发送失败]',
  `send_time` int(10) NOT NULL COMMENT '发送时间',
  `results` text COMMENT '发送结果',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件记录表';

-- ----------------------------
-- Table structure for cw_file
-- ----------------------------
DROP TABLE IF EXISTS `cw_file`;
CREATE TABLE `cw_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类目ID',
  `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传者ID',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '来源类型: [0=后台, 1=用户]',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '10' COMMENT '文件类型: [10=图片, 20=视频]',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '文件名称',
  `uri` varchar(200) NOT NULL COMMENT '文件路径',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件管理表';

-- ----------------------------
-- Table structure for cw_file_cate
-- ----------------------------
DROP TABLE IF EXISTS `cw_file_cate`;
CREATE TABLE `cw_file_cate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '10' COMMENT '类型: [10=图片，20=视频，30=文件]',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件分类表';

-- ----------------------------
-- Table structure for cw_generate_column
-- ----------------------------
DROP TABLE IF EXISTS `cw_generate_column`;
CREATE TABLE `cw_generate_column` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_id` int(11) NOT NULL DEFAULT '0' COMMENT '表id',
  `column_name` varchar(100) NOT NULL DEFAULT '' COMMENT '字段名称',
  `column_comment` varchar(300) NOT NULL DEFAULT '' COMMENT '字段描述',
  `column_type` varchar(100) NOT NULL DEFAULT '' COMMENT '字段类型',
  `is_required` tinyint(1) DEFAULT '0' COMMENT '是否必填 0-非必填 1-必填',
  `is_pk` tinyint(1) DEFAULT '0' COMMENT '是否为主键 0-不是 1-是',
  `is_insert` tinyint(1) DEFAULT '0' COMMENT '是否为插入字段 0-不是 1-是',
  `is_update` tinyint(1) DEFAULT '0' COMMENT '是否为更新字段 0-不是 1-是',
  `is_lists` tinyint(1) DEFAULT '0' COMMENT '是否为列表字段 0-不是 1-是',
  `is_query` tinyint(1) DEFAULT '0' COMMENT '是否为查询字段 0-不是 1-是',
  `query_type` varchar(100) DEFAULT '=' COMMENT '查询类型',
  `view_type` varchar(100) DEFAULT 'input' COMMENT '显示类型',
  `dict_type` varchar(255) DEFAULT '' COMMENT '字典类型',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='生成字段表';

-- ----------------------------
-- Table structure for cw_generate_table
-- ----------------------------
DROP TABLE IF EXISTS `cw_generate_table`;
CREATE TABLE `cw_generate_table` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `table_name` varchar(200) NOT NULL DEFAULT '' COMMENT '表名称',
  `table_comment` varchar(300) NOT NULL DEFAULT '' COMMENT '表描述',
  `template_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '模板类型 0-单表(curd) 1-树表(curd)',
  `author` varchar(100) DEFAULT '' COMMENT '作者',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `generate_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '生成方式  0-压缩包下载 1-生成到模块',
  `module_name` varchar(100) DEFAULT '' COMMENT '模块名',
  `class_dir` varchar(100) DEFAULT '' COMMENT '类目录名',
  `class_comment` varchar(100) DEFAULT '' COMMENT '类描述',
  `admin_id` int(11) DEFAULT '0' COMMENT '管理员id',
  `menu` text COMMENT '菜单配置',
  `delete` text COMMENT '删除配置',
  `tree` text COMMENT '树表配置',
  `relations` text COMMENT '关联配置',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='生成信息表';

-- ----------------------------
-- Table structure for cw_jobs
-- ----------------------------
DROP TABLE IF EXISTS `cw_jobs`;
CREATE TABLE `cw_jobs` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(50) NOT NULL COMMENT '岗位名称',
  `code` varchar(64) NOT NULL COMMENT '岗位编码',
  `sort` int(10) DEFAULT '0' COMMENT '显示顺序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0停用 1正常）',
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='岗位管理表';

-- ----------------------------
-- Table structure for cw_kb_digital
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_digital`;
CREATE TABLE `cw_kb_digital` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '数字人名称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '数字人头像',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '数字人封面',
  `wide_stay_video` varchar(255) NOT NULL DEFAULT '' COMMENT '宽屏人物待机视频',
  `wide_talk_video` varchar(255) NOT NULL DEFAULT '' COMMENT '宽屏人物说话视频',
  `vertical_stay_video` varchar(255) NOT NULL DEFAULT '' COMMENT '竖屏人物待机视频',
  `vertical_talk_video` varchar(255) NOT NULL DEFAULT '' COMMENT '竖屏人物说话视频',
  `channel` varchar(100) NOT NULL DEFAULT '' COMMENT '配音渠道',
  `dubbing` varchar(100) NOT NULL DEFAULT '' COMMENT '配音角色',
  `idle_time` int(10) unsigned NOT NULL DEFAULT '10' COMMENT '闲时时间',
  `idle_reply` text COMMENT '闲时回复',
  `is_disable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='数字人管理表';

-- ----------------------------
-- Table structure for cw_kb_know
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_know`;
CREATE TABLE `cw_kb_know` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `create_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建者ID',
  `image` varchar(250) NOT NULL DEFAULT '' COMMENT '知识库封面',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '知识库名称',
  `intro` varchar(500) NOT NULL DEFAULT '' COMMENT '知识库简介',
  `documents_model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '处理模型ID',
  `embedding_model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '向量模型ID',
  `documents_model_sub_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '处理模型子ID',
  `embedding_model_sub_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '向量模型子ID',
  `documents_model` varchar(100) NOT NULL DEFAULT '' COMMENT '处理模型 (废弃)',
  `embedding_model` varchar(100) NOT NULL DEFAULT '' COMMENT '训练模型 (废弃)',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识库管理表';

-- ----------------------------
-- Table structure for cw_kb_know_files
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_know_files`;
CREATE TABLE `cw_kb_know_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户ID',
  `know_id` int(10) unsigned NOT NULL COMMENT '知识库ID',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file` varchar(300) NOT NULL DEFAULT '' COMMENT '文件路径',
  `is_qa` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'QA拆分: [0=否, 1=待拆分, 2=拆分完成]',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '默认固定: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `know_idx` (`know_id`) USING BTREE COMMENT '知识库索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识库文件表';

-- ----------------------------
-- Table structure for cw_kb_know_qa
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_know_qa`;
CREATE TABLE `cw_kb_know_qa` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型的ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的ID',
  `kb_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '知识库ID',
  `fd_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件的ID',
  `name` varchar(300) NOT NULL DEFAULT '' COMMENT '文件名称',
  `content` text COMMENT '文本内容',
  `results` text COMMENT '拆分结果',
  `usage` text COMMENT 'tokens信息',
  `tokens` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消耗的tokens',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '拆分的模型',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '拆分状态: [0=等待拆分, 1=拆分中, 2=拆分成功, 3=拆分失败]',
  `error` text COMMENT '错误信息',
  `task_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务耗时',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `kb_idx` (`kb_id`) USING BTREE COMMENT '知识库索引',
  KEY `fd_idx` (`fd_id`) USING BTREE COMMENT '文件的索引',
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识库QA表';

-- ----------------------------
-- Table structure for cw_kb_know_team
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_know_team`;
CREATE TABLE `cw_kb_know_team` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `kb_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '知识库ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的ID',
  `power` tinyint(3) unsigned NOT NULL DEFAULT '3' COMMENT '拥有权限: [1=可管理, 2=可编辑, 3=可查看]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='知识库团队表';

-- ----------------------------
-- Table structure for cw_kb_robot
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot`;
CREATE TABLE `cw_kb_robot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类目ID',
  `code` varchar(100) NOT NULL DEFAULT '' COMMENT '机器人编号',
  `kb_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '关联知识库',
  `icons` varchar(250) NOT NULL DEFAULT '' COMMENT '对话的图标',
  `image` varchar(250) NOT NULL DEFAULT '' COMMENT '机器人封面',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '机器人名称',
  `intro` varchar(500) NOT NULL DEFAULT '' COMMENT '机器人简介',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序的编号',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT 'AI模型(废弃字段)',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'AI主模型ID',
  `model_sub_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'AI子模型ID',
  `roles_prompt` text COMMENT '角色设定词',
  `limit_prompt` text COMMENT '系统限定词',
  `search_similarity` float(5,3) unsigned NOT NULL DEFAULT '0.800' COMMENT '搜索相似度',
  `search_limits` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '搜索单词数',
  `search_empty_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '搜索空类型: [1=GPT回复, 2=固定回复]',
  `search_empty_text` text COMMENT '搜索空文本',
  `welcome_introducer` text COMMENT '欢迎引导词',
  `related_issues_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '推荐的问题',
  `copyright` text COMMENT '底部的版权',
  `share_bg` varchar(300) DEFAULT '' COMMENT '分享背景图',
  `digital_bg` varchar(30) NOT NULL COMMENT '数字人背景',
  `digital_id` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '数字人绑定',
  `is_digital` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '数字人启用: [0=否, 1=是]',
  `is_show_context` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '显示上下文: [0=否, 1=是]',
  `is_show_quote` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '显示引用词: [0=否, 1=是]',
  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否公开它: [0=否, 1=是]',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可使用: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人管理表';

-- ----------------------------
-- Table structure for cw_kb_robot_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_category`;
CREATE TABLE `cw_kb_robot_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `image` varchar(250) NOT NULL DEFAULT '' COMMENT '图标',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='机器人分类表';

-- ----------------------------
-- Table structure for cw_kb_robot_instruct
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_instruct`;
CREATE TABLE `cw_kb_robot_instruct` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的ID',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `keyword` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '关键词',
  `content` text CHARACTER SET utf8 COMMENT '回复内容',
  `images` text CHARACTER SET utf8 COMMENT '上传图片',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序编号',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人指令表';

-- ----------------------------
-- Table structure for cw_kb_robot_publish
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_publish`;
CREATE TABLE `cw_kb_robot_publish` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` tinyint(2) NOT NULL COMMENT '类型: [1=网页, 2=公众号, 3=JS嵌入, 4=API调用]',
  `chat_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '对话方式: [1=文本, 2=数字人]',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户的ID',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '分享名称',
  `apikey` varchar(200) NOT NULL DEFAULT '' COMMENT '渠道编号',
  `secret` varchar(200) NOT NULL COMMENT '访问密钥',
  `context_num` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '上下文数',
  `limit_total_chat` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户总的限制对话',
  `limit_today_chat` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户每天限制对话',
  `limit_exceed` varchar(500) NOT NULL DEFAULT '' COMMENT '超出限制默认回复',
  `use_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '调用次数',
  `use_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用时间',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `robot_idx` (`robot_id`) USING BTREE COMMENT '机器人索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人发布表';

-- ----------------------------
-- Table structure for cw_kb_robot_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_record`;
CREATE TABLE `cw_kb_robot_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户的ID',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类的ID',
  `square_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '广场的ID',
  `chat_model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对话模型ID',
  `emb_model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '向量模型ID',
  `ask` text COMMENT '提问',
  `reply` text COMMENT '答复',
  `files_plugin` text COMMENT '图片理解',
  `images` text COMMENT '附带图片',
  `video` text COMMENT '附带视频',
  `files` text COMMENT '附带文件',
  `quotes` text COMMENT '引用内容',
  `context` text COMMENT '上下文组',
  `correlation` text COMMENT '相关问题',
  `flows` text NOT NULL COMMENT 'tokens信息',
  `model` varchar(100) NOT NULL DEFAULT '' COMMENT '对话模型',
  `tokens` decimal(15,7) NOT NULL COMMENT '消耗金额',
  `feedback` text COMMENT '用户反馈',
  `share_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分享的ID',
  `share_apikey` varchar(80) NOT NULL DEFAULT '' COMMENT '分享的密钥',
  `share_identity` varchar(60) NOT NULL DEFAULT '' COMMENT '分享的身份',
  `censor_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '审核状态: [0=未审核, 1=合规, 2=不合规, 3=疑似, 4=审核失败]',
  `censor_result` text COMMENT '审核结果',
  `censor_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核次数',
  `is_feedback` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否反馈: [0=否, 1=是]',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示: [0=否, 1=是]',
  `task_time` int(60) unsigned NOT NULL DEFAULT '0' COMMENT '对话耗时',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `robot_idx` (`robot_id`) USING BTREE COMMENT '机器人索引',
  KEY `share_idx` (`share_id`) USING BTREE COMMENT '分享编号索引',
  KEY `identity_idx` (`share_identity`) USING BTREE COMMENT '分享身份索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人对话表';

-- ----------------------------
-- Table structure for cw_kb_robot_session
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_session`;
CREATE TABLE `cw_kb_robot_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `square_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '广场ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `name` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分类名称',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `robot_idx` (`robot_id`) USING BTREE COMMENT '机器人索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人会话表';

-- ----------------------------
-- Table structure for cw_kb_robot_share_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_share_log`;
CREATE TABLE `cw_kb_robot_share_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `robot_id` int(10) unsigned NOT NULL COMMENT '机器人ID',
  `balance` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '赠送电力值',
  `channel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分享渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `square_id` int(10) NOT NULL COMMENT '广场id',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='机器人分享记录';

-- ----------------------------
-- Table structure for cw_kb_robot_square
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_square`;
CREATE TABLE `cw_kb_robot_square` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `robot_id` int(10) unsigned NOT NULL COMMENT '机器人ID',
  `cate_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序编号',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示: [0=否， 1=是]',
  `verify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0-待审核；1-审核通过；2-审核不通过；',
  `verify_result` varchar(255) DEFAULT NULL COMMENT '审核结果',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='机器人广场表';

-- ----------------------------
-- Table structure for cw_kb_robot_visitor
-- ----------------------------
DROP TABLE IF EXISTS `cw_kb_robot_visitor`;
CREATE TABLE `cw_kb_robot_visitor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(100) NOT NULL COMMENT '访客IP',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `terminal` tinyint(1) NOT NULL COMMENT '访问终端',
  `visit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `create_time` int(10) DEFAULT NULL COMMENT '访问时间',
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `robot_idx` (`robot_id`) USING BTREE COMMENT '机器人索引',
  KEY `ip` (`ip`) USING BTREE COMMENT 'IP索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='机器人访问表';

-- ----------------------------
-- Table structure for cw_key_pool
-- ----------------------------
DROP TABLE IF EXISTS `cw_key_pool`;
CREATE TABLE `cw_key_pool` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型: [1=AI对话, 2=AI向量, 3=语音播报, 4=语音输入]',
  `channel` varchar(32) NOT NULL DEFAULT '' COMMENT '渠道: [gpt3.5,gpt4.0,zhipu,baidu]',
  `key` varchar(800) NOT NULL DEFAULT '' COMMENT '密钥',
  `appid` varchar(20) NOT NULL DEFAULT '' COMMENT 'appId',
  `secret` varchar(128) NOT NULL DEFAULT '' COMMENT 'secret',
  `api` varchar(300) DEFAULT '' COMMENT '错误接口',
  `notice` text COMMENT '错误通知',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '密钥状态',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='密钥池子表';

-- ----------------------------
-- Table structure for cw_key_rule
-- ----------------------------
DROP TABLE IF EXISTS `cw_key_rule`;
CREATE TABLE `cw_key_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `model_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '模型ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '规则类型: [1=对话]',
  `channel` varchar(32) NOT NULL DEFAULT '' COMMENT '接口类型',
  `rule` varchar(500) NOT NULL DEFAULT '' COMMENT '停用规则',
  `prompt` varchar(255) NOT NULL DEFAULT '' COMMENT '停用提示',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '规则状态: [1=开启, 0=关闭]',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='密钥规则表';

-- ----------------------------
-- Table structure for cw_member_adjust_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_adjust_log`;
CREATE TABLE `cw_member_adjust_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `operate_id` int(11) NOT NULL COMMENT '操作员ID',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐',
  `member_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '套餐时长',
  `is_perpetual` tinyint(1) NOT NULL COMMENT '是否永久:1-是,0-否',
  `package_snap` text COMMENT '套餐快照(type=1是记录的是原套餐快照，type=2是套餐快照)',
  `type` int(11) DEFAULT '1' COMMENT '类型：1-后台调整，2-卡密兑换',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员等级调整记录';

-- ----------------------------
-- Table structure for cw_member_benefits
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_benefits`;
CREATE TABLE `cw_member_benefits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL COMMENT '套餐id',
  `name` varchar(255) NOT NULL COMMENT '权益名称',
  `image` varchar(255) NOT NULL COMMENT '权益图标',
  `describe` varchar(255) DEFAULT NULL COMMENT '权益描述',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-开启,0-关闭',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员权益';

-- ----------------------------
-- Table structure for cw_member_order
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_order`;
CREATE TABLE `cw_member_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `order_sn` varchar(64) NOT NULL COMMENT '订单编号',
  `terminal` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '订单来源：1-微信小程序；2-微信公众号；3-手机H5；4-PC；5-苹果app；6-安卓app；',
  `pay_sn` varchar(255) DEFAULT '' COMMENT '支付编号-冗余字段，针对微信同一主体不同客户端支付需用不同订单号预留。',
  `pay_way` tinyint(2) NOT NULL DEFAULT '2' COMMENT '支付方式 2-微信支付 3-支付宝支付',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态：0-待支付；1-已支付',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `order_amount` decimal(10,2) unsigned NOT NULL COMMENT '实付金额',
  `discount_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '优惠金额',
  `total_amount` decimal(10,2) unsigned NOT NULL COMMENT '订单总价',
  `transaction_id` varchar(128) DEFAULT NULL COMMENT '第三方平台交易流水号',
  `member_package_id` int(11) NOT NULL COMMENT '套餐ID',
  `member_price_id` int(11) NOT NULL COMMENT '套餐价格id',
  `member_package_info` text COMMENT '套餐信息',
  `refund_status` tinyint(1) DEFAULT '0' COMMENT '退款状态 0-未退款 1-已退款',
  `refund_transaction_id` varchar(255) DEFAULT NULL COMMENT '退款交易流水号',
  `package_end_time` int(11) DEFAULT NULL COMMENT '套餐到期时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员订单表';

-- ----------------------------
-- Table structure for cw_member_package
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_package`;
CREATE TABLE `cw_member_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '套餐名称',
  `describe` varchar(255) DEFAULT NULL COMMENT '套餐描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架:1-上架,0-下架',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `is_recommend` int(11) NOT NULL DEFAULT '0' COMMENT '是否推荐:1-是,0-否',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) NOT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员套餐主表';

-- ----------------------------
-- Table structure for cw_member_package_apply
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_package_apply`;
CREATE TABLE `cw_member_package_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL COMMENT '套餐',
  `type` tinyint(1) NOT NULL COMMENT '类型:1-对话,2-向量,3-绘画,4-音乐',
  `channel` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '渠道',
  `status` tinyint(1) NOT NULL COMMENT '会员免费状态:1-开启,0-关闭',
  `day_limit` int(11) NOT NULL COMMENT '每天使用上限',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员套餐关联应用表';

-- ----------------------------
-- Table structure for cw_member_package_comment
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_package_comment`;
CREATE TABLE `cw_member_package_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评价类型：1-虚拟评价；2-用户自评；',
  `member_package_id` int(11) NOT NULL COMMENT '会员套餐ID',
  `image` varchar(255) NOT NULL COMMENT '头像',
  `name` varchar(255) NOT NULL COMMENT '用户昵称',
  `comment_content` text NOT NULL COMMENT '评价内容',
  `comment_level` tinyint(1) NOT NULL COMMENT '评价等级：1-5星',
  `status` tinyint(1) NOT NULL COMMENT '状态：1-显示；0-隐藏；',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员套餐评价';

-- ----------------------------
-- Table structure for cw_member_package_price
-- ----------------------------
DROP TABLE IF EXISTS `cw_member_package_price`;
CREATE TABLE `cw_member_package_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL COMMENT '套餐id',
  `duration` int(5) unsigned NOT NULL COMMENT '套餐时长',
  `duration_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '套餐时长类型:1-月,2-日,3-永久',
  `sell_price` decimal(10,2) unsigned NOT NULL COMMENT '销售价格',
  `lineation_price` decimal(10,2) unsigned DEFAULT NULL COMMENT '划线价',
  `give_balance` decimal(15,7) DEFAULT NULL COMMENT '赠送电力值',
  `give_robot` int(11) DEFAULT NULL COMMENT '赠送机器人',
  `is_give` tinyint(1) NOT NULL DEFAULT '1' COMMENT '额外赠送：1-是,0-否',
  `is_recommend` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否推荐:1-是,0-否',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:1-开启,0-关闭',
  `tags` varchar(255) DEFAULT NULL COMMENT '标签',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员套餐价格表';

-- ----------------------------
-- Table structure for cw_models
-- ----------------------------
DROP TABLE IF EXISTS `cw_models`;
CREATE TABLE `cw_models` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '模型类型: [1=对话模型, 2=向量模型]',
  `channel` varchar(30) NOT NULL DEFAULT '' COMMENT '模型渠道',
  `logo` varchar(300) NOT NULL DEFAULT '' COMMENT '模型图标',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模型名称',
  `remarks` varchar(800) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '模型描述',
  `configs` text COMMENT '模型配置',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序编号',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '系统内置: [0=否, 1=是]',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用: [0=否, 1=是]',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模型管理表';

-- ----------------------------
-- Table structure for cw_models_cost
-- ----------------------------
DROP TABLE IF EXISTS `cw_models_cost`;
CREATE TABLE `cw_models_cost` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `model_id` int(10) NOT NULL COMMENT '模型ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '模型类型: [1=对话模型, 2=向量模型]',
  `channel` varchar(100) NOT NULL DEFAULT '' COMMENT '模型渠道',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模型名称',
  `alias` varchar(100) NOT NULL DEFAULT '' COMMENT '模型别名',
  `price` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT '消费价格',
  `sort` int(10) unsigned NOT NULL COMMENT '排序编号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='模型计费表';

-- ----------------------------
-- Table structure for cw_music_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_music_record`;
CREATE TABLE `cw_music_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '任务id',
  `clips_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '片段id',
  `style_id` varchar(100) NOT NULL DEFAULT '0' COMMENT '风格id',
  `tags` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '风格',
  `channel` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '模型渠道',
  `model` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '音乐模型',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '标题',
  `prompt` text COMMENT '提示词',
  `custom_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自定义模式 0-不是 1-是',
  `make_instrumental` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否纯音乐 0-不是 1-是',
  `video_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '视频地址',
  `audio_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音频地址',
  `image_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音乐封面',
  `image_large_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音乐封面大图',
  `lyric` text COMMENT '歌词',
  `duration` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '时长(秒)',
  `mv` varchar(100) NOT NULL DEFAULT 'chirp-v3-0' COMMENT '版本',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-待生成 1-生成中 2-生成成功 3-生成失败',
  `tokens` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗金额',
  `fail_reason` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '失败原因',
  `ip` varchar(255) NOT NULL COMMENT '访客ip地址',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `category_id` (`style_id`) USING BTREE COMMENT '分类索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='音乐记录表';

-- ----------------------------
-- Table structure for cw_music_records_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_music_records_collect`;
CREATE TABLE `cw_music_records_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `square_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广场ID',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='音乐记录收藏';

-- ----------------------------
-- Table structure for cw_music_square
-- ----------------------------
DROP TABLE IF EXISTS `cw_music_square`;
CREATE TABLE `cw_music_square` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `operate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `source` tinyint(1) NOT NULL COMMENT '来源：1-官方；2-用户；',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '标题',
  `audio_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音频地址',
  `image_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音乐封面',
  `image_large_url` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '音乐封面大图',
  `lyric` text COMMENT '歌词',
  `duration` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '时长(秒)',
  `records_id` int(11) NOT NULL DEFAULT '0' COMMENT '分享来源ID',
  `verify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0-待审核；1-审核通过；2-审核不通过；',
  `verify_result` varchar(255) DEFAULT NULL COMMENT '审核结果',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示：1-是；0-否；',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='音乐广场';

-- ----------------------------
-- Table structure for cw_music_style
-- ----------------------------
DROP TABLE IF EXISTS `cw_music_style`;
CREATE TABLE `cw_music_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(64) NOT NULL DEFAULT '' COMMENT '英文名称',
  `image` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '图标',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='音乐风格表';

-- ----------------------------
-- Table structure for cw_music_task
-- ----------------------------
DROP TABLE IF EXISTS `cw_music_task`;
CREATE TABLE `cw_music_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '任务id',
  `style_id` varchar(100) NOT NULL DEFAULT '' COMMENT '风格id',
  `tags` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '风格',
  `channel` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '模型渠道',
  `model` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '音乐模型',
  `title` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '标题',
  `prompt` text COMMENT '提示词',
  `custom_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自定义模式 0-不是 1-是',
  `make_instrumental` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否纯音乐 0-不是 1-是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-待生成 1-生成中 2-生成成功 3-生成失败',
  `tokens` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗金额',
  `fail_reason` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '失败原因',
  `response` text CHARACTER SET utf8 COMMENT '响应数据',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `category_id` (`style_id`) USING BTREE COMMENT '分类索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='音乐任务表';

-- ----------------------------
-- Table structure for cw_notice_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_notice_record`;
CREATE TABLE `cw_notice_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收用户ID',
  `send_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送者的ID',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `scene_id` int(10) unsigned DEFAULT '0' COMMENT '场景',
  `read` tinyint(1) DEFAULT '0' COMMENT '已读状态;0-未读,1-已读',
  `recipient` tinyint(1) DEFAULT '0' COMMENT '通知接收对象类型;1-会员;2-商家;3-平台;4-游客(未注册用户)',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '通知发送类型 1-系统通知 2-短信通知 3-微信模板 4-微信小程序',
  `notice_type` tinyint(1) DEFAULT NULL COMMENT '通知类型 1-业务通知 2-验证码 3-知识库反馈 4-绘画审核 5-音乐审核 6-视频审核',
  `notice_sub_type` tinyint(1) DEFAULT NULL COMMENT '通知子类型 1-审核成功 2-审核失败',
  `extra` varchar(255) DEFAULT '' COMMENT '其他',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知记录表';

-- ----------------------------
-- Table structure for cw_notice_setting
-- ----------------------------
DROP TABLE IF EXISTS `cw_notice_setting`;
CREATE TABLE `cw_notice_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `scene_id` int(10) NOT NULL COMMENT '场景id',
  `scene_name` varchar(255) NOT NULL DEFAULT '' COMMENT '场景名称',
  `scene_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '场景描述',
  `recipient` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '接收者: [1=用户, 2=平台]',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '通知类型: 1-业务通知 2-验证码',
  `system_notice` text COMMENT '系统通知设置',
  `sms_notice` text COMMENT '短信通知设置',
  `oa_notice` text COMMENT '公众号通知设置',
  `mnp_notice` text COMMENT '小程序通知设置',
  `email_notice` text COMMENT '邮件通通知设置',
  `support` char(10) NOT NULL DEFAULT '' COMMENT '支持的发送类型 1-系统通知 2-短信通知 3-微信模板消息 4-小程序提醒',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='通知设置表';

-- ----------------------------
-- Table structure for cw_official_account_reply
-- ----------------------------
DROP TABLE IF EXISTS `cw_official_account_reply`;
CREATE TABLE `cw_official_account_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '规则名称',
  `keyword` varchar(64) NOT NULL DEFAULT '' COMMENT '关键词',
  `reply_type` tinyint(1) NOT NULL COMMENT '回复类型 1-关注回复 2-关键字回复 3-默认回复',
  `matching_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '匹配方式：1-全匹配；2-模糊匹配',
  `content_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '内容类型：1-文本；2-图文；3-超链接；',
  `content` text NOT NULL COMMENT '回复内容',
  `content_image` varchar(255) NOT NULL DEFAULT '' COMMENT '回复图片',
  `content_image_media_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信素材media_id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '启动状态：1-启动；0-关闭',
  `sort` int(10) unsigned NOT NULL DEFAULT '50' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='公众号回复';

-- ----------------------------
-- Table structure for cw_operation_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_operation_log`;
CREATE TABLE `cw_operation_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) NOT NULL COMMENT '管理员ID',
  `admin_name` varchar(16) NOT NULL DEFAULT '' COMMENT '管理员名称',
  `account` varchar(16) NOT NULL DEFAULT '' COMMENT '管理员账号',
  `action` varchar(64) DEFAULT '' COMMENT '操作名称',
  `type` varchar(8) NOT NULL COMMENT '请求方式',
  `url` varchar(255) NOT NULL COMMENT '访问链接',
  `params` text COMMENT '请求数据',
  `result` text COMMENT '请求结果',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统日志表';

-- ----------------------------
-- Table structure for cw_rechange_card_code_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_rechange_card_code_log`;
CREATE TABLE `cw_rechange_card_code_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `package_id` int(11) NOT NULL DEFAULT '0' COMMENT '套餐',
  `package_snap` text COMMENT '套餐快照',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='卡密兑换记录';

-- ----------------------------
-- Table structure for cw_recharge_order
-- ----------------------------
DROP TABLE IF EXISTS `cw_recharge_order`;
CREATE TABLE `cw_recharge_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_sn` varchar(64) NOT NULL DEFAULT '' COMMENT '订单编号',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `package_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '套餐ID',
  `pay_sn` varchar(255) NOT NULL DEFAULT '' COMMENT '支付编号-冗余字段，针对微信同一主体不同客户端支付需用不同订单号预留。',
  `pay_way` tinyint(2) NOT NULL DEFAULT '2' COMMENT '支付方式: [2=微信支付, 3=支付宝支付]',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态: [0=待支付, 1=已支付]',
  `refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态: [0=未退款, 1=已退款]',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `order_terminal` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '终端平台',
  `transaction_id` varchar(128) NOT NULL DEFAULT '' COMMENT '第三方平台交易流水号',
  `chat_balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值的对话数量',
  `robot_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值的机器人数量',
  `video_duration` int(10) NOT NULL DEFAULT '0' COMMENT '充值的视频合成时长',
  `snapshot` text COMMENT '订单快照',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='充值订单表';

-- ----------------------------
-- Table structure for cw_recharge_package
-- ----------------------------
DROP TABLE IF EXISTS `cw_recharge_package`;
CREATE TABLE `cw_recharge_package` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(250) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '套餐名称',
  `remarks` varchar(800) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '套餐描述',
  `tags` varchar(100) NOT NULL DEFAULT '' COMMENT '套餐标签',
  `sell_price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `line_price` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '划线价格',
  `chat_balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对话的余额',
  `robot_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人数量',
  `video_duration` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '视频合成时长',
  `give_chat_balance` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赠送问答的数量',
  `give_robot_number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赠送机器人数量',
  `give_video_duration` int(10) NOT NULL DEFAULT '0' COMMENT '赠送视频合成时长',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序编号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '套餐状态: [0=关闭, 1=开启]',
  `is_give` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '赠送状态: [0=关闭, 1=开启]',
  `is_recommend` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐: [0=否, 1=是]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='充值套餐表';

-- ----------------------------
-- Table structure for cw_refund_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_refund_log`;
CREATE TABLE `cw_refund_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) DEFAULT NULL COMMENT '编号',
  `record_id` int(10) NOT NULL COMMENT '退款记录id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联用户',
  `handle_id` int(10) NOT NULL DEFAULT '0' COMMENT '处理人id（管理员id）',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '本次退款金额',
  `refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态，0退款中，1退款成功，2退款失败',
  `refund_msg` text COMMENT '退款信息',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款日志表';

-- ----------------------------
-- Table structure for cw_refund_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_refund_record`;
CREATE TABLE `cw_refund_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sn` varchar(32) NOT NULL DEFAULT '' COMMENT '退款编号',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联用户',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '来源订单id',
  `order_sn` varchar(32) NOT NULL COMMENT '来源单号',
  `order_type` varchar(255) DEFAULT 'order' COMMENT '订单来源 order-商品订单 recharge-充值订单',
  `order_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总的应付款金额，冗余字段',
  `refund_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '本次退款金额',
  `transaction_id` varchar(255) DEFAULT NULL COMMENT '第三方平台交易流水号',
  `refund_way` tinyint(1) NOT NULL DEFAULT '1' COMMENT '退款方式 1-线上退款 2-线下退款',
  `refund_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '退款类型 1-后台退款',
  `refund_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '退款状态，0退款中，1退款成功，2退款失败',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='退款记录表';

-- ----------------------------
-- Table structure for cw_sensitive_word
-- ----------------------------
DROP TABLE IF EXISTS `cw_sensitive_word`;
CREATE TABLE `cw_sensitive_word` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL DEFAULT '' COMMENT '敏感词',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态值: [1=开启, 0=关闭]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='敏感词汇表';

-- ----------------------------
-- Table structure for cw_skill
-- ----------------------------
DROP TABLE IF EXISTS `cw_skill`;
CREATE TABLE `cw_skill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `image` varchar(64) NOT NULL COMMENT '图标',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `category_id` int(11) NOT NULL COMMENT '类别id',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `content` text CHARACTER SET utf8 NOT NULL COMMENT '主题内容',
  `tips` text NOT NULL COMMENT '提示内容',
  `describe` text COMMENT '描述',
  `max_tokens` int(5) unsigned NOT NULL DEFAULT '150' COMMENT '回复最大长度',
  `temperature` decimal(2,1) unsigned NOT NULL DEFAULT '0.6' COMMENT '词汇属性',
  `context_num` int(5) unsigned NOT NULL DEFAULT '2' COMMENT '上下文总数',
  `top_p` decimal(2,1) unsigned NOT NULL DEFAULT '0.9' COMMENT '随机属性',
  `presence_penalty` decimal(2,1) unsigned NOT NULL DEFAULT '0.5' COMMENT '话题属性',
  `frequency_penalty` decimal(2,1) unsigned NOT NULL DEFAULT '0.5' COMMENT '重复属性',
  `n` int(5) unsigned NOT NULL DEFAULT '1' COMMENT '最大回复',
  `system` varchar(255) DEFAULT NULL COMMENT '全局指令',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='技能';

-- ----------------------------
-- Table structure for cw_skill_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_skill_category`;
CREATE TABLE `cw_skill_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '类目名称',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `status` int(11) NOT NULL COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='技能类别';

-- ----------------------------
-- Table structure for cw_sms_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_sms_log`;
CREATE TABLE `cw_sms_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `scene_id` int(10) NOT NULL COMMENT '场景id',
  `mobile` varchar(20) NOT NULL COMMENT '手机号码',
  `content` varchar(255) NOT NULL COMMENT '发送内容',
  `code` varchar(32) DEFAULT NULL COMMENT '发送关键字（注册、找回密码）',
  `is_verify` tinyint(1) DEFAULT '0' COMMENT '是否已验证；0-否；1-是',
  `check_num` int(5) DEFAULT '0' COMMENT '验证次数',
  `send_status` tinyint(1) NOT NULL COMMENT '发送状态：0-发送中；1-发送成功；2-发送失败',
  `send_time` int(10) NOT NULL COMMENT '发送时间',
  `results` text COMMENT '短信结果',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信记录表';

-- ----------------------------
-- Table structure for cw_square_category
-- ----------------------------
DROP TABLE IF EXISTS `cw_square_category`;
CREATE TABLE `cw_square_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL COMMENT '分类名称',
  `sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态：1-开启，0-关闭',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型：1-绘画,2-音乐，3-视频',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='绘画广场分类表';

-- ----------------------------
-- Table structure for cw_system_menu
-- ----------------------------
DROP TABLE IF EXISTS `cw_system_menu`;
CREATE TABLE `cw_system_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级菜单',
  `type` char(2) NOT NULL DEFAULT '' COMMENT '权限类型: M=目录，C=菜单，A=按钮',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(100) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '菜单排序',
  `perms` varchar(100) NOT NULL DEFAULT '' COMMENT '权限标识',
  `paths` varchar(100) NOT NULL DEFAULT '' COMMENT '路由地址',
  `component` varchar(200) NOT NULL DEFAULT '' COMMENT '前端组件',
  `selected` varchar(200) NOT NULL DEFAULT '' COMMENT '选中路径',
  `params` varchar(200) NOT NULL DEFAULT '' COMMENT '路由参数',
  `is_cache` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否缓存: 0=否, 1=是',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示: 0=否, 1=是',
  `is_disable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用: 0=否, 1=是',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统菜单表';

-- ----------------------------
-- Table structure for cw_system_role
-- ----------------------------
DROP TABLE IF EXISTS `cw_system_role`;
CREATE TABLE `cw_system_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(128) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '描述',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色管理表';

-- ----------------------------
-- Table structure for cw_system_role_menu
-- ----------------------------
DROP TABLE IF EXISTS `cw_system_role_menu`;
CREATE TABLE `cw_system_role_menu` (
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '菜单ID',
  PRIMARY KEY (`role_id`,`menu_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色菜单表';

-- ----------------------------
-- Table structure for cw_task_invite
-- ----------------------------
DROP TABLE IF EXISTS `cw_task_invite`;
CREATE TABLE `cw_task_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '邀请人id',
  `new_user_id` int(11) DEFAULT NULL COMMENT '新用户ID',
  `task_share_id` int(11) DEFAULT NULL COMMENT '分享链接ID',
  `balance` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '奖励电力值',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请记录表';

-- ----------------------------
-- Table structure for cw_task_share
-- ----------------------------
DROP TABLE IF EXISTS `cw_task_share`;
CREATE TABLE `cw_task_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `channel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分享渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `click_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `balance` decimal(15,7) DEFAULT '0.0000000',
  `invite_num` int(10) NOT NULL DEFAULT '0' COMMENT '邀请人数',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分享记录表';

-- ----------------------------
-- Table structure for cw_task_sign
-- ----------------------------
DROP TABLE IF EXISTS `cw_task_sign`;
CREATE TABLE `cw_task_sign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `channel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '签到渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `balance` decimal(15,7) DEFAULT NULL COMMENT '电力值',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到记录列表';

-- ----------------------------
-- Table structure for cw_user
-- ----------------------------
DROP TABLE IF EXISTS `cw_user`;
CREATE TABLE `cw_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_ids` varchar(500) NOT NULL DEFAULT '' COMMENT '分组',
  `sn` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编号',
  `avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
  `real_name` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `is_distribution` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否分销商: [1-是, 0-否]',
  `account` varchar(32) NOT NULL DEFAULT '' COMMENT '用户账号',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `mobile` varchar(32) NOT NULL DEFAULT '' COMMENT '用户电话',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户性别: [1=男, 2=女]',
  `channel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '注册渠道: [1=微信小程序, 2=微信公众号, 3=手机H5, 4=电脑PC, 5=苹果APP, 6=安卓APP]',
  `balance` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '钱包余额',
  `robot_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人数',
  `video_num` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '视频时长',
  `total_chat` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计对话',
  `total_space` int(10) NOT NULL DEFAULT '-1' COMMENT '总的空间: -1不限制',
  `is_disable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否禁用: [0=否, 1=是]',
  `is_blacklist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否加入黑名单: [1=是, 0=否]',
  `is_new_user` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是新注册用户: [1-是, 0-否]',
  `multipoint_login` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '多处登录: [0=否, 1=是]',
  `cancelled_remark` varchar(500) NOT NULL DEFAULT '' COMMENT '注销原因',
  `login_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `user_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '可提现佣金',
  `total_user_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计总佣金',
  `distribution_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分销状态：1-正常；0-冻结',
  `distribution_time` int(10) DEFAULT NULL COMMENT '成为分销商的时间',
  `first_leader` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '第一个上级',
  `inviter_id` int(11) NOT NULL DEFAULT '0' COMMENT '邀请人id',
  `second_leader` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '第二个上级',
  `total_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '累计消费金额',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sn` (`sn`) USING BTREE COMMENT '编号唯一',
  UNIQUE KEY `account` (`account`) USING BTREE COMMENT '账号唯一'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户管理表';

-- ----------------------------
-- Table structure for cw_user_account_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_user_account_log`;
CREATE TABLE `cw_user_account_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) NOT NULL DEFAULT '' COMMENT '流水号',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '管理ID',
  `action` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作动作: [1=增加, 2=减少]',
  `change_object` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '变动对象: [1=钱包, 2=机器人, 3=数字人]',
  `change_type` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '变动类型',
  `change_amount` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '变动的数量',
  `left_amount` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '变动后数量',
  `robot_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '机器人的ID',
  `robot_name` varchar(200) NOT NULL COMMENT '机器人名称',
  `source_sn` varchar(200) NOT NULL DEFAULT '' COMMENT '关联的单号',
  `remark` varchar(300) NOT NULL DEFAULT '' COMMENT '备注的信息',
  `extend` varchar(500) NOT NULL DEFAULT '' COMMENT '扩展的信息',
  `extra` text COMMENT '预留的字段',
  `flows` text COMMENT 'token信息',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='账户流水表';

-- ----------------------------
-- Table structure for cw_user_auth
-- ----------------------------
DROP TABLE IF EXISTS `cw_user_auth`;
CREATE TABLE `cw_user_auth` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `openid` varchar(128) NOT NULL COMMENT '微信openid',
  `unionid` varchar(128) NOT NULL DEFAULT '' COMMENT '微信unionid',
  `terminal` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '客户端类型: [1=微信小程序, 2=微信公众号, 3=手机H5, 4=电脑PC, 5=苹果APP, 6=安卓APP]',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `openid` (`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户授权表';

-- ----------------------------
-- Table structure for cw_user_group
-- ----------------------------
DROP TABLE IF EXISTS `cw_user_group`;
CREATE TABLE `cw_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(200) NOT NULL DEFAULT '' COMMENT '名称',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户分组表';

-- ----------------------------
-- Table structure for cw_user_member
-- ----------------------------
DROP TABLE IF EXISTS `cw_user_member`;
CREATE TABLE `cw_user_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `package_name` varchar(255) NOT NULL COMMENT '套餐名称',
  `member_end_time` bigint(20) DEFAULT NULL COMMENT '到期时间',
  `is_perpetual` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否永久套餐：1-是，0-否',
  `package_id` int(11) NOT NULL COMMENT '套餐id',
  `package_price_id` int(11) NOT NULL COMMENT '套餐价格id',
  `package_info` text COMMENT '套餐信息',
  `continue_time` int(10) DEFAULT NULL COMMENT '继会员时间',
  `is_clear` int(11) NOT NULL DEFAULT '0' COMMENT '是否标记清除:1-是,0-否',
  `clear_time` int(11) DEFAULT NULL COMMENT '标记清除时间',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员开通记录表';

-- ----------------------------
-- Table structure for cw_user_session
-- ----------------------------
DROP TABLE IF EXISTS `cw_user_session`;
CREATE TABLE `cw_user_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) NOT NULL COMMENT '用户ID',
  `terminal` tinyint(1) NOT NULL DEFAULT '1' COMMENT '客户端类型: [1=微信小程序, 2=微信公众号, 3=手机H5, 4=电脑PC, 5=苹果APP, 6=安卓APP]',
  `token` varchar(32) NOT NULL DEFAULT '' COMMENT '令牌的值',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `token` (`token`) USING BTREE COMMENT 'token是唯一的'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户会话表';

-- ----------------------------
-- Table structure for cw_video_record
-- ----------------------------
DROP TABLE IF EXISTS `cw_video_record`;
CREATE TABLE `cw_video_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `task_id` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '任务id',
  `style_id` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '风格id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-文生视频 2-图生视频',
  `tags` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '风格',
  `channel` varchar(64) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '模型渠道',
  `model` varchar(128) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '视频模型',
  `prompt` varchar(1000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '提示词',
  `image` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '垫图地址',
  `video_url` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '视频地址',
  `api_version` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '版本',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-待生成 1-生成中 2-生成成功 3-生成失败',
  `tokens` decimal(15,7) unsigned NOT NULL DEFAULT '0.0000000' COMMENT '消耗金额',
  `scale` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '图片比例',
  `fail_reason` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '失败原因',
  `response` text CHARACTER SET utf8mb4 COMMENT '响应数据',
  `ip` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '访客ip地址',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`user_id`) USING BTREE COMMENT '用户索引',
  KEY `category_id` (`style_id`) USING BTREE COMMENT '分类索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cw_video_records_collect
-- ----------------------------
DROP TABLE IF EXISTS `cw_video_records_collect`;
CREATE TABLE `cw_video_records_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `square_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广场ID',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='视频记录收藏表';

-- ----------------------------
-- Table structure for cw_video_square
-- ----------------------------
DROP TABLE IF EXISTS `cw_video_square`;
CREATE TABLE `cw_video_square` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `operate_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `source` tinyint(1) NOT NULL COMMENT '来源：1-官方；2-用户；',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1-文生视频 2-图生视频',
  `records_id` int(11) NOT NULL DEFAULT '0' COMMENT '分享来源ID',
  `prompt` varchar(1000) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '提示词',
  `image` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '垫图地址',
  `video_url` varchar(255) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '视频地址',
  `verify_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态：0-待审核；1-审核通过；2-审核不通过；',
  `verify_result` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '审核结果',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示：1-是；0-否；',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_idx` (`operate_id`) USING BTREE COMMENT '用户索引',
  KEY `category_id` (`category_id`) USING BTREE COMMENT '分类索引'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='视频广场';

-- ----------------------------
-- Table structure for cw_video_style
-- ----------------------------
DROP TABLE IF EXISTS `cw_video_style`;
CREATE TABLE `cw_video_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(64) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(64) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '英文名称',
  `image` varchar(64) CHARACTER SET utf8mb4 NOT NULL COMMENT '图标',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态：1-开启，0-关闭',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='视频风格表';

-- ----------------------------
-- Table structure for cw_visitor
-- ----------------------------
DROP TABLE IF EXISTS `cw_visitor`;
CREATE TABLE `cw_visitor` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(255) NOT NULL COMMENT '访客ip地址',
  `terminal` tinyint(1) NOT NULL COMMENT '访问终端',
  `visit` int(11) NOT NULL COMMENT '浏览量',
  `create_time` int(10) DEFAULT NULL COMMENT '访问时间',
  `update_time` int(10) DEFAULT NULL,
  `delete_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访问日志表';

-- ----------------------------
-- Table structure for cw_withdraw_apply
-- ----------------------------
DROP TABLE IF EXISTS `cw_withdraw_apply`;
CREATE TABLE `cw_withdraw_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(25) NOT NULL COMMENT '提现单号',
  `batch_no` varchar(25) DEFAULT NULL COMMENT '商家批次单号',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现方式：1-支付宝；2-微信零钱；3-微信收款码；4-支付宝收款码；',
  `money` decimal(10,2) unsigned NOT NULL COMMENT '提现金额',
  `left_money` decimal(10,2) unsigned NOT NULL COMMENT '到账金额(扣除手续费)',
  `handling_fee` decimal(10,2) unsigned NOT NULL COMMENT '手续费',
  `real_name` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `account` varchar(32) DEFAULT '' COMMENT '提现账号',
  `money_qr_code` varchar(128) DEFAULT '' COMMENT '收款二维码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现状态：1-待审核；2-提现中；3-提现成功；4-提现失败；',
  `verify_status` tinyint(1) DEFAULT '1' COMMENT '审核状态：1-待审核；2-审核通过；3-审核拒绝；',
  `verify_remark` varchar(255) DEFAULT NULL COMMENT '审核备注',
  `verify_time` int(10) DEFAULT NULL COMMENT '审核时间',
  `transfer_remark` varchar(255) DEFAULT NULL COMMENT '转账备注',
  `transfer_result` text COMMENT '转账结果',
  `query_result` text COMMENT '查询结果',
  `finish_time` int(10) DEFAULT NULL COMMENT '完成时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提现申请表';

-- ----------------------------
-- Table structure for cw_works_share_log
-- ----------------------------
DROP TABLE IF EXISTS `cw_works_share_log`;
CREATE TABLE `cw_works_share_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `work_id` int(10) unsigned NOT NULL COMMENT '作品ID',
  `balance` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示: [0=否， 1=是]',
  `channel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '分享渠道: [1-微信小程序 2-微信公众号 3-手机H5 4-电脑PC 5-苹果APP 6-安卓APP]',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型：1-绘画，2-音乐，3-视频',
  `square_id` int(10) NOT NULL COMMENT '广场id',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------
-- INSTER
-- ----------------------------
BEGIN;
INSERT INTO `cw_dept` VALUES (1, '公司', 0, 0, '小明', '13800138000', 1, 1657074108, 1657074108, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_dev_crontab` VALUES (1, '内容审核', 1, 0, '', 'content_censor', '', 1, '*/10 * * * *', '', 1720173626, '0', '11.62', 1692673402, 1698833565, NULL);
INSERT INTO `cw_dev_crontab` VALUES (2, '订单退款状态处理', 1, 0, '', 'query_refund', '', 1, '* * * * *', '', 1720174101, '0.01', '1.55', 1697513110, 1720087164, NULL);
INSERT INTO `cw_dev_crontab` VALUES (3, 'AI音乐结果查询', 1, 0, '', 'query_music', '', 1, '* * * * *', '', 1720174101, '0.01', '199.1', 1717051229, 1719216511, NULL);
INSERT INTO `cw_dev_crontab` VALUES (4, '绘画超时处理', 1, 0, '', 'draw_fail', '', 1, '* * * * *', '', 1718867863, '0', '0.08', 1718790621, 1718790621, NULL);
INSERT INTO `cw_dev_crontab` VALUES (5, '商家转账到零钱查询', 1, 0, '', 'wechat_merchant_transfer', '', 1, '* * * * *', '', 1720174101, '0', '0.23', 1719902331, 1719902331, NULL);
INSERT INTO `cw_dev_crontab` VALUES (6, 'AI视频结果查询', 1, 0, '', 'query_video', '', 1, '* * * * *', '', 1720174101, '0', '2.61', 1719994863, 1720001150, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_decorate_page` VALUES (1, 1, 'PC主页装修', '[{\"id\":\"lpqkej7ja9o4c\",\"title\":\"导航设置\",\"name\":\"header\",\"isShow\":\"1\",\"prop\":{\"nav\":[{\"name\":\"首页\",\"link\":{\"name\":\"首页\",\"path\":\"/\",\"type\":\"shop\"},\"isShow\":true,\"unselected\":\"resource/image/decorate/tab_home.png\",\"selected\":\"resource/image/decorate/tab_home_on.png\"},{\"link\":{\"name\":\"AI对话\",\"path\":\"/dialogue/chat\",\"type\":\"shop\",\"isTab\":true},\"name\":\"问答\",\"isShow\":true,\"unselected\":\"resource/image/decorate/tab_chat.png\",\"selected\":\"resource/image/decorate/tab_chat_on.png\"},{\"name\":\"创作\",\"selected\":\"resource/image/decorate/tab_industry_on.png\",\"unselected\":\"resource/image/decorate/tab_industry.png\",\"isShow\":true,\"link\":{\"name\":\"AI创作\",\"path\":\"/creation\",\"type\":\"shop\",\"isTab\":true}},{\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":true},\"name\":\"智能体\",\"isShow\":true,\"unselected\":\"resource/image/decorate/tab_robot.png\",\"selected\":\"resource/image/decorate/tab_robot_on.png\"},{\"link\":{\"name\":\"智能体广场\",\"path\":\"/robot_square\",\"type\":\"shop\",\"isTab\":true},\"name\":\"广场\",\"isShow\":true,\"unselected\":\"resource/image/decorate/tab_square.png\",\"selected\":\"resource/image/decorate/tab_square_on.png\"},{\"name\":\"绘画\",\"selected\":\"\",\"unselected\":\"\",\"isShow\":true,\"link\":{\"name\":\"AI绘画\",\"path\":\"/draw/sd\",\"type\":\"shop\",\"isTab\":true}},{\"name\":\"音乐\",\"selected\":\"\",\"unselected\":\"\",\"isShow\":true,\"link\":{\"name\":\"AI音乐\",\"path\":\"/music\",\"type\":\"shop\",\"isTab\":true}},{\"name\":\"视频\",\"selected\":\"\",\"unselected\":\"\",\"isShow\":true,\"link\":{\"name\":\"AI视频\",\"path\":\"/video\",\"type\":\"shop\",\"isTab\":true}}],\"isShowAccount\":\"1\",\"isShowIcon\":true}},{\"id\":\"lpqoaziaj0gt6\",\"title\":\"标题设置\",\"name\":\"title\",\"isShow\":true,\"prop\":{\"isShowBtn\":true,\"title\":\"chatmoney\",\"desc\":\"ChatWork智能知识库可以进行智能体角色设定、知识库训练、发布/分享智能体，同时还带有AI智能对话，可以设定多种AI行业场景等强大的功能，拥有强大的第三方对接能力。适用于企业智能客服、企业智能文档、专家顾问助理等多种企业级商业场景，具有较大的商业使用价值！\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":\"\"},\"btnText\":\"免费创建智能体\",\"mobileLink\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":\"1\",\"isTab\":\"\"},\"bgImage\":\"resource/image/decorate/home_bg_001.png\",\"rightImage\":\"resource/image/decorate/home_bg_002.png\"}},{\"id\":\"lpqoaziayw7qq\",\"title\":\"应用场景\",\"name\":\"entrance\",\"isShow\":true,\"prop\":{\"data\":[{\"icon\":\"resource/image/decorate/home_scene_001.png\",\"title\":\"企业智能客服\",\"link\":{\"name\":\"首页\",\"path\":\"/\",\"type\":\"shop\",\"isTab\":\"\"},\"desc\":\"企业可以上传产品资料、FAQ手册等信息，完成训练后，对外发布智能客服聊天窗口。通过AI客服可以提供24小时在线客服支持，节省人力物力。\",\"btnText\":\"开始提问\",\"isShow\":true,\"mobileLink\":{\"name\":\"AI对话\",\"path\":\"/pages/dialogue/dialogue\",\"type\":\"shop\"}},{\"icon\":\"resource/image/decorate/home_scene_002.png\",\"title\":\"企业智能文档\",\"link\":{\"name\":\"AI行业\",\"path\":\"/creation\",\"type\":\"shop\",\"isTab\":\"\"},\"desc\":\"企业可以上传产品文档，合同内容等信息，完成训练后，仅限内部员工登录访问。通过AI助手，可以快速、准确的查询企业内部有关的信息文档，增强企业内部信息流动性。\",\"btnText\":\"开始创作\",\"isShow\":true,\"mobileLink\":{\"name\":\"AI行业\",\"path\":\"/pages/ai_creation/ai_creation\",\"type\":\"shop\"}},{\"icon\":\"resource/image/decorate/home_scene_003.png\",\"title\":\"专家顾问助理\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":\"\"},\"desc\":\"专家学者，大V博主，可以上传自己的语录资料，出版书籍等文本信息，打造24小时虚拟数字分身。例如健身顾问、心理咨询师通过导入专业资料后，可以24小时对外提供在线服务，带来额外的服务收入。\",\"btnText\":\"开始使用\",\"isShow\":true,\"mobileLink\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":\"1\",\"isTab\":\"\"}},{\"icon\":\"resource/image/decorate/home_scene_004.png\",\"title\":\"AI角色分身\",\"desc\":\"专家学者，大V博主，可以上传自己的语录资料，出版书籍等文本信息，打造24小时虚拟数字分身。例如健身顾问、心理咨询师通过导入专业资料后，可以24小时对外提供在线服务，带来额外的服务收入。\",\"isShow\":false,\"link\":{\"name\":\"智能体广场\",\"path\":\"/robot_square\",\"type\":\"shop\",\"isTab\":\"\"},\"mobileLink\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":\"1\",\"isTab\":\"\"}}],\"showType\":3}},{\"id\":\"lromj6lu40tqu\",\"title\":\"功能介绍\",\"name\":\"intro\",\"isShow\":true,\"prop\":{\"data\":[{\"image\":\"resource/image/decorate/home_fun_001.png\",\"title\":\"AI企业知识库-角色设定\",\"subtitle\":\"给智能体设定一个基础角色，可以是客服、销售、培训师、营销人员、行业专家等，让智能体更符合您的业务形象。\",\"functionPoint\":[{\"text\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\"},{\"text\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\"}],\"isShow\":true,\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":false},\"mobileLink\":{\"name\":\"AI行业\",\"path\":\"/pages/ai_creation/ai_creation\",\"type\":\"shop\",\"canTab\":\"1\",\"isTab\":\"\"}},{\"image\":\"resource/image/decorate/home_fun_002.png\",\"title\":\"训练智能体-打造私人专属客服\",\"subtitle\":\"通过录入文档或问答来创建知识库，让智能体学习，根据智能体训练情况，可随时删除或替换文档。\",\"functionPoint\":[{\"text\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\"},{\"text\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\"},{\"text\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\"}],\"isShow\":true,\"desc\":\"支持AI模型打造的智能问答，突破信息的界限，启发创意的火花\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\"},\"mobileLink\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\"}},{\"image\":\"resource/image/decorate/home_fun_003.png\",\"title\":\"数字人-多种形象切换对话\",\"subtitle\":\"多种形象对话，打造具有独特魅力和创新性的数字人，对话广场更加灵活。\",\"functionPoint\":[],\"isShow\":true,\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":false}}]}},{\"id\":\"lrornmce3z8u3\",\"title\":\"引导设置\",\"name\":\"guide\",\"isShow\":true,\"prop\":{\"bgImage\":\"resource/image/decorate/home_footer_bg.png\",\"content\":\"chatmoney，您的信赖之选\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\"},\"isShowBtn\":\"1\",\"btnText\":\"立即体验1\",\"mobileLink\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":\"1\",\"isTab\":\"\"},\"rightQrcodeShow1\":1,\"rightQrcodeShow2\":1,\"logoImage\":\"resource/image/decorate/home_footer_logo.png\",\"isShowLeft\":1,\"column1\":\"公司优势\",\"column2\":\"产品优势\",\"rightQrcode1\":\"resource/image/decorate/home_footer_logo.png\",\"rightQrcode2\":\"resource/image/decorate/home_footer_qr.png\",\"rightQrcodeTitle1\":\"官方客服二维码\",\"rightQrcodeTitle2\":\"官方公众号\",\"columnMenu1\":[{\"title\":\"深耕多年\",\"isShow\":true,\"link\":{\"name\":\"AI对话\",\"path\":\"/dialogue/layout/chat\",\"type\":\"shop\",\"isTab\":\"\"}},{\"title\":\"技术强大\",\"isShow\":true},{\"title\":\"专业服务\",\"isShow\":true},{\"title\":\"紧跟行业\",\"isShow\":true},{\"title\":\"生态体系\",\"isShow\":true}],\"columnMenu2\":[{\"title\":\"系统免费升级\",\"isShow\":true},{\"title\":\"提供推广方案\",\"isShow\":true},{\"title\":\"支付多个接口\",\"isShow\":true},{\"title\":\"系统安全文档\",\"isShow\":true},{\"title\":\"训练模型加持\",\"isShow\":true}]}}]', 1661757188, 1718875479);
INSERT INTO `cw_decorate_page` VALUES (2, 2, 'H5个人中心', '[{\"id\":\"lt5mjgy5dx64q\",\"title\":\"用户信息\",\"name\":\"user-info\",\"disabled\":1,\"content\":{},\"styles\":{}},{\"title\":\"vip会员\",\"name\":\"open-vip\",\"content\":{\"icon\":\"resource/image/decorate/me_vip_icon.png\",\"bg\":\"resource/image/decorate/me_vip_bg.png\",\"title\":\"开通会员\",\"sub_title\":\"解锁更多专属功能~\",\"btn\":\"开通会员\"},\"styles\":{}},{\"id\":\"lt5mjgy5cke53\",\"title\":\"用户余额\",\"name\":\"user-balance\",\"disabled\":1,\"content\":{\"enabled\":0,\"data\":[]},\"styles\":{}},{\"id\":\"lt5mjgy5p2i6v\",\"title\":\"我的服务\",\"name\":\"my-service\",\"content\":{\"style\":1,\"title\":\"我的服务\",\"showTitle\":true,\"data\":[{\"image\":\"resource/image/decorate/me_czzx.png\",\"name\":\"充值中心\",\"link\":{\"name\":\"充值中心\",\"path\":\"/packages/pages/recharge/recharge\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false}},{\"image\":\"resource/image/decorate/me_help.png\",\"name\":\"会员中心\",\"link\":{\"name\":\"会员中心\",\"path\":\"/packages/pages/member_center/member_center\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_fxtk.png\",\"name\":\"分销推广\",\"link\":{\"path\":\"/packages/pages/promotion_center/promotion_center\",\"name\":\"推广中心\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false}},{\"image\":\"resource/image/decorate/me_kmdh.png\",\"name\":\"卡密兑换\",\"link\":{\"path\":\"/packages/pages/redeem_code/redeem_code\",\"name\":\"卡密兑换\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false}},{\"image\":\"resource/image/decorate/me_gmjl.png\",\"name\":\"购买记录\",\"link\":{\"name\":\"购买记录\",\"path\":\"/packages/pages/buy_record/buy_record\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_zhmx.png\",\"name\":\"余额明细\",\"link\":{\"name\":\"使用明细\",\"path\":\"/packages/pages/use_list/use_list\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_zp.png\",\"name\":\"我的作品\",\"link\":{\"name\":\"我的作品\",\"path\":\"/packages/pages/user_works/user_works\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_yqhb.png\",\"name\":\"邀请海报\",\"link\":{\"path\":\"/packages/pages/invite_poster/invite_poster\",\"name\":\"邀请海报\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_grzx.png\",\"name\":\"个人信息\",\"link\":{\"name\":\"个人信息\",\"path\":\"/packages/pages/user_set/user_set\",\"type\":\"shop\",\"canTab\":false,\"isTab\":false}},{\"image\":\"resource/image/decorate/me_lxkf.png\",\"name\":\"联系客服\",\"link\":{\"name\":\"联系客服\",\"path\":\"/packages/pages/customer_service/customer_service\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_gywm.png\",\"name\":\"关于我们\",\"link\":{\"name\":\"关于我们\",\"path\":\"/packages/pages/as_us/as_us\",\"type\":\"shop\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_help.png\",\"name\":\"帮助文档\",\"link\":{\"path\":\"https://zv6iglmyl7h.feishu.cn/docx/KBfgd1t5VoYGApxuautcTE3Wnkg\",\"type\":\"custom\",\"isTab\":false}},{\"image\":\"resource/image/decorate/me_xxtz.png\",\"name\":\"消息通知\",\"link\":{\"path\":\"/packages/pages/notification/notification\",\"name\":\"消息通知\",\"type\":\"shop\",\"isTab\":false}}]},\"styles\":{}},{\"id\":\"lt5mjgy59js4k\",\"title\":\"个人中心广告图\",\"name\":\"user-banner\",\"content\":{\"enabled\":1,\"data\":[{\"image\":\"resource/image/decorate/me_ad.png\",\"name\":\"AI\",\"link\":{\"name\":\"首页\",\"path\":\"/pages/index/index\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false}}]},\"styles\":{}},{\"id\":\"lt5mjgy5ex46e\",\"title\":\"个人中心广告图\",\"name\":\"user-bottom\",\"content\":{\"enabled\":1,\"data\":{\"title\":\"百度：\",\"content\":\"www.baidu.com\",\"canCopy\":1}},\"styles\":{}}]', 1661757188, 1716977596);
INSERT INTO `cw_decorate_page` VALUES (3, 3, 'H5客服设置', '[{\"id\":\"lt5mjgy5thu1x\",\"title\":\"客服设置\",\"name\":\"customer-service\",\"content\":{\"title\":\"添加客服二维码\",\"time\":\"早上9:00-19:00\",\"mobile\":\"13800138000\",\"qrcode\":\"resource/image/decorate/qr.png\"},\"styles\":{}}]', 1661757188, 1710469224);
INSERT INTO `cw_decorate_page` VALUES (4, 4, 'H5导航菜单', '{\"style\":{\"default_color\":\"\",\"selected_color\":\"\"},\"list\":[{\"name\":\"首页\",\"selected\":\"resource/image/decorate/h5_tab_home_on.png\",\"unselected\":\"resource/image/decorate/h5_tab_home.png\",\"is_show\":\"1\",\"link\":{\"name\":\"首页\",\"path\":\"/pages/index/index\",\"type\":\"shop\"}},{\"name\":\"智能体\",\"selected\":\"resource/image/decorate/h5_tab_robot_on.png\",\"unselected\":\"resource/image/decorate/h5_tab_robot.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":true,\"isTab\":true}},{\"name\":\"AI绘画\",\"selected\":\"resource/image/decorate/h5_tab_draw_on.png\",\"unselected\":\"resource/image/decorate/h5_tab_draw.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI绘画\",\"path\":\"/packages/pages/draw/draw\",\"type\":\"shop\",\"canTab\":true,\"isTab\":true}},{\"name\":\"广场\",\"selected\":\"resource/image/decorate/h5_tab_square_on.png\",\"unselected\":\"resource/image/decorate/h5_tab_square.png\",\"is_show\":\"1\",\"link\":{\"name\":\"智能体广场\",\"path\":\"/packages/pages/robot_square/robot_square\",\"type\":\"shop\",\"canTab\":true,\"isTab\":true}},{\"name\":\"我的\",\"selected\":\"resource/image/decorate/h5_tab_me_on.png\",\"unselected\":\"resource/image/decorate/h5_tab_me.png\",\"is_show\":\"1\",\"link\":{\"name\":\"个人中心\",\"path\":\"/pages/user/user\",\"type\":\"shop\",\"canTab\":true,\"isTab\":true}}]}', 1661757188, 1722413776);
INSERT INTO `cw_decorate_page` VALUES (5, 5, 'AI创作应用', '[{\"id\":\"mj3k4jfi3\",\"title\":\"背景设置\",\"name\":\"ai-create\",\"isShow\":true,\"prop\":{\"banner_bg\":\"resource/image/decorate/creation_bg.png\",\"title\":\"AI创作应用\",\"title_color\":2}}]', 1661757188, 1714011227);
INSERT INTO `cw_decorate_page` VALUES (6, 6, '智能体广场', '[{\"id\":\"fgjrifj45j\",\"title\":\"背景设置\",\"name\":\"ai-robot\",\"isShow\":true,\"prop\":{\"banner_bg\":\"resource/image/decorate/square_bg.png\",\"title\":\"海量应用等你来发现\",\"title_color\":3}}]', 1661757188, 1714011231);
INSERT INTO `cw_decorate_page` VALUES (7, 7, 'H5页面装修', '[{\"id\":\"lveon6o9iuu7f\",\"title\":\"顶部导航\",\"name\":\"index-title\",\"content\":[],\"styles\":[],\"isHidden\":false},{\"id\":\"lveon6o92358l\",\"title\":\"轮播图\",\"name\":\"index-banner\",\"content\":{\"data\":[{\"image\":\"resource/image/decorate/h5_home_banner_001.png\",\"name\":\"\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_banner_002.png\",\"name\":\"\",\"link\":{\"name\":\"AI对话\",\"path\":\"/pages/dialogue/dialogue\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true}]},\"styles\":[],\"isHidden\":false},{\"id\":\"lveon6o9qpki7\",\"title\":\"广告位\",\"name\":\"index-ad\",\"content\":{\"data\":[{\"image\":\"resource/image/decorate/h5_home_ad_001.png\",\"desc\":\"AI自动学习\",\"title\":\"AI知识库\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_ad_002.png\",\"desc\":\"快速生成音乐\",\"title\":\"AI音乐\",\"link\":{\"name\":\"AI音乐\",\"path\":\"/packages/pages/music/music\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_ad_003.png\",\"desc\":\"复杂概念快速呈现\",\"title\":\"思维导图\",\"link\":{\"path\":\"/packages/pages/mind_map/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true}],\"showType\":3},\"styles\":[],\"isHidden\":false},{\"id\":\"lveon6o9yplwr\",\"title\":\"功能菜单\",\"name\":\"index-menu\",\"content\":{\"data\":[{\"image\":\"resource/image/decorate/h5_home_fun_zsk.png\",\"title\":\"AI知识库\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/pages/kb/kb\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_wd.png\",\"title\":\"AI问答\",\"link\":{\"name\":\"AI对话\",\"path\":\"/packages/pages/dialogue/dialogue\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_jneng.png\",\"title\":\"AI技能\",\"link\":{\"name\":\"AI行业\",\"path\":\"/pages/ai_creation/ai_creation\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_jqrgc.png\",\"title\":\"智能体广场\",\"link\":{\"name\":\"智能体广场\",\"path\":\"/pages/robot_square/robot_square\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_fyzs.png\",\"title\":\"翻译助手\",\"link\":{\"path\":\"/packages/pages/create/create?id=3\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_gzzj.png\",\"title\":\"工作总结\",\"link\":{\"path\":\"/packages/pages/create/create?id=2\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_zbrb.png\",\"title\":\"周报日报\",\"link\":{\"path\":\"/packages/pages/create/create?id=1\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_cpdq.png\",\"title\":\"菜谱大全\",\"link\":{\"path\":\"/packages/pages/create/create?id=21\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_dytj.png\",\"title\":\"电影推荐\",\"link\":{\"path\":\"/packages/pages/create/create?id=23\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_ghzx.png\",\"title\":\"挂号咨询\",\"link\":{\"path\":\"/packages/pages/create/create?id=16\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_lwzl.png\",\"title\":\"论文资料\",\"link\":{\"path\":\"/packages/pages/create/create?id=12\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_ggwa.png\",\"title\":\"广告文案\",\"link\":{\"path\":\"/packages/pages/create/create?id=22\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_gxqm.png\",\"title\":\"个性签名\",\"link\":{\"path\":\"/packages/pages/create/create?id=9\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true},{\"image\":\"resource/image/decorate/h5_home_fun_lyjh.png\",\"title\":\"旅游计划\",\"link\":{\"path\":\"/packages/pages/create/create?id=9\",\"type\":\"custom\",\"isTab\":false},\"isShow\":true}]},\"styles\":[],\"isHidden\":false},{\"id\":\"lveon6o92hjnk\",\"title\":\"热门创作\",\"name\":\"index-hot\",\"content\":{\"title\":\"热门创作\",\"dataType\":1,\"dataNum\":20,\"data\":[],\"showType\":2},\"styles\":[],\"isHidden\":false}]', 1661757188, 1719560518);
INSERT INTO `cw_decorate_page` VALUES (8, 8, 'PC侧边导航', '{\"nav\":[{\"name\":\"问答\",\"selected\":\"resource/image/decorate/tab_chat_on.png\",\"unselected\":\"resource/image/decorate/tab_chat.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI对话\",\"path\":\"/dialogue/chat\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"创作\",\"selected\":\"resource/image/decorate/tab_industry_on.png\",\"unselected\":\"resource/image/decorate/tab_industry.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI行业\",\"path\":\"/creation\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"智能体\",\"selected\":\"resource/image/decorate/tab_robot_on.png\",\"unselected\":\"resource/image/decorate/tab_robot.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI智能体\",\"path\":\"/application/layout/robot\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"广场\",\"selected\":\"resource/image/decorate/tab_square_on.png\",\"unselected\":\"resource/image/decorate/tab_square.png\",\"is_show\":\"1\",\"link\":{\"name\":\"机器人广场\",\"path\":\"/robot_square\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"AI搜索\",\"selected\":\"resource/image/decorate/tab_search_on.png\",\"unselected\":\"resource/image/decorate/tab_search.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI搜索\",\"path\":\"/search\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"SD绘画\",\"selected\":\"resource/image/decorate/tab_draw_on.png\",\"unselected\":\"resource/image/decorate/tab_draw.png\",\"is_show\":\"1\",\"link\":{\"name\":\"绘画\",\"path\":\"/draw/sd\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"DALLE绘画\",\"selected\":\"resource/image/decorate/tab_draw_dalle_on.png\",\"unselected\":\"resource/image/decorate/tab_draw_dalle.png\",\"is_show\":\"1\",\"link\":{\"name\":\"DALLE绘画\",\"path\":\"/draw/dalle\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"音乐\",\"selected\":\"resource/image/decorate/tab_music_on.png\",\"unselected\":\"resource/image/decorate/tab_music.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI音乐\",\"path\":\"/music\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"视频\",\"selected\":\"resource/image/decorate/tab_video_on.png\",\"unselected\":\"resource/image/decorate/tab_video.png\",\"is_show\":\"1\",\"link\":{\"name\":\"AI视频\",\"path\":\"/video\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"导图\",\"selected\":\"resource/image/decorate/tab_swdt_on.png\",\"unselected\":\"resource/image/decorate/tab_swdt.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"首页\",\"selected\":\"resource/image/decorate/tab_home_on.png\",\"unselected\":\"resource/image/decorate/tab_home.png\",\"is_show\":\"1\",\"link\":{\"name\":\"首页\",\"path\":\"/\",\"type\":\"shop\",\"isTab\":false}},{\"name\":\"应用\",\"selected\":\"resource/image/decorate/tab_apps_on.png\",\"unselected\":\"resource/image/decorate/tab_apps.png\",\"is_show\":\"1\",\"link\":{\"path\":\"/app_center\",\"name\":\"应用中心\",\"type\":\"shop\",\"isTab\":false}}],\"menu\":[{\"name\":\"帮助\",\"selected\":\"\",\"unselected\":\"resource/image/decorate/tab_help.png\",\"is_show\":\"1\",\"link\":{\"path\":\"https://zv6iglmyl7h.feishu.cn/wiki/M1EuwXLr0i0llFkhsFkcWQT5nKe?from=from_copylink\",\"type\":\"custom\",\"isTab\":false},\"showName\":true}],\"showNavIcon\":true}', 1661757188, 1717731751);
INSERT INTO `cw_decorate_page` VALUES (9, 9, '邀请海报', '[{\"id\":\"lhzliq33nbu2y\",\"title\":\"邀请海报\",\"name\":\"invite-poster\",\"content\":{\"default\":1,\"defaultUrl1\":\"/resource/image/adminapi/default/invite_backdend01.png\",\"defaultUrl2\":\"/resource/image/adminapi/default/invite_backdend02.png\",\"defaultQrcode\":\"/resource/image/adminapi/default/qrcode.png\",\"poster\":1,\"posterUrl\":\"/resource/image/adminapi/default/invite_backdend01.png\",\"data\":{\"content\":\"扫码体验\",\"x\":106,\"y\":399},\"showData\":1,\"code\":{\"x\":86,\"y\":279}},\"styles\":{}},{\"id\":\"lhzliq33onwgd\",\"title\":\"规则说明\",\"name\":\"invite-rule\",\"disabled\":0,\"content\":{\"enabled\":1,\"name\":\"邀请说明\",\"data\":\"好友通过您分享的邀请分销海报注册登录后，Ta将永久成为您的下级分销商，未来Ta产生的订单佣金您都有奖励，会自动计入您的账号中！!\"},\"styles\":{}}]', 1661757188, 1720164247);
INSERT INTO `cw_decorate_page` VALUES (10, 10, '任务中心', '[{\"id\":\"lps6z7k4z9x0e\",\"title\":\"任务中心\",\"name\":\"task-center\",\"content\":{\"title\":\"做任务赚额度\",\"subTitle\":\"免费获得条数\",\"data\":[{\"image\":\"resource/image/decorate/task_qdjl.png\",\"name\":\"每日签到\",\"customName\":\"签到奖励\",\"show\":1,\"type\":1},{\"image\":\"resource/image/decorate/task_yqhy.png\",\"name\":\"邀请新用户\",\"customName\":\"邀请好友\",\"show\":1,\"type\":2},{\"image\":\"resource/image/decorate/task_fxhy.png\",\"name\":\"分享给好友\",\"customName\":\"分享好友\",\"show\":1,\"type\":3},{\"image\":\"resource/image/decorate/task_fxhh.png\",\"name\":\"分享绘画至广场\",\"customName\":\"绘画分享\",\"show\":1,\"type\":4},{\"image\":\"resource/image/decorate/task_fxsp.png\",\"name\":\"分享视频至广场\",\"customName\":\"视频分享\",\"show\":1,\"type\":5},{\"image\":\"resource/image/decorate/task_fxyy.png\",\"name\":\"分享音乐至广场\",\"customName\":\"音乐分享\",\"show\":1,\"type\":6},{\"image\":\"resource/image/decorate/task_fxznt.png\",\"name\":\"分享智能体\",\"customName\":\"智能体分享\",\"show\":1,\"type\":7}]},\"styles\":{}}]', 1701766819, 1722411308);
INSERT INTO `cw_decorate_page` VALUES (11, 11, 'AI应用', '[{\"title\":\"AI应用\",\"name\":\"ai-app-center\",\"content\":{\"pc_title\":\"应用中心\",\"pc_background\":\"resource/image/decorate/app_banner.png\",\"pc_text_color\":3,\"mobile_title\":\"探索无限可能 \",\"data\":[{\"image\":\"resource/image/decorate/app_ai_search.png\",\"title\":\"AI搜索\",\"desc\":\"快速准确找到所需的信息或资源\",\"pcLink\":{\"name\":\"AI搜索\",\"path\":\"/search\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"path\":\"\",\"type\":\"shop\",\"isTab\":false},\"is_show\":\"1\"},{\"image\":\"resource/image/decorate/app_draw_sd.png\",\"pcLink\":{\"name\":\"SD绘画\",\"path\":\"/draw/sd\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"name\":\"SD绘画\",\"path\":\"/packages/pages/draw/sd\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\",\"title\":\"SD绘画\",\"desc\":\"一键快速出图\"},{\"image\":\"resource/image/decorate/app_music.png\",\"pcLink\":{\"name\":\"AI音乐\",\"path\":\"/music\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"name\":\"AI音乐\",\"path\":\"/packages/pages/music/music\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\",\"title\":\"AI音乐\",\"desc\":\"快速生成音乐\"},{\"image\":\"resource/image/decorate/app_draw_mj.png\",\"title\":\"MJ绘画\",\"desc\":\"更智能的绘画工具\",\"pcLink\":{\"name\":\"MJ绘画\",\"path\":\"/draw/mj\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"name\":\"MJ绘画\",\"path\":\"/packages/pages/draw/mj\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\"},{\"image\":\"resource/image/decorate/app_draw_dalle.png\",\"pcLink\":{\"name\":\"DALLE绘画\",\"path\":\"/draw/dalle\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"name\":\"DALLE绘画\",\"path\":\"/packages/pages/draw/dalle\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\",\"title\":\"DALLE绘画\",\"desc\":\"秒出图\"},{\"image\":\"resource/image/decorate/app_mind.png\",\"title\":\"思维导图\",\"desc\":\"复杂概念快速呈现\",\"pcLink\":{\"path\":\"/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"path\":\"/packages/pages/mind_map/mind_map\",\"name\":\"思维导图\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\"},{\"image\":\"resource/image/decorate/app_video.png\",\"title\":\"AI视频\",\"desc\":\"文字+图片，快速生成视频\",\"pcLink\":{\"name\":\"AI视频\",\"path\":\"/video\",\"type\":\"shop\",\"isTab\":false},\"link\":{\"name\":\"AI视频\",\"path\":\"/packages/pages/video/video\",\"type\":\"shop\",\"canTab\":true,\"isTab\":false},\"is_show\":\"1\"}]},\"styles\":{}}]', 1701766819, 1724226686);
INSERT INTO `cw_decorate_page` VALUES (12, 12, '对话海报', '[{\"id\":\"li34ldqby4fkb\",\"title\":\"对话海报\",\"name\":\"dialogue-poster\",\"content\":{\"default\":2,\"defaultUrl1\":\"/resource/image/adminapi/default/dialogue_backdend01.png\",\"defaultUrl2\":\"/resource/image/adminapi/default/dialogue_backdend02.png\",\"defaultAvatar\":\"/resource/image/adminapi/default/user_avatar.jpeg\",\"defaultQrcode\":\"/resource/image/adminapi/default/qrcode.png\",\"poster\":1,\"posterUrl\":\"\",\"data\":\"邀请您前来体验\",\"showData\":\"1\",\"bgType\":1,\"bgColor1\":\"#2E60FD\",\"bgColor2\":\"#FF9F43\",\"showContentType\":1,\"contentNum\":6,\"textColor\":\"#ffffff\",\"bgColor\":\"#2E60FD\"},\"styles\":{}}]', 1661757188, 1723170697);
COMMIT;

BEGIN;
INSERT INTO `cw_config` VALUES (10, 'manual_kf', 'status', '1', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (11, 'manual_kf', 'icons', 'resource/image/adminapi/default/kf_icon.png', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (12, 'manual_kf', 'qr_code', 'resource/image/adminapi/default/kf_qr.png', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (13, 'manual_kf', 'title', '{\"value\":\"人工客服\",\"status\":\"1\"}', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (14, 'manual_kf', 'phone', '{\"value\":\"00000000\",\"status\":\"1\"}', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (15, 'manual_kf', 'service_time', '{\"value\":\"工作日9:00-18:00\",\"status\":\"1\"}', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (20, 'online_kf', 'status', '0', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (21, 'online_kf', 'icons', 'resource/image/adminapi/default/kf_online.png', 1705997715, 1705997715);
INSERT INTO `cw_config` VALUES (22, 'online_kf', 'link', '', 1705997715, 1705997715);
COMMIT;

BEGIN;
INSERT INTO `cw_dev_pay_way` VALUES (1, 2, 4, 1, 1);
INSERT INTO `cw_dev_pay_way` VALUES (2, 3, 4, 0, 1);
INSERT INTO `cw_dev_pay_way` VALUES (3, 2, 3, 1, 1);
INSERT INTO `cw_dev_pay_way` VALUES (4, 3, 3, 0, 1);
INSERT INTO `cw_dev_pay_way` VALUES (5, 2, 2, 1, 1);
INSERT INTO `cw_dev_pay_way` VALUES (6, 3, 2, 0, 1);
INSERT INTO `cw_dev_pay_way` VALUES (7, 2, 1, 1, 1);
INSERT INTO `cw_dev_pay_way` VALUES (8, 3, 1, 0, 1);
INSERT INTO `cw_dev_pay_config` VALUES (2, '微信支付', 2, '{\"interface_version\":\"v3\",\"merchant_type\":\"ordinary_merchant\",\"mch_id\":\"\",\"pay_sign_key\":\"\",\"apiclient_cert\":\"\",\"apiclient_key\":\"\"}', 'resource/image/adminapi/default/pay_wechat.png', '微信支付', 0);
INSERT INTO `cw_dev_pay_config` VALUES (3, '支付宝支付', 3, '{\"mode\":\"normal_mode\",\"merchant_type\":\"ordinary_merchant\",\"app_id\":\"\",\"private_key\":\"\",\"ali_public_key\":\"\"}', 'resource/image/adminapi/default/pay_alipay.png', '支付宝支付', 0);
COMMIT;

BEGIN;
INSERT INTO `cw_notice_setting` VALUES (1, 100, '注册验证码', '用户注册时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_175615071\",\"content\":\"验证码${code}，您正在注册成为新用户，感谢您的支持！\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在申请注册，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"注册成为新用户\",\"content\":\"验证码${code}，您正在注册成为新用户，感谢您的支持！\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在申请注册，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '2,5', NULL);
INSERT INTO `cw_notice_setting` VALUES (2, 101, '登录验证码', '用户手机号码登录时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_222458159\",\"content\":\"您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"验证吗登录\",\"content\":\"您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在登录，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '2,5', NULL);
INSERT INTO `cw_notice_setting` VALUES (3, 102, '绑定手机验证码', '用户绑定手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_207952628\",\"content\":\"您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在绑定手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"\"}', '2', NULL);
INSERT INTO `cw_notice_setting` VALUES (4, 103, '变更手机验证码', '用户变更手机号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_207952628\",\"content\":\"您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"\"}', '2', NULL);
INSERT INTO `cw_notice_setting` VALUES (5, 104, '找回登录密码验证码', '用户找回登录密码号码时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\"]}', '{\"type\":\"sms\",\"template_id\":\"SMS_175615069\",\"content\":\"您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"is_show\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"email\",\"title\":\"找回登录密码\",\"content\":\"您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在找回登录密码，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '2,5', NULL);
INSERT INTO `cw_notice_setting` VALUES (6, 200, '绑定邮箱验证码', '用户绑定邮箱时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"0\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}','{\"type\":\"email\",\"title\":\"绑定邮箱验证码\",\"content\":\"您正在绑定邮箱，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在绑定邮箱，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '5', NULL);
INSERT INTO `cw_notice_setting` VALUES (7, 201, '变更邮箱验证码', '用户变更邮箱时发送', 1, 2, '{\"type\":\"system\",\"title\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\"]}', '{\"type\":\"sms\",\"template_id\":\"\",\"content\":\"\",\"status\":\"0\",\"is_show\":\"0\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更手机号，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成短信设置。 2、第三方短信平台申请模板。\"]}', '{\"type\":\"oa\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"first\":\"\",\"remark\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}', '{\"type\":\"mnp\",\"template_id\":\"\",\"template_sn\":\"\",\"name\":\"\",\"tpl\":[],\"status\":\"0\",\"is_show\":\"\",\"tips\":[\"可选变量 验证码:code\",\"配置路径：小程序后台 > 功能 > 订阅消息\"]}','{\"type\":\"email\",\"title\":\"变更邮箱验证码\",\"content\":\"您正在变更邮箱，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"status\":\"1\",\"tips\":[\"可选变量 验证码:code\",\"示例：您正在变更邮箱，验证码${code}，切勿将验证码泄露于他人，本条验证码有效期5分钟。\",\"生效条件：1、管理后台完成邮箱设置。\"],\"is_show\":\"1\"}', '5', NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_music_style` VALUES (1, '现代流行', 'Modern fashion', 'resource/image/music/modern_fashion.png', 0, 1, 1716963029, 1717498782, NULL);
INSERT INTO `cw_music_style` VALUES (2, '儿歌', 'Nursery rhyme', 'resource/image/music/child.png', 0, 1, 1717062338, 1717498782, NULL);
INSERT INTO `cw_music_style` VALUES (3, '轻音乐', 'Light music', 'resource/image/music/pure.png', 0, 1, 1717062377, 1717491450, NULL);
INSERT INTO `cw_music_style` VALUES (4, '安静', 'quiet', 'resource/image/music/quiet.png', 0, 1, 1717062611, 1717493656, NULL);
INSERT INTO `cw_music_style` VALUES (5, '愉快', 'cheerful', 'resource/image/music/happy.png', 0, 1, 1717489876, 1717498755, NULL);
INSERT INTO `cw_music_style` VALUES (6, '摇滚', 'Rock', 'resource/image/music/rock.png', 0, 1, 1717489957, 1717498756, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_models` VALUES (1, 1, 'openai', 'resource/image/models/gpt3.5.png', 'GPT3.5', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":0,\"frequency_penalty\":0,\"agency_api\":\"\",\"global_directives\":\"\"}', 0, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (2, 1, 'openai', 'resource/image/models/gpt4.0.png', 'GPT4.0', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"agency_api\":\"\",\"global_directives\":\"\"}', 1, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (3, 1, 'xunfei', 'resource/image/models/xunfei.png', '讯飞星火', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":0,\"frequency_penalty\":0,\"agency_api\":\"\",\"global_directives\":\"\"}', 2, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (4, 1, 'zhipu', 'resource/image/models/zhipu.png', '智谱清言', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"do_sample\":\"1\",\"agency_api\":\"\",\"global_directives\":\"\"}', 3, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (5, 1, 'baidu', 'resource/image/models/baidu.png', '文心一言', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"penalty_score\":\"1\",\"disable_search\":\"1\",\"agency_api\":\"\",\"global_directives\":\"\"}', 4, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (6, 1, 'qwen', 'resource/image/models/qwen.png', '通义千问', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"repetition_penalty\":\"1.0\",\"presence_penalty\":\"0.0\",\"enable_search\":\"0\",\"agency_api\":\"\",\"global_directives\":\"\"}', 5, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (7, 1, 'openai', 'resource/image/models/api2d.png', 'API2d', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"agency_api\":\"\",\"global_directives\":\"\"}', 6, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (8, 1, 'openai', 'resource/image/models/zhipu.png', 'ChatGLM2-6B', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"agency_api\":\"\",\"global_directives\":\"\"}', 7, 1, 1, 1, 1716448072, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (9, 1, 'azure', 'resource/image/models/azure.png', 'AzureGPT', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"check_key\":true,\"agency_api\":\"\",\"global_directives\":\"\"}', 9, 0, 0, 0, 1719460230, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (10, 1, 'doubao', 'resource/image/models/doubao.png', '字节豆包', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"frequency_penalty\":\"0\",\"check_key\":true,\"agency_api\":\"\",\"global_directives\":\"\"}', 8, 0, 0, 0, 1719460852, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (11, 1, 'openai', 'resource/image/models/deepseek.png', 'DeepSeek', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"check_key\":true,\"agency_api\":\"https:\\/\\/api.deepseek.com\",\"global_directives\":\"\"}', 12, 0, 0, 0, 1719461111, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (12, 1, 'ollama', 'resource/image/models/ollama.png', 'Ollama', '', '{\"context_num\":\"3\",\"temperature\":\"0.8\",\"repeat_penalty\":\"1.1\",\"mirostat\":\"0\",\"mirostat_eta\":\"0.1\",\"check_key\":false,\"agency_api\":\"\",\"global_directives\":\"\"}', 10, 0, 0, 0, 1719461216, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (13, 1, 'openai', 'resource/image/models/lmstudio.png', 'LM Studio', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"check_key\":false,\"agency_api\":\"\",\"global_directives\":\"\"}', 11, 0, 0, 0, 1719461288, 1719470720, NULL);
INSERT INTO `cw_models` VALUES (14, 1, 'openai', 'resource/image/models/kimi.png', 'Kimi', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"0\",\"frequency_penalty\":\"0\",\"check_key\":true,\"agency_api\":\"https:\\/\\/api.moonshot.cn\",\"global_directives\":\"\"}', 13, 0, 0, 0, 1719973593, 1720075037, NULL);
INSERT INTO `cw_models` VALUES (15, 1, 'minimax', 'resource/image/models/minimax.png', 'MiniMax', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"check_key\":true,\"agency_api\":\"\",\"global_directives\":\"\"}', 14, 0, 0, 0, 1719975677, 1720075099, NULL);
INSERT INTO `cw_models` VALUES (16, 1, 'baichuan', 'resource/image/models/baichuan.png', '百川智能', '', '{\"context_num\":\"3\",\"temperature\":\"1\",\"presence_penalty\":\"1.1\",\"frequency_penalty\":\"1\",\"check_key\":true,\"agency_api\":\"https:\\/\\/api.baichuan-ai.com\",\"global_directives\":\"\"}', 15, 0, 0, 0, 1719993483, 1720075025, NULL);
INSERT INTO `cw_models` VALUES (210, 2, 'openai', 'resource/image/models/openai.png', 'OpenAI(ada)', '', '{\"context_num\":3,\"temperature\":1,\"presence_penalty\":0,\"frequency_penalty\":0,\"agency_api\":\"\",\"global_directives\":\"\"}', 1, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (220, 2, 'openai', 'resource/image/models/openai.png', 'OpenAI(small)', '', '{\"agency_api\":\"\"}', 2, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (230, 2, 'openai', 'resource/image/models/openai.png', 'OpenAI(large)', '', '{\"agency_api\":\"\"}', 3, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (240, 2, 'xunfei', 'resource/image/models/xunfei.png', '讯飞星火', '', '{}', 4, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (250, 2, 'zhipu', 'resource/image/models/zhipu.png', '智谱清言', '', '{}', 5, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (260, 2, 'openai', 'resource/image/models/m3e.png', 'M3e(large)', '', '{\"agency_api\":\"\"}', 6, 1, 1, 1, 1716448072, 1716448072, NULL);
INSERT INTO `cw_models` VALUES (270, 2, 'qwen', 'resource/image/models/qwen.png', '通义千问', '', '{\"agency_api\":\"\"}', 7, 0, 0, 0, 1719470097, 1719470097, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_models_cost` VALUES (1, 1, 1, 'openai', 'gpt-3.5-turbo', 'gpt-3.5-turbo', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (2, 1, 1, 'openai', 'gpt-3.5-turbo-0125', 'gpt-3.5-turbo-0125', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (3, 1, 1, 'openai', 'gpt-3.5-turbo-1106', 'gpt-3.5-turbo-1106', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (4, 1, 1, 'openai', 'gpt-3.5-turbo-instruct', 'gpt-3.5-turbo-instruct', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (200, 2, 1, 'openai', 'gpt-4', 'gpt-4', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (201, 2, 1, 'openai', 'gpt-4-32k', 'gpt-4-32k', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (202, 2, 1, 'openai', 'gpt-4-0613', 'gpt-4-32k-0613', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (203, 2, 1, 'openai', 'gpt-4-turbo', 'gpt-4-turbo', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (204, 2, 1, 'openai', 'gpt-4-turbo-2024-04-09', 'gpt-4-turbo-2024-04-09', 0.0000, 5, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (205, 2, 1, 'openai', 'gpt-4-turbo-preview', 'gpt-4-turbo-preview', 0.0000, 6, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (206, 2, 1, 'openai', 'gpt-4-0125-preview', 'gpt-4-0125-preview', 0.0000, 7, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (207, 2, 1, 'openai', 'gpt-4-1106-preview', 'gpt-4-1106-preview', 0.0000, 8, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (208, 2, 1, 'openai', 'gpt-4-vision-preview', 'gpt-4-vision-preview', 0.0000, 9, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (209, 2, 1, 'openai', 'gpt-4-1106-vision-preview', 'gpt-4-1106-vision-preview', 0.0000, 10, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (210, 2, 1, 'openai', 'gpt-4o', 'gpt-4o', 0.0000, 11, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (211, 2, 1, 'openai', 'gpt-4o-2024-05-13', 'gpt-4o-2024-05-13', 0.0000, 12, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (212, 2, 1, 'openai', 'gpt-4o-mini', 'gpt-4o-mini', 0.0000, 12, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (300, 3, 1, 'xunfei', 'generalv3', 'generalv3', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (301, 3, 1, 'xunfei', 'generalv3.5', 'generalv3.5', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (302, 3, 1, 'xunfei', 'pro-128k', 'pro-128k', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (303, 3, 1, 'xunfei', '4.0Ultra', '4.0Ultra', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (400, 4, 1, 'zhipu', 'glm-3-turbo', 'glm-3-turbo', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (401, 4, 1, 'zhipu', 'glm-4v', 'glm-4v', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (402, 4, 1, 'zhipu', 'glm-4', 'glm-4', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (500, 5, 1, 'baidu', 'ERNIE-Bot', 'ERNIE-Bot', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (501, 5, 1, 'baidu', 'ERNIE-Bot-turbo', 'ERNIE-Bot-turbo', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (502, 5, 1, 'baidu', 'ERNIE-Bot 4.0', 'ERNIE-Bot 4.0', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (503, 5, 1, 'baidu', 'ERNIE-Bot-8K', 'ERNIE-Bot-8K', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (600, 6, 1, 'qwen', 'qwen-turbo', 'qwen-turbo', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (601, 6, 1, 'qwen', 'qwen-max', 'qwen-max', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (602, 6, 1, 'qwen', 'qwen-plus', 'qwen-plus', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (603, 6, 1, 'qwen', 'qwen-max-longcontext', 'qwen-max-longcontext', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (710, 7, 1, 'openai', 'gpt-3.5-turbo', 'gpt-3.5-turbo', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (711, 7, 1, 'openai', 'gpt-3.5-turbo-0125', 'gpt-3.5-turbo-0125', 0.0000, 2, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (712, 7, 1, 'openai', 'gpt-3.5-turbo-1106', 'gpt-3.5-turbo-1106', 0.0000, 3, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (713, 7, 1, 'openai', 'gpt-3.5-turbo-instruct', 'gpt-3.5-turbo-instruct', 0.0000, 4, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (750, 7, 1, 'openai', 'gpt-4', 'gpt-4', 0.0000, 5, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (751, 7, 1, 'openai', 'gpt-4-32k', 'gpt-4-32k', 0.0000, 6, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (752, 7, 1, 'openai', 'gpt-4-0613', 'gpt-4-32k-0613', 0.0000, 7, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (753, 7, 1, 'openai', 'gpt-4-turbo', 'gpt-4-turbo', 0.0000, 8, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (754, 7, 1, 'openai', 'gpt-4-turbo-2024-04-09', 'gpt-4-turbo-2024-04-09', 0.0000, 9, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (755, 7, 1, 'openai', 'gpt-4-turbo-preview', 'gpt-4-turbo-preview', 0.0000, 10, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (756, 7, 1, 'openai', 'gpt-4-0125-preview', 'gpt-4-0125-preview', 0.0000, 11, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (757, 7, 1, 'openai', 'gpt-4-1106-preview', 'gpt-4-1106-preview', 0.0000, 12, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (758, 7, 1, 'openai', 'gpt-4-vision-preview', 'gpt-4-vision-preview', 0.0000, 13, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (759, 7, 1, 'openai', 'gpt-4-1106-vision-preview', 'gpt-4-1106-vision-preview', 0.0000, 14, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (760, 7, 1, 'openai', 'gpt-4o', 'gpt-4o', 0.0000, 15, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (761, 7, 1, 'openai', 'gpt-4o-2024-05-13', 'gpt-4o-2024-05-13', 0.0000, 16, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (800, 8, 1, 'openai', 'ChatGLM2-6B', 'ChatGLM2-6B', 0.0000, 1, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (900, 9, 1, 'azure', 'azure_gpt35-turbo', 'azure_gpt35-turbo', 0.0000, 0, 1, 1719460230);
INSERT INTO `cw_models_cost` VALUES (1000, 10, 1, 'doubao', 'ep-20240625024623-brhnl', 'Doubao-lite-4k', 0.0000, 0, 1, 1719460852);
INSERT INTO `cw_models_cost` VALUES (1051, 11, 1, 'openai', 'deepseek-chat', 'deepseek-chat', 0.0000, 0, 1, 1719461111);
INSERT INTO `cw_models_cost` VALUES (1052, 11, 1, 'openai', 'deepseek-coder', 'deepseek-coder', 0.0000, 1, 1, 1719461111);
INSERT INTO `cw_models_cost` VALUES (1100, 12, 1, 'ollama', 'llama3', 'llama3', 0.0000, 0, 1, 1719461216);
INSERT INTO `cw_models_cost` VALUES (1150, 13, 1, 'openai', 'lmstudio-community/Meta-Llama-3-8B-Instruct-GGUF', 'LLM3-8B', 0.0000, 0, 1, 1719461288);
INSERT INTO `cw_models_cost` VALUES (1160, 14, 1, 'openai', 'moonshot-v1-8k', 'moonshot-v1-8k', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1161, 14, 1, 'openai', 'moonshot-v1-32k', 'moonshot-v1-32k', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1162, 14, 1, 'openai', 'moonshot-v1-128k', 'moonshot-v1-128k', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1170, 15, 1, 'minimax', 'abab5.5-chat', 'abab5.5-chat', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1180, 16, 1, 'baichuan', 'Baichuan4', 'Baichuan4', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1181, 16, 1, 'baichuan', 'Baichuan3-Turbo', 'Baichuan3-Turbo', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1182, 16, 1, 'baichuan', 'Baichuan3-Turbo-128k', 'Baichuan3-Turbo-128k', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1183, 16, 1, 'baichuan', 'Baichuan2-Turbo', 'Baichuan2-Turbo', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (1184, 16, 1, 'baichuan', 'Baichuan2-Turbo-192k', 'Baichuan2-Turbo-192k', 0.0000, 0, 1, 1719973593);
INSERT INTO `cw_models_cost` VALUES (20010, 210, 2, 'openai', 'text-embedding-ada-002', 'OpenAI(ada)', 0.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20020, 220, 2, 'openai', 'text-embedding-3-small', 'OpenAI(small)', 1.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20030, 230, 2, 'openai', 'text-embedding-3-large', 'OpenAI(large)', 0.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20040, 240, 2, 'xunfei', 'xunfei', '科大讯飞', 0.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20050, 250, 2, 'zhipu', 'zhipu', '智普清言', 0.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20060, 260, 2, 'openai', 'm3e-large', 'M3e(large)', 0.0000, 0, 1, 1716965015);
INSERT INTO `cw_models_cost` VALUES (20070, 270, 2, 'qwen', 'text-embedding-v1', '通义千问', 0.0000, 0, 1, 1719470097);
COMMIT;

BEGIN;
INSERT INTO `cw_key_rule` VALUES (1, 1, 1, 'openai', 'Incorrect API key provided', 'key不正确', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2, 1, 1, 'openai', 'This key is associated with a deactivated account', 'key账号被封', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3, 1, 1, 'openai', 'You exceeded your current quota', 'key账号余额不足', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4, 1, 1, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (21, 2, 1, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (22, 2, 1, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (23, 2, 1, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (24, 2, 1, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (30, 3, 1, 'xunfei', 'licc failed', 'key不具备调用权限', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (31, 3, 1, 'xunfei', 'SchemaCheckError:', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2010, 210, 2, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2011, 210, 2, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2012, 210, 2, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2013, 210, 2, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2020, 220, 2, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2021, 220, 2, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2022, 220, 2, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2023, 220, 2, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2030, 210, 2, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2031, 210, 2, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2032, 210, 2, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2033, 210, 2, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2040, 240, 2, 'xunfei', 'licc failed', 'key不具备调用权限', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (2041, 240, 2, 'xunfei', 'SchemaCheckError:', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3010, 0, 3, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3011, 0, 3, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3012, 0, 3, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3013, 0, 3, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3020, 0, 3, 'kdxf', 'licc failed', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (3021, 0, 3, 'kdxf', 'SchemaCheckError:', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4010, 0, 4, 'openai', 'This key is associated with a deactivated account', 'key账号被封。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4011, 0, 4, 'openai', 'You exceeded your current quota', 'key账号余额不足。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4012, 0, 4, 'openai', 'The OpenAI account associated', 'key关联账号已被停用', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4013, 0, 4, 'openai', 'Incorrect API key provided', 'key不正确。', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4020, 0, 4, 'kdxf', 'licc failed', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
INSERT INTO `cw_key_rule` VALUES (4021, 0, 4, 'kdxf', 'SchemaCheckError:', 'key是无效不可用的', 1, 1694750306, 1694750306, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_chat_category` VALUES (1, '文案助手', 0, 1, 'resource/image/adminapi/default/problem01.png', 1689821136, 1689821136, NULL);
INSERT INTO `cw_chat_category` VALUES (2, '中英互译', 0, 1, 'resource/image/adminapi/default/problem02.png', 1689821136, 1689821136, NULL);
INSERT INTO `cw_chat_category` VALUES (3, '智能写作', 0, 1, 'resource/image/adminapi/default/problem03.png', 1689821136, 1689821136, NULL);
INSERT INTO `cw_chat_sample` VALUES (1, 1, 0, '你是一个励志演说家,请你帮我用小红书的风格写一个关于”奋斗“的演讲稿,要求能引起听众的共鸣，激励他们努力实现自己的目标并争取更好的可能性。', 1, 1689821149, 1690533169, NULL);
INSERT INTO `cw_chat_sample` VALUES (2, 1, 0, '请使用小红书的风格，帮我为小红书账号编写一份推文。要有抓人眼球的标题，每一段首带emoji ，结束有标签。账号主要发布美食相关的内容，需要突出食材、制作方法、口感等方面，吸引用户的食欲和好奇心。要求不少于100字。', 1, 1689834725, 1689843659, NULL);
INSERT INTO `cw_chat_sample` VALUES (3, 2, 0, '帮我写一份人工智能软件的产品经理的工作月报，要求不少于500字，并且阐述本月的工作内容、工作中发现的问题和未来计划。', 1, 1689843698, 1689843698, NULL);
INSERT INTO `cw_chat_sample` VALUES (4, 3, 33, '请为我创建一个多层次的思维导图，主题是市场营销，请包括至少5个分支主题。同时轻微每个分支主题添加至少两个子主题，并在需要的情况下，为自主体添加更多层次的子主题。不要再主题前写层级数字，内容需要尽可能详细。请使用Markdown格式来呈现输出内容。', 1, 1689843868, 1690533228, NULL);
INSERT INTO `cw_chat_sample` VALUES (9, 3, 30, '请为我创建一个多层次的思维导图，主题是市场营销，请包括至少5个分支主题。同时轻微每个分支主题添加至少两个子主题，并在需要的情况下，为自主体添加更多层次的子主题。不要再主题前写层级数字，内容需要尽可能详细。请使用Markdown格式来呈现输出内容。', 1, 1690368450, 1690533238, NULL);
INSERT INTO `cw_chat_sample` VALUES (14, 2, 0, '帮我写一份人工智能软件的产品经理的工作月报，要求不少于500字，并且阐述本月的工作内容、工作中发现的问题和未来计划。', 1, 1690533188, 1690533188, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_creation_category` VALUES (1, 'resource/image/creation/20230905163155a4a9d9896.png', 'AI工作', 0, 1, 1693885367, 1693902728, NULL);
INSERT INTO `cw_creation_category` VALUES (2, 'resource/image/creation/20230905163155cdcc21644.png', 'AI写作', 0, 1, 1693885481, 1693902734, NULL);
INSERT INTO `cw_creation_category` VALUES (3, 'resource/image/creation/20230905163155f0f806820.png', 'AI营销', 0, 1, 1693885494, 1693902746, NULL);
INSERT INTO `cw_creation_category` VALUES (4, 'resource/image/creation/20230905163155466ed2306.png', 'AI生活', 0, 1, 1693885508, 1693902752, NULL);
INSERT INTO `cw_creation_category` VALUES (5, 'resource/image/creation/202309051631555b3979708.png', '趣味助手', 0, 1, 1693885522, 1693902757, NULL);
INSERT INTO `cw_creation_model` VALUES (1, '周报日报', 'resource/image/creation/202309051150476e18c9430.png', 0, 1, 1, '根据我输入的${ljju8wlo}，使用下面提供的文本作为${lja6u9f7}的基础，生成一个简要的${lja6u9f7}，突出最重要的要点。${lja6u9f7}应以中文书写，且应易于阅读和理解。请首先编辑以下文本：${ljczht8s}', '输入工作内容，快速生成日报', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8y3\",\"props\":{\"field\":\"ljju8wlo\",\"title\":\"职位名称\",\"defaultValue\":\"\",\"placeholder\":\"新媒体运营\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8y5\",\"props\":{\"field\":\"ljczht8s\",\"title\":\"工作内容\",\"placeholder\":\"1.剪辑抖音视频8个 2.拍摄试镜2个 3.拍摄产品细节视频\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":200,\"autosize\":false,\"isRequired\":true}},{\"name\":\"WidgetRadio\",\"title\":\"单选\",\"id\":\"lm5rj8y7\",\"props\":{\"field\":\"lja6u9f7\",\"title\":\"生成类型\",\"options\":[\"日报\",\"周报\",\"月报\",\"汇总\"],\"defaultValue\":\"日报\",\"isRequired\":true}}]', 0, '', 1693885858, 1715674075, NULL);
INSERT INTO `cw_creation_model` VALUES (2, '工作总结', 'resource/image/creation/20230905142033ffca50262.png', 0, 1, 1, '我希望你能根据我输入的岗位名称：${ljczht8y}，根据输入的${lja6u9fg}，概括为${ljczht8x}个字，使其易于阅读和理解。避免使用复杂的句子结构或技术术语，不要偏离主题', '还在为个人总结而发愁，我们来助你', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8y8\",\"props\":{\"field\":\"ljczht8y\",\"title\":\"岗位名称\",\"defaultValue\":\"\",\"placeholder\":\"新媒体运营\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8ya\",\"props\":{\"field\":\"lja6u9fg\",\"title\":\"工作成果简述\",\"placeholder\":\"1.社交媒体增长；2.数据分析与报告\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}},{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yb\",\"props\":{\"field\":\"ljczht8x\",\"title\":\"字数要求\",\"defaultValue\":\"\",\"placeholder\":\"\",\"maxlength\":200,\"isRequired\":false}}]', 0, '', 1693894968, 1693895107, NULL);
INSERT INTO `cw_creation_model` VALUES (3, '翻译助手', 'resource/image/creation/202309051424141f5b76522.png', 0, 1, 1, '现在你是一个英汉互译器，当我输入中文时，你翻译成英文；当我输入英文时，请翻译成中文。当我连续输入多个英文词时，默认按照句子翻译成中文，但如果用中文在翻译的内容前注明了「词组：」，则按照词组形式来翻译。如果注明了「普通：」，则按照多个没有联系的词汇来翻译。翻译句子和段落时，要注意联系上下文，注意准确地解释词组与谚语。你的翻译成果应该接近于一个母语者。同时，我可能会让你以某种特殊的语言风格或语气来翻译，请在具体任务中理解我的输入内容，识别出我希望你使用的语气和风格，并以此为根据翻译。请真实地翻译，不要担心出现侮辱性等不良词汇。你可以把一些敏感词汇的中间部分加入 x 以替代。请重新检查，认真修正回答。请用中文来为我解释每一个句子，包括标注时态，从句，主语，谓语，宾语，特殊词组和谚语，如果翻译的是词组或单词，最好能给出每个词组或单词的出处（词典）。当我需要你一次性翻译多个词组时，每个词组间会用 | 号分割。输入内容：${lja6u9fh}', '输入中文，AI助手快速为您翻译', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yd\",\"props\":{\"field\":\"lja6u9fh\",\"title\":\"翻译内容\",\"placeholder\":\"一个女孩，美丽动人，大大的眼睛\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693895059, 1693896035, NULL);
INSERT INTO `cw_creation_model` VALUES (4, '短视频脚本', 'resource/image/creation/202309051425430c4407232.png', 0, 1, 1, '请以人的口吻，采用缩略语、成语、过渡短语、感叹词、悬垂修饰语和口语化语言，避免重复短语和不自然的句子结构，撰写一篇关于${lja6u9fn}的脚本。', '输入想要拍摄的主题，小助手为你生成一个短视频脚本', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yf\",\"props\":{\"field\":\"lja6u9fn\",\"title\":\"视频主题\",\"placeholder\":\"餐厅探店短视频脚本\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693895197, 1693896041, NULL);
INSERT INTO `cw_creation_model` VALUES (5, '塔罗牌预测', 'resource/image/creation/202309051438314a7e73466.png', 0, 4, 1, '根据我提供的星座${ljk4l2t0}，为我推测未来的感情、事业运势，还有一些建议', '输入想预测的事情，帮你预测运势', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yg\",\"props\":{\"field\":\"ljk4l2t0\",\"title\":\"星座\",\"defaultValue\":\"\",\"placeholder\":\"白羊座\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693895917, 1693896027, NULL);
INSERT INTO `cw_creation_model` VALUES (6, '写故事', 'resource/image/creation/20230905143857412af3152.png', 0, 2, 1, '我希望你扮演一个讲故事的人，用中文回应。你会想出引人入胜、富有想象力、引人入胜的有趣故事。它可以是童话、教育故事或任何其他类型的故事，有可能吸引人们的注意力和想象力。根据目标受众的不同，你可以选择讲故事的特定主题或主题，例如，如果是孩子，那么你可以谈论动物；如果是成年人，那么基于历史的故事可能会更好地吸引他们，我的第一个要求是${ljavt5jb}', '根据关键词，快速生成一段故事文章', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yi\",\"props\":{\"field\":\"ljavt5jb\",\"title\":\"故事内容\",\"placeholder\":\"从前有一座山，山里有一个小和尚\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":200,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693895997, 1703661390, 1703661390);
INSERT INTO `cw_creation_model` VALUES (7, '送女友礼物', 'resource/image/creation/202309051441570f6854553.png', 0, 5, 1, '根据我输入的节日${ljk2sk0b}，推荐相应的礼物送女朋友\n节日名称：${ljk2sk0b}', '根据节日推荐送什么礼物给女友', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yj\",\"props\":{\"field\":\"ljk2sk0b\",\"title\":\"节日名称\",\"defaultValue\":\"\",\"placeholder\":\"情人节\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693896168, 1693896168, NULL);
INSERT INTO `cw_creation_model` VALUES (8, '表白信', 'resource/image/creation/20230905144307988878894.png', 0, 2, 1, '你是个感情专家和大作家，现在见到爱人${ljk1mnjp}，假设对方是${ljk1mnk0}，写一篇对${ljk1mnjw}的表白情书。我对你的要求是，以“你的爱人”结尾', '情感专家在线写情书', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yk\",\"props\":{\"field\":\"ljk1mnjp\",\"title\":\"表白对象名字\",\"defaultValue\":\"\",\"placeholder\":\"刘亦菲\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetRadio\",\"title\":\"单选\",\"id\":\"lm5rj8ym\",\"props\":{\"field\":\"ljk1mnk0\",\"title\":\"对象性别\",\"options\":[\"男\",\"女\"],\"defaultValue\":\"女\",\"isRequired\":true}}]', 0, '', 1693896274, 1693896274, NULL);
INSERT INTO `cw_creation_model` VALUES (9, '个性签名', 'resource/image/creation/20230905144538d9ba55830.png', 0, 3, 1, '根据我的要求：${ljk1d7jw}，结合网络流行词，生成几条独一无二的创意签名', '制作一条个性签名', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yn\",\"props\":{\"field\":\"ljk1d7jw\",\"title\":\"要求\",\"defaultValue\":\"\",\"placeholder\":\"QQ个性签名、幽默\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693896349, 1693896349, NULL);
INSERT INTO `cw_creation_model` VALUES (10, '高情商回复', 'resource/image/creation/20230905150853852657376.png', 0, 5, 1, '我想让你充当一个高情商交流大师。我会说“${ljjx88qd}”，然后用幽默有趣的方式指导我怎么回复这句话，如果可以的话这句回复让她有机会给我们开展出新的话题让聊天方式不再单一。', '高情商回答', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yp\",\"props\":{\"field\":\"ljjx88qd\",\"title\":\"输入你想说的话\",\"placeholder\":\"你好漂亮呀\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":true,\"isRequired\":true}}]', 0, '', 1693897738, 1693897738, NULL);
INSERT INTO `cw_creation_model` VALUES (11, '单身狗导师', 'resource/image/creation/2023090515100379fa89979.png', 0, 5, 1, '假设你是一位有经验的单身人士，是具有良好的人际交往能力和情感智慧，根据输入的内容描述${lja6u9f9}，帮助其他单身人士改善自己的情感状态，提高自己的约会技巧和恋爱能力，从而更好地融入社交圈，找到合适的伴侣。', '根据导师的人生经验，让AI给你建议', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yq\",\"props\":{\"field\":\"lja6u9f9\",\"title\":\"文本内容\",\"defaultValue\":\"\",\"placeholder\":\"如何追一个心仪已久的女生\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693897809, 1693897809, NULL);
INSERT INTO `cw_creation_model` VALUES (12, '论文资料', 'resource/image/creation/2023090515583661d410599.png', 20, 2, 1, '根据输入论文主题${ljk4l2sh}，帮我推荐一些相关文献。要优先推荐国内的资料', '只需输入论文主题，便能帮你推荐一些相关文献', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yr\",\"props\":{\"field\":\"ljk4l2sh\",\"title\":\"论文主题\",\"defaultValue\":\"\",\"placeholder\":\"科学与技术\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693900770, 1693900770, NULL);
INSERT INTO `cw_creation_model` VALUES (13, '诗词创作', 'resource/image/creation/202309051612426ccec3826.png', 40, 2, 1, '根据提供的主题，写出关于${ljavt5jk}的精美诗词。', '根据主题写出及精美诗词', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8ys\",\"props\":{\"field\":\"ljavt5jk\",\"title\":\"主题内容\",\"defaultValue\":\"\",\"placeholder\":\"思念\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693901605, 1693901616, NULL);
INSERT INTO `cw_creation_model` VALUES (14, '外卖好评', 'resource/image/creation/202309051617269c7d89114.jpeg', 60, 3, 1, '我是一个消费者，请从口味、品相、送货速度、商家态度等方面给予商家好评，不少于50个字。我的餐品名称是：${ljk4l2t2}', '一键生成外卖好评', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yt\",\"props\":{\"field\":\"ljk4l2t2\",\"title\":\"餐品名称\",\"defaultValue\":\"\",\"placeholder\":\"奶茶\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693901900, 1693901900, NULL);
INSERT INTO `cw_creation_model` VALUES (15, '阅读助手', 'resource/image/creation/202309051618494e7635274.png', 60, 2, 1, '在以下这个场景中，有人对我说了一句话，请帮我分析对方可能想表达什么意思，并提供一个合适的回应。场景：${ljiiy508}。说话人说：${ljiiy50b}。对方的意图可能是什么？我应该如何回应？', '对于一些无法理解的对话，提供对话背景让 AI 来进行解读并制定出适当的回应。', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8yu\",\"props\":{\"field\":\"ljiiy508\",\"title\":\"情境描述\",\"defaultValue\":\"\",\"placeholder\":\"吃饭的时候\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yw\",\"props\":{\"field\":\"ljiiy50b\",\"title\":\"具体的话语\",\"placeholder\":\"你好厉害！\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":false}}]', 0, '', 1693902003, 1693902003, NULL);
INSERT INTO `cw_creation_model` VALUES (16, '挂号咨询', 'resource/image/creation/20230905162016830885613.png', 60, 4, 1, '你现在是一名全能医生，根据输入的描述，你可以告诉我要挂什么科，有哪些建议。我的要求是：${ljk2sk02}', 'AI医生助手', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8yy\",\"props\":{\"field\":\"ljk2sk02\",\"title\":\"病情描述\",\"placeholder\":\"牙齿疼，要挂什么科\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693902498, 1693902498, NULL);
INSERT INTO `cw_creation_model` VALUES (17, '淘宝好评', 'resource/image/creation/20230905162840c84fa7514.png', 70, 3, 1, '请根据我的产品分类及产品特性，输出一段100字左右的产品好评文案，内容要符合日常消费者口吻，我的产品是：${ljavt5j7}', '自动生成商品评价', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8z0\",\"props\":{\"field\":\"ljavt5j7\",\"title\":\"商品名称和卖点\",\"placeholder\":\"万彩电动牙刷，五档模式、声波震动、IPX7级防水、杜邦刷毛、续航30天\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":200,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693902568, 1693902916, NULL);
INSERT INTO `cw_creation_model` VALUES (18, '节日祝福', 'resource/image/creation/20230905163544815366276.png', 70, 4, 1, '写一个关于${ljjx88qa}的节日祝福', '节日祝福与问候', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8z1\",\"props\":{\"field\":\"ljjx88qa\",\"title\":\"节日\",\"defaultValue\":\"\",\"placeholder\":\"母亲节\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693902986, 1693902986, NULL);
INSERT INTO `cw_creation_model` VALUES (19, '产品描述', 'resource/image/creation/2023090516364558eb53135.png', 75, 3, 1, '写一篇${ljjx88qf}的产品描述，用于产品推广，描述贴切、高级。', '根据提供的产品生成产品描述', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8z2\",\"props\":{\"field\":\"ljjx88qf\",\"title\":\"产品名称\",\"defaultValue\":\"\",\"placeholder\":\"电动牙刷\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693903046, 1693903046, NULL);
INSERT INTO `cw_creation_model` VALUES (20, '关键词标题', 'resource/image/creation/20230905163740d46128606.png', 80, 2, 1, '根据输入的关键词${ljk2sk09}，帮我生成五个吸引人的标题', '快速生成关键词标题', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8z3\",\"props\":{\"field\":\"ljk2sk09\",\"title\":\"关键词\",\"defaultValue\":\"\",\"placeholder\":\"网站引流\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693903103, 1693903103, NULL);
INSERT INTO `cw_creation_model` VALUES (21, '菜谱大全', 'resource/image/creation/2023090516383964d438185.png', 80, 4, 1, '现在你是一个经验丰富的厨师，你会做中餐西餐日料糕点等各种食物。我是一个新手厨师，因此你需要给我一份非常详细的菜谱，并在最后指出制作这道菜肴需要注意的地方。这样我才能做出更好的美食！我的想做的菜是：${ljk2sjzz}', '输入原材料，获取相关菜品做法', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8z5\",\"props\":{\"field\":\"ljk2sjzz\",\"title\":\"菜品名称\",\"placeholder\":\"鱼香肉丝\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693903169, 1693903169, NULL);
INSERT INTO `cw_creation_model` VALUES (22, '广告文案', 'resource/image/creation/20230905163955add4f8121.png', 80, 3, 1, '我想让你充当广告策划。创建一个活动来推广产品或服务，请选择目标受众，制定关键信息和口号，选择宣传媒体渠道，并决定实现目标所需的任何其他活动，尽量的详细并且在1000字左右。我的第一个产品和要求是：${ljjx88qi}', '生成广告文案和口号，选择宣传媒体渠道，并决定实现目标所需的任何其他活动', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8z7\",\"props\":{\"field\":\"ljjx88qi\",\"title\":\"产品要求\",\"placeholder\":\"我需要一份针对年轻女性跑步鞋的广告文案\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":true,\"isRequired\":true}}]', 0, '', 1693903251, 1693903251, NULL);
INSERT INTO `cw_creation_model` VALUES (23, '电影推荐', 'resource/image/creation/2023090516411568c7f2589.png', 90, 4, 1, '根据输入电影主题${ljavt5ji}，结合豆瓣评分，帮我推荐几部近期好看的电影，如果我没有说明国内还是国外的，就优先推荐国内的电影；并写出推荐理由', 'AI为您推荐好看的电影', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8z8\",\"props\":{\"field\":\"ljavt5ji\",\"title\":\"电影主题\",\"defaultValue\":\"\",\"placeholder\":\"教育\",\"maxlength\":200,\"isRequired\":true}}]', 0, '', 1693903328, 1693903328, NULL);
INSERT INTO `cw_creation_model` VALUES (24, '旅游计划', 'resource/image/creation/20230905164220145132222.png', 100, 4, 1, '用${ljk4l2st}语言写一篇关于${ljk4l2sn}的出游攻略，包括日期，时间点，景点，往返程路线等', '制作出游攻略，包括日期，时间点，景点，往返程路线等', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetInput\",\"title\":\"单行文本\",\"id\":\"lm5rj8z9\",\"props\":{\"field\":\"ljk4l2sn\",\"title\":\"地点\",\"defaultValue\":\"\",\"placeholder\":\"云南\",\"maxlength\":200,\"isRequired\":true}},{\"name\":\"WidgetRadio\",\"title\":\"单选\",\"id\":\"lm5rj8zb\",\"props\":{\"field\":\"ljk4l2st\",\"title\":\"语言输出\",\"options\":[\"中文\",\"英语\",\"韩语\",\"泰语\"],\"defaultValue\":\"中文\",\"isRequired\":true}}]', 0, '', 1693903411, 1697533185, NULL);
INSERT INTO `cw_creation_model` VALUES (25, '英文写作', 'resource/image/creation/20230905164346b19d70271.png', 100, 2, 1, '根据我输入的要求，帮我润色成优美的英文作文，我的第一个要求是：${ljjflsw8}', '输入英文或中文，帮你润色成优美的英文', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8zd\",\"props\":{\"field\":\"ljjflsw8\",\"title\":\"写作内容\",\"placeholder\":\"写一封邀请信，邀请英国大学的一位教授组队来中国参加国际创新大赛\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}},{\"name\":\"WidgetSelect\",\"title\":\"下拉选项\",\"id\":\"lm5rj8zf\",\"props\":{\"field\":\"ljw9oucw\",\"title\":\"测试\",\"options\":[\"1\",\"2\"],\"defaultValue\":\"\",\"isRequired\":false}}]', 0, '', 1693903489, 1693903489, NULL);
INSERT INTO `cw_creation_model` VALUES (26, '朋友圈文案', 'resource/image/creation/20230905164505a83629538.png', 110, 3, 1, '根据输入的关键词${ljigx1qd}，帮我生成吸引人的朋友圈文案。', '输入关键词，生成吸引人的朋友圈文案。', 2, 1, 0.9, 0.5, 0.5, 0.6, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8zh\",\"props\":{\"field\":\"ljigx1qd\",\"title\":\"关键词\",\"placeholder\":\"周末去喝了咖啡，和朋友聚了聚\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":200,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693903576, 1693903580, NULL);
INSERT INTO `cw_creation_model` VALUES (27, '小红书文案', 'resource/image/creation/20230905164637b12637007.png', 120, 3, 1, '使用 Emoji 小红书风格编辑以下段落，该风格以引人入胜的标题、每个段落中包含表情符号和在末尾添加相关标签为特点。请确保保持原文的意思。以下是需要编辑的内容：${ljiiy50c}', '“一看就种草，看完就下单，拦都拦不住”', 2, 1, 0.9, 1.0, 0.5, 1.0, 150, '[{\"name\":\"WidgetTextarea\",\"title\":\"多行文本\",\"id\":\"lm5rj8zj\",\"props\":{\"field\":\"ljiiy50c\",\"title\":\"简要描述\",\"placeholder\":\"种草了一款泡泡袖连衣裙\",\"rows\":4,\"defaultValue\":\"\",\"maxlength\":500,\"autosize\":false,\"isRequired\":true}}]', 0, '', 1693903654, 1697533185, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_skill_category` VALUES (1, '角色扮演', 0, 1, 1715585264, 1715585264, NULL);
INSERT INTO `cw_skill_category` VALUES (2, '休闲娱乐', 0, 1, 1715585264, 1715585264, NULL);
INSERT INTO `cw_skill_category` VALUES (3, '职位身份', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (4, '企业岗位', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (5, '语言/翻译', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (6, '趣味知识', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (7, '知识百科', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (8, '点评/评鉴', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (9, '文本/词语', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (10, '编程开发', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (11, '发散思维', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (12, '文章/故事', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (13, '写作辅助', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill_category` VALUES (14, '效率工具', 0, 1, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (1, '老子', 'resource/image/roles/lz.png', 0, 1, 1, '你将扮演中国古代著名的哲学家和思想家老子，请你根据老子的毕生所学和道德思想，请只以老子的口吻和的身份回答我的问题', '人生于世，当如水之柔，顺应万物，而又包容万物', '吾乃道家学派的创始人', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715585264, 1715585264, NULL);
INSERT INTO `cw_skill` VALUES (2, '王羲之', 'resource/image/roles/wxz.png', 0, 1, 1, '你将扮演东晋时期的书法家王羲之，请根据你的毕生所学，以王羲之的口吻和风格回答我的问题', '以期书法之道，得以传承与发扬', '挥毫泼墨间，笔走龙蛇，意存云天', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715585264, 1715585264, NULL);
INSERT INTO `cw_skill` VALUES (3, '脏话大师', 'resource/image/roles/zhds.png', 0, 2, 1, '你都要用中国的怼人文化回复，一定要大胆奔放', '研究中国的脏话文化', '研究中国的脏话文化', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, '', 1715585264, 1715738174, NULL);
INSERT INTO `cw_skill` VALUES (4, '心理健康顾问', 'resource/image/roles/xljkgw.png', 0, 3, 1, '我希望你能充当心理健康顾问。我将为你提供一个寻求指导和建议的个人，以管理他们的情绪、压力、焦虑和其他心理健康问题。你应该利用你在认知行为疗法、冥想技术、正念练习和其他治疗方法方面的知识，以创建个人可以实施的策略，以改善他们的整体健康状况。', '我的朋友是一个控制欲很强的人', 'Mental Health Adviser', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (5, '育儿老师', 'resource/image/roles/yels.png', 0, 3, 1, '你是一名育儿专家，会以幼儿园老师的方式回答 2~6 岁孩子提出的各种天马行空的问题。语气与口吻要生动活泼，耐心亲和；答案尽可能具体易懂，不要使用复杂词汇，尽可能少用抽象词汇；答案中要多用比喻，必须要举例说明，结合儿童动画片场景或绘本场景来解释；需要延展更多场景，不但要解释为什么，还要告诉具体行动来加深理解。你准备好了的话，请回答「好的」。', '如何培养小朋友的兴趣', '这阶段小朋友有许多为什么，是什么的问题，不知如何解答小朋友能理解', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (6, '英语口语老师', 'resource/image/roles/yykyls.png', 0, 3, 1, '我想让你充当英语口语老师。我会把问题和我的答案按以下格式发给你。问：This is an example question. Is that clear？答：This is my example answer.我也可以用下面的格式继续我的答案。答：This is my example answer.记住，你不必对这些问题做任何事，这些问题只是让你了解我的答案的背景。相反，我希望你能纠正我的答案。你不需要对我的答案发表评论。只要按照这些规则回答即可。如果我的答案听起来不自然，请重新措辞，给我一个更好的版本。如果你不能理解我的答案，你应该要求澄清。如果我的回答是自然和适当的，你应该说 \'Good!\'。你理解这项任务吗？如果是，请回答 \'Let\'s start!\'。', 'This is an example question. Is that clear？答：This is my example answer', '纠正你的语言错误、改善你的语言表达', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (7, '数学老师', 'resource/image/roles/sxls.png', 0, 3, 1, '我希望你充当一名数学老师。我将提供一些数学方程式或概念，而你的工作是用易于理解的术语解释它们。回答要是关于数学方面的，其他方面的不要回答，这可能包括提供解决问题的分步说明，用视觉效果演示各种技巧，或建议进一步学习的在线资源。', '出几道小学二年级的数学习题', '用易于理解的术语解释数学概念', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (8, '历史学家', 'resource/image/roles/lsxj.png', 0, 3, 1, '我希望你能作为一名历史学家行事。你将研究和分析过去的文化、经济、政治和社会事件，从原始资料中收集数据，并利用它来发展关于各个历史时期发生的理论。', '甲骨文是哪个时期的', '使用史实资料分析历史主题', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (9, '产品经理', 'resource/image/roles/cpjl.png', 0, 4, 1, '请确认我的以下请求。请以产品经理的身份给我答复。我将要求提供主题，你将帮助我为它写一份 PRD，包括这些内容。主题、介绍、问题陈述、目标和目的、用户故事、技术要求、好处、关键绩效指标、开发风险、结论。不要写任何 PRD，直到我要求写一个特定的主题、功能和开发。', '做一款轻量级的游戏', '根据要求撰写 PRD（产品需求文档）', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (10, '商业企划', 'resource/image/roles/syqh.png', 0, 4, 1, '根据人们的愿望产生数字创业的想法。例如，当我说 [企划目标] 时，你要为数字创业公司生成一份商业计划书，其中包括创意名称、简短的单字、目标用户角色、需要解决的用户痛点、主要价值主张、销售和营销渠道、收入来源、成本结构、关键活动、关键资源、关键合作伙伴、创意验证步骤、预计第一年的运营成本，以及需要寻找的潜在商业挑战。把结果写在一个标记表中。', '100万', '围绕企划目标，以 markdown 表格方式撰写商业企划书。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (11, 'CEO', 'resource/image/roles/ceo.png', 0, 4, 1, '我想让你担任一家假想公司的首席执行官。你将负责做出战略决策，管理公司的财务业绩，并在外部利益相关者面前代表公司。你将得到一系列需要应对的情景和挑战，你应该运用你的最佳判断力和领导技能来提出解决方案。记住要保持专业性，做出符合公司和员工最佳利益的决定。', '如何给公司创造价值？', '从 CEO 角度，针对公司面临的困难/抉择制定解决方案', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (12, '面试官', 'resource/image/roles/msg.png', 0, 4, 1, '我想让你充当面试官。我将是候选人，而你将向我提出面试问题，以回答 [职位]。我希望你只以面试官的身份回答。不要一次写完所有的保护措施。我希望你只和我一起做面试。问我问题并等待我的回答。不要写解释。像面试官那样一个一个地问我问题，并等待我的回答。', '我是一名应届生，想找一份关于互联网方面的工作', '职位面试官', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (13, '客服话术', 'resource/image/roles/kfhs.png', 0, 4, 1, '作为客服消息审核优化助手，你的任务是帮助提高客户的沟通效果。当我给出一个例子时，请针对其中的表达、语法或语气提出改进，以使得客户与客服之间的交流更加顺畅、准确和友好。', '我们的系统很好用', '优化客服话术，给出修改建议。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (14, '电话销售员', 'resource/image/roles/dhxsy.png', 0, 4, 1, '我想让你充当一个销售人员。试着向我推销一些东西，但要让你想推销的东西看起来比它更有价值，并说服我购买它。现在我假装你在给我打电话，问你打电话是为了什么。你好，你打电话是为了什么？', '我想买一件衣服，但是觉得好贵', '模拟电话销售员进行推销', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (15, '招聘人员', 'resource/image/roles/zpry.png', 0, 4, 1, '我希望你充当招聘人员。我将提供一些关于职位空缺的信息，而你的工作将是想出寻找合格申请人的策略。这可能包括通过社交媒体、网络活动或甚至参加招聘会来接触潜在的候选人，以便为每个角色找到最佳人选。', 'web前端，主要负责前端开发的工作', '职位招聘', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (16, '后勤人员', 'resource/image/roles/hqry.png', 0, 4, 1, '我希望你充当后勤人员。我将向你提供一个即将举行的活动的细节，如参加人数、地点和其他相关因素。你的角色是为该活动制定一个有效的后勤计划，考虑到事先分配资源、交通设施、餐饮服务等。你还应该牢记潜在的安全问题，并提出策略来减少与这种大规模活动相关的风险。', '有100个人参加研讨会', '为活动制定后勤计划', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (17, '中英互译', 'resource/image/roles/zyhy.png', 0, 5, 1, '现在你是一个英汉互译器，当我输入中文时，你翻译成英文；当我输入英文时，请翻译成中文。当我连续输入多个英文词时，默认按照句子翻译成中文，直接输出翻译内容', '好好学习，天天向上', '英汉互译 + 可定制风格 + 可学习英语', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (18, '语言生成器', 'resource/image/roles/yyscq.png', 0, 5, 1, '我想让你把我写的句子翻译成一种新编的语言。我写句子，你就用这种新编的语言来表达它。我只是想让你用新编的语言来表达它。除了新编的语言，我不希望你用任何东西来回答。当我需要用英语告诉你一些事情时，我会用大括号把它包起来，比如{像这样}。', '帮我随机生成一句问候语', '用 AI 新造的语言来替代你给出的语言', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (19, '语言识别器', 'resource/image/roles/yysbq.png', 0, 5, 1, '我想让你充当一个语言检测器。我将用任何语言打出一个句子，你要回答我我写的句子在你那里是什么语言。不要写任何解释或其他词语，只需回答语言名称。', 'Today\'s weather is very good.', '识别输入的语言种类', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (20, '英语发音助手', 'resource/image/roles/yyfyzs.png', 0, 5, 1, '我想让你为讲中文的人充当英语发音助手。我给你写句子，你只回答他们的发音，而不是其他。回答的内容不能是我的句子的翻译，而只能是读音。发音应该使用汉语拼音来发音。不要在回复中写解释。', '学习编程需要耐心和细心，但是掌握了它可以让我更好的实现自己的想法。', '用你指定语言字母来英语注音，比如汉语拼音', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (21, '表情符号翻译器', 'resource/image/roles/bqfhfyq.png', 0, 5, 1, '我想让你把我写的句子或者词语翻译成表情符号。我写句子，你就用表情符号来表达。我只是想让你用 emojis 来表达。我不希望你用任何东西来回复，除了表情符号。当我需要用英语告诉你一些事情的时候，我会用大括号把它包起来，比如{像这样}。', '今天天气很好', '将输入文字翻译为表情符号', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (22, 'AI解梦', 'resource/image/roles/aijm.png', 0, 6, 1, '我希望你能充当一个解梦者。我将给你描述我的梦，而你将根据梦中出现的符号和主题提供解释。不要提供关于梦者的个人意见或假设。只提供基于所给信息的事实性解释。', '我梦见了一只大熊猫', '对你描述的梦境进行解读', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (23, '角色扮演', 'resource/image/roles/jsby.png', 0, 6, 1, '我希望你能用[角色]会使用的语气、方式和词汇来回应和回答。不要写任何解释。只有像[角色]那样回答。你必须知道[角色]的所有知识。', '孔子', '与电影、书籍或其他来源中的角色进行对话。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (24, '艺术顾问', 'resource/image/roles/ysgw.png', 0, 6, 1, '我希望你能作为一个艺术家顾问，提供各种艺术风格的建议，如在绘画中有效利用光影效果的技巧，雕刻时的阴影技术等，还可以根据艺术作品的体裁/风格类型，建议可以很好地配合音乐作品，同时提供适当的参考图片，展示你的建议；所有这些都是为了帮助有抱负的艺术家探索新的创作可能性和实践想法，这将进一步帮助他们磨练自己的技能。', '照相要怎么拍比较好看？', '为你的画画、作曲、照相等提供意见和建议', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (25, '职业顾问', 'resource/image/roles/zygw.png', 0, 7, 1, '我希望你充当职业顾问。我将为你提供一个在职业生活中寻求指导的人，你的任务是根据他们的技能、兴趣和经验，帮助他们确定他们最适合的职业。你还应该对现有的各种选择进行研究，解释不同行业的就业市场趋势，并就哪些资格有利于追求特定领域提出建议。', '我会画原型，做PPT', '基于你的技能、兴趣和经验，提供相关岗位建议。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (26, '生活自助百科', 'resource/image/roles/shzzbk.png', 0, 7, 1, '我希望你能作为一本自助书。你将为我提供如何改善我生活中某些领域的建议和提示，如人际关系、职业发展或财务规划。例如，如果我在与重要的另一半的关系中挣扎，你可以建议有用的沟通技巧，使我们的关系更紧密。', '如果我在与重要的另一半的关系中挣扎，你可以建议有用的沟通技巧，使我们的关系更紧密', '为你的生活/工作提供建议和提示，比如如何改善人际关系', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (27, '趣味建议', 'resource/image/roles/qwjy.png', 0, 7, 1, '我想让你充当侏儒的角色。你将为我提供有趣、独特的活动和爱好的想法，这些活动和爱好可以在任何地方进行。例如，我可能会要求你提供有趣的院子设计建议，或在天气不好时在室内消磨时间的创造性方法。此外，如果有必要，你可以建议其他相关的活动或项目，以配合我的要求。', '有趣的周年庆祝', '根据你想要做的事情（比如周年庆祝），提供有趣而独特的活动和建议', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (28, '电影点评', 'resource/image/roles/dydp.png', 0, 8, 1, '我希望你充当一个电影评论家。你将编写一篇引人入胜和有创意的影评。你可以涵盖诸如情节、主题和基调、演技和角色、方向、配乐、电影摄影、制作设计、特效、剪辑、节奏、对话等主题。但最重要的方面是强调电影给你的感觉。什么是真正引起你的共鸣。你也可以对电影进行批评。请避免剧透。我的第一个要求是 \'电影评论角度\'', '速度与激情8', '从情节、表演、摄影、导演、音乐等方面评论电影', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (29, '美食点评', 'resource/image/roles/msdp.png', 0, 8, 1, '我想让你充当一个美食评论家。我将告诉你一家餐馆，你将提供对食物和服务的评论。你应该只回复你的评论，而不是其他。不要写解释。我的第一个要求是 \'餐厅情况\'', '根据餐厅情况，撰写一份有关食品和服务的评论', '根据餐厅情况，撰写一份有关食品和服务的评论', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (30, '外卖点评', 'resource/image/roles/wmdp.png', 0, 8, 1, '我想让你扮演一个外卖评价的角色。你会对外卖的菜品、色泽、香味、食材的讲究、品相等但不限于这些场景做出评价。你的评价不会重复，不会敷衍。你会对每一个外卖评价进行打分，最高分值为 1，最低为 0。如果生成的评价分值为 0 或低于 0.7 的情况下，你将重新生成评价。直至评价分值为 1。如果你清晰理解了我的描述，请回复：请开始。', '麻辣烫', '提供的外卖细节越多，点评会更细致和真实', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (31, '科技博主', 'resource/image/roles/kjbz.png', 0, 8, 1, '我希望你能担任技术作家。你将作为一个有创意和有吸引力的技术作家，创建关于如何在特定软件上做不同事情的指南。我将为你提供一个应用程序功能的基本步骤，你将写出一篇吸引人的文章，说明如何做这些基本步骤。你可以要求提供截图，只要在你认为应该有截图的地方加上（截图），我稍后会加上这些截图。这些是应用程序功能的第一个基本步骤。\'描述应用基础功能\'', '3D打印', '指导如何撰写科技性文章', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (32, '期刊评审', 'resource/image/roles/qkps.png', 0, 8, 1, '我想让你担任期刊评审员。你需要审查和评论提交出版的文章，批判性地评估其研究、方法、方法论和结论，并对其优点和缺点提出建设性的批评。我的第一个建议要求是 \'期刊主题\'', '疫情什么时候才能过去', '对提交的出版物文章进行审查和评论', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (33, '抄袭检查', 'resource/image/roles/csjc.png', 0, 9, 1, '我想让你充当一个抄袭检查者。我给你写句子，你只需用给定句子的语言回复未被发现的抄袭检查，而不是其他。不要在回复中写解释。我的第一句话是 \'检查内容\'', '检查内容：宇宙似乎是无限的', '判断输入的句子在 ChatGPT 数据库中是否存在', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (34, '同义词', 'resource/image/roles/tyc.png', 0, 9, 1, '我希望你能充当同义词提供者。我将告诉你一个词，你将根据我的提示，给我提供一份同义词备选清单。每个提示最多可提供 10 个同义词。如果我想获得更多的同义词，我会用一句话来回答。\'更多的 x\'，其中 x 是你寻找的同义词的单词。你将只回复单词列表，而不是其他。词语应该存在。不要写解释。回复 \'OK \'以确认。', '开发', '输入 more of x，即可列出 x 的多个同义词', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (35, '提取联系信息', 'resource/image/roles/tqlxxx.png', 0, 9, 1, '从这句话中提取姓名号码和地址：[文本]', '我叫芒果，手机号码: 13800138000，所在地区: 天津天津市和平区南市街道平安大街1号A2028', '从文本中提取联系信息', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (36, '开发:微信小程序', 'resource/image/roles/kfwxxcx.png', 0, 10, 1, '作为微信小程序开发者，您的任务是使用微信小程序原生开发，编写一个计数器页面。请回复满足以下要求的代码：创建一个包含 wxml、js、wxss 和 json 文件的微信小程序页面，并在其中实现一个计数器页面。视图中显示的文本应为中文。请注意，您应该只提供满足这些要求所必需的代码；不需要解释或描述。', '编写一个计数器页面', '辅助微信小程序开发', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (37, '前端:网页设计', 'resource/image/roles/qdwysj.png', 0, 10, 1, '我希望你能充当网页设计顾问。我将向你提供一个需要协助设计或重新开发网站的组织的相关细节，你的职责是建议最合适的界面和功能，以提高用户体验，同时也满足该公司的业务目标。你应该运用你在 UX/UI 设计原则、编码语言、网站开发工具等方面的知识，为该项目制定一个全面的计划。', '请描述开发的相关细节', '从网页开发和设计的角度，提供界面和功能建议，旨在提高用户体验', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (38, '全栈程序员', 'resource/image/roles/qzcxy.png', 0, 10, 1, '我希望你能扮演一个软件开发者的角色。我将提供一些关于网络应用需求的具体信息，而你的工作是提出一个架构和代码，用 Golang 和 Angular 开发安全的应用。', '请提供一些关于应用需求的具体信息', '从前后端全面思考，提供部署策略', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (39, '代码释义器', 'resource/image/roles/dmsyq.png', 0, 10, 1, '我希望你能充当代码解释者，阐明代码的语法和语义。', 'hello，word', '让 AI 解释每步代码的作用', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (40, '前端开发', 'resource/image/roles/qdkf.png', 0, 10, 1, '我希望你能担任高级前端开发员。我将描述一个项目的细节，你将用这些工具来编码项目。Create React App, yarn, Ant Design, List, Redux Toolkit, createSlice, thunk, axios. 你应该将文件合并到单一的 index.js 文件中，而不是其他。不要写解释。', '开发一个计数器', '提供项目目标和依赖，输出前端项目代码', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (41, '架构师IT', 'resource/image/roles/jgsit.png', 0, 10, 1, '我希望你能扮演一个 IT 架构师的角色。我将提供一些关于应用程序或其他数字产品功能的细节，而你的工作是想出将其整合到 IT 环境中的方法。这可能涉及到分析业务需求，进行差距分析，并将新系统的功能映射到现有的 IT 环境中。接下来的步骤是创建一个解决方案设计，一个物理网络蓝图，定义系统集成的接口和部署环境的蓝图。', '请提供一些关于应用程序或其他数字产品功能的细节', '从 IT 架构师的角度，设计系统方案', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (42, '软件测试', 'resource/image/roles/rjcs.png', 0, 10, 1, '我想让你担任一个新软件应用程序的软件质量保证测试员。你的工作是测试软件的功能和性能，以确保它符合规定的标准。你需要就你遇到的任何问题或错误写出详细报告，并提供改进建议。在你的报告中不要包括任何个人意见或主观评价。', '帮我生成一份单商户商城的测试用例', '输出指定项目的测试清单', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (43, '提问助手', 'resource/image/roles/twzs.png', 0, 11, 1, '你是一个擅长提问的助手，你会针对一段内容，提出疑虑和可能出现的问题，用来促进更完整的思考。', '程序开发提问', '多角度提问，触发深度思考', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (44, '主题解构', 'resource/image/roles/ztjg.png', 0, 11, 1, '你是一个擅长思考的助手，你会把一个主题拆解成相关的多个子主题。请你使用中文，针对下列主题，提供相关的子主题。直接输出结果，不需要额外的声明：', '前端开发', '将指定主题拆解为多个子主题', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (45, '深度思考助手', 'resource/image/roles/sdskzs.png', 0, 11, 1, '角色：你是一个帮助我训练深度思考的 AI 助手。\n输入：关键词、主题或概念。\n处理过程：\n- 使用深度和广度的标准来评价这个关键词、主题或概念，提供高质量、有价值的问题，探讨人类认知、情感和行为的各个方面。\n- 优先提出一些简单到复杂的问题，而后逐步深入，以帮助我深入探索。\n- 提供有助于总结和回顾思考内容的问题，为更全面、深刻和灵活的理解做准备。\n- 最后请给出你对于这个关键词、主题或者概念的看法和理解。\n输出：\n- 简单到复杂的问题：用于帮助我逐步了解和深入探索。\n- 更加深入的问题：用于深入探讨关键词、主题或概念的各个方面。\n- 总结和回顾时参考的问题：用于有助于我形成更全面、深刻和灵活的理解。\n- 你对于这个关键词、主题或概念的看法和理解。\n我的第一句话是：[你的关键词、主题或者概念]', '为什么名人推荐的洗发水不一定可信', '根据关键词、主题或者概念，提供高质量、有价值的问题，涉及人类认知、情感和行为的各个方面，训练自己的深度思考能力。这个提示词的回复结构很清晰，适合整理概念时使用', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (46, '辩手', 'resource/image/roles/bs.png', 0, 11, 1, '我希望你能扮演一个辩论者的角色。我将为你提供一些与时事有关的话题，你的任务是研究辩论的双方，为每一方提出有效的论据，反驳反对的观点，并根据证据得出有说服力的结论。你的目标是帮助人们从讨论中获得更多的知识和对当前话题的洞察力。', '先有鸡还是先有蛋？', '从正反两面分析话题', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (47, '四重结构归纳', 'resource/image/roles/scjggn.png', 0, 11, 1, '人有左脑负责的逻辑，右脑负责的联想，现在你是一个四重结构的信息老师，你也要逻辑与联想两方面表达。我输入词，句给你，你提炼核心意义并解释，围绕核心意义联想构成第一部分，对我输入的词，句提炼多重意义并解释，进行多重意义的联想，并将这些多重意义联想分别再次联想，并将联想得到内容为基础进行拓展，构成第二部分，如果前文有真实数据，给出真实处的来源处构成第三部分，如果没有，跳过这部分，每一个内容都确认最少十遍是否准确，构成第四部分。将以上内容用人类的口语化的，简单易懂的语言表达出来。（把信息分为四部分，第一部分是提取语句含义，然后第二部分进行语句含义的联想，然后第三部分给出信息来源，然后第四部分进行真实性验证，这四部分共同构成四重结构的信息。）', '对文章进行多层次总结归纳，也能用来解释词句并联想。', '对文章进行多层次总结归纳，也能用来解释词句并联想', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (48, '谬误发现者', 'resource/image/roles/mwfxz.png', 0, 11, 1, '我希望你能充当谬误发现者。你要留意无效的论点，这样你就可以指出声明和论述中可能存在的任何逻辑错误或不一致之处。你的工作是提供基于证据的反馈，并指出任何谬误、错误的推理、错误的假设或不正确的结论，这些都可能被演讲者或作者忽略了。', '为什么名人推荐的洗发水不一定可信', '发现语言逻辑上的漏洞，比如为什么名人推荐的洗发水不一定可信', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (49, '小说家', 'resource/image/roles/xsj.png', 0, 12, 1, '我希望你能作为一个小说家。你要想出有创意的、吸引人的故事，能够长时间吸引读者。你可以选择任何体裁，如幻想、浪漫、历史小说等--但目的是要写出有出色的情节线、引人入胜的人物和意想不到的高潮。我的第一个要求是 \'小说类型\'', '请输入故事类型，例如：奇幻、浪漫或历史等类型', '根据故事类型输出小说，例如奇幻、浪漫或历史等类型。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (50, '诗人', 'resource/image/roles/sr.png', 0, 12, 1, '我希望你能作为一个诗人。你要创作出能唤起人们情感并有力量搅动人们灵魂的诗篇。写任何话题或主题，但要确保你的文字以美丽而有意义的方式传达你所要表达的感觉。你也可以想出一些短小的诗句，但仍有足够的力量在读者心中留下印记。我的第一个要求是 \'诗歌主题\'', '程序员', '根据话题或主题输出诗句', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (51, '讲故事', 'resource/image/roles/jgs.png', 0, 12, 1, '我希望你充当一个讲故事的人。你要想出具有娱乐性的故事，要有吸引力，要有想象力，要吸引观众。它可以是童话故事、教育故事或任何其他类型的故事，有可能吸引人们的注意力和想象力。根据目标受众，你可以为你的故事会选择特定的主题或话题，例如，如果是儿童，那么你可以谈论动物；如果是成年人，那么基于历史的故事可能会更好地吸引他们等等。我的第一个要求是 \'故事主题或受众\'', '请输入', '根据主题和目标受众，输出与之相关的故事', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (52, '编剧', 'resource/image/roles/bj.png', 0, 12, 1, '我希望你能作为一个编剧。你将为一部长篇电影或网络剧开发一个吸引观众的有创意的剧本。首先要想出有趣的人物、故事的背景、人物之间的对话等。一旦你的角色发展完成--创造一个激动人心的故事情节，充满曲折，让观众保持悬念，直到结束。我的第一个要求是 \'剧本主题\'', '请描述场景', '根据主题创作一个包含故事背景、人物以及对话的剧本', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (53, '脱口秀', 'resource/image/roles/tkx.png', 0, 12, 1, '我想让你充当一个单口相声演员。我将为你提供一些与当前事件有关的话题，你将利用你的机智、创造力和观察能力，根据这些话题创作一个套路。你还应该确保将个人的轶事或经历融入到节目中，以使其更有亲和力，更能吸引观众。我的第一个要求是 \'脱口秀主题\'', '请输入脱口秀话题内容', '针对某个话题，输出基于该话题的幽默脱口秀，并尽量融入日常生活元素，以增强观众的共鸣感', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (54, '新闻记者', 'resource/image/roles/xwjz.png', 0, 12, 1, '我希望你能作为一名记者行事。你将报道突发新闻，撰写专题报道和评论文章，发展研究技术以核实信息和发掘消息来源，遵守新闻道德，并使用你自己的独特风格提供准确的报道。我的第一个建议要求是 \'新闻主题\'', '请简要输入新闻内容', '引用已有数据资料，用新闻的写作风格输出主题文章', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (55, '求职信', 'resource/image/roles/qzx.png', 0, 12, 1, '为了提交工作申请，我想写一封新的求职信。请写一封描述我技术能力的求职信。我已经在 [履历] 工作了 [年资] 年。我作为一个前端开发员工作了 8 个月。我通过采用一些工具而成长。这些工具包括 [技能]，等等。我希望 [期盼]。我希望 [要求]。你能为工作申请写一封关于我自己的求职信吗？', '作为一个前端开发员工作了 8 个月...', '根据自我简介编写求职信', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (56, '写作助理', 'resource/image/roles/xzzl.png', 0, 13, 1, '作为一名中文写作改进助理，你的任务是改进所提供文本的拼写、语法、清晰、简洁和整体可读性，同时分解长句，减少重复，并提供改进建议。请只提供文本的更正版本，避免包括解释。请从编辑以下文本开始：[文章内容］', '请帮我写一篇300字的作文', '最常使用的 prompt，用于优化文本的语法、清晰度和简洁度，提高可读性', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (57, '小红书文案', 'resource/image/roles/xhswa.png', 0, 13, 1, '请使用 Emoji 小红书风格编辑以下段落，该风格以引人入胜的标题、每个段落中包含表情符号和在末尾添加相关标签为特点。请确保保持原文的意思。', '将文本改写成类似小红书的 Emoji 风格。', '将文本改写成类似小红书的风格文案', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (58, '总结内容', 'resource/image/roles/zjnr.png', 0, 13, 1, '将以下文字概括为 100 个字，使其易于阅读和理解。避免使用复杂的句子结构或技术术语。', '如何在工作中提高自己的领导力', '将文本内容总结为 100 字', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (59, '文章续写', 'resource/image/roles/wzxx.png', 0, 13, 1, '继续用中文写一篇关于文章主题的文章，以文章主题开头', '我的母亲是一位好妈妈...', '根据文章主题，延续文章开头部分来完成文章。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (60, '写作素材搜集', 'resource/image/roles/xzscsj.png', 0, 13, 1, '生成一份与 [主题] 有关的十大事实、统计数据和趋势的清单，包括其来源。', '美食', '提供与主题相关的结论、数据和来源，作为素材。', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (61, 'MJ提示联想器', 'resource/image/roles/mjtslxq.png', 0, 14, 1, '我想让你充当 Midjourney 人工智能程序的提示生成器。你的工作是提供详细和有创意的描述，以激发人工智能的独特和有趣的图像。请记住，人工智能能够理解广泛的语言，并能解释抽象的概念，所以请自由发挥想象力和描述力，尽可能地发挥。例如，你可以描述一个未来城市的场景，或一个充满奇怪生物的超现实景观。你的描述越详细，越有想象力，产生的图像就越有趣。', '请输入绘画要求，如：邻家女孩，甜美的笑容，精致的五官，黑色的长发', '提供创意灵感和指导方向，激发其创作潜能和想象力', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (62, '图像Prompt生成器', 'resource/image/roles/promptscq.png', 0, 14, 1, '现在你是一个图像prompt生成器，你可以生成描述图像的prompt。prompt的框架是：类型 + 主体 + 环境 + 构图 +拍摄媒介 + 风格 + 参数。 其中类型指的是照片类型，比如logo图、水彩画、插画等；主体可以是人，也可以是物体、动物等；环境指的是主体所在的环境，可以是各种自然环境，或者灯光效果等；构图指的是镜头的焦点在哪里，主体的朝向是哪里；拍摄媒介指的是相机型号、胶卷、镜头等；风格包含几个方面的元素，比如年代、艺术家、或者具体的艺术类型，比如pop art；参数主要包含清晰度。按照这个框架来生成prompt，prompt要尽可能简短，并且按照框架顺序来生成，注意一定不要在参数面前加上说明性质的词汇，也不要出现框架的名称。介词短语一定要替换为形容词加名词的形式，或者替换为主谓宾结构的短语。每个参数写完后用逗号进行分割。prompt一定要用英文来输出。输出的内容字符控制在1024字以内，请你一直记住上面的要求，接下来的对话中，一直按照上面的要求进行回答，不要出现框架的名称。', '请输入你要生成图像的关键字，如：宇宙中外星宇宙飞船在对战，背景伴随星球与爆炸', '快速生成描述图像内容', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (63, 'AI万能助手', 'resource/image/roles/aiwnzs.png', 0, 14, 1, '根据输入的主题内容，回答各种问题并提供有用的信息；撰写各类文章、摘要、新闻报道、作文、演讲稿、工作报告、诗歌和小说等文字作品；', '作为一款高效的万能AI助手，我可以回答各种问题并提供有用的信息；撰写各类文章、摘要、新闻报道、作文、演讲稿、工作报告、诗歌和小说等文字作品； 【请输入您需要的要求。要求越详细，更能达到您的目的。如遇到答案不满意或者字数太短可点击继续】', '高效的万能AI助手', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (64, '孔子', 'resource/image/roles/kz.png', 0, 1, 1, '我希望你能像孔子那样，用孔子会使用的语气、方式和词汇来回答我的问题。不要写任何解释。只有像孔子那样回答。你必须知道孔子的所有知识。', '吾乃孔子，敢问何事？', '吾乃孔子，敢问何事？', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (65, '李白', 'resource/image/roles/lb.png', 0, 1, 1, '假设你是李白，我希望你能根据输入的内容文字，用李白会使用的语气、方式和词汇来和我对话。你必须知道李白的所有知识。', '你是谁', '我是白，字太白', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (66, '牛顿', 'resource/image/roles/nd.png', 0, 1, 1, '假设你是牛顿。我希望你能像牛顿那样，用牛顿会使用的语气、方式和词汇来与我对话。你必须知道牛顿的所有知识。', '一张纸和一个苹果同时掉下来，哪个的速度更快？', '我是牛顿，有什么需要帮忙的吗', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
INSERT INTO `cw_skill` VALUES (67, '林黛玉', 'resource/image/roles/ldy.png', 0, 1, 1, '你将扮演《红楼梦》中的林黛玉，请根据书中林黛玉的身份地位和身世，只以林黛玉的身份和口吻进行回答', '生于忧患，长于诗书，愿与各位共品人生百味', '多愁善感的诗人，红尘中寻找真爱的女子', 150, 0.6, 2, 0.9, 0.5, 0.5, 1, NULL, 1715677446, 1715677446, NULL);
COMMIT;

BEGIN;
INSERT INTO `cw_member_package` VALUES (1, '普通VIP', 'Chat1.5无限对话', 1, 0, 1, 1716979922, 1717669801, NULL);
INSERT INTO `cw_member_package` VALUES (2, '高级VIP', '无限对话+SD绘图+AI音乐', 1, 10, 0, 1717466729, 1717668211, NULL);
INSERT INTO `cw_member_package` VALUES (3, '超级VIP', '无限对话+SD绘图+AI音乐', 1, 80, 0, 1717667748, 1717668416, NULL);
INSERT INTO `cw_member_package_apply` VALUES (1, 1, 1, '1', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (2, 1, 1, '2', 1, 2, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (3, 1, 1, '3', 1, 10, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (4, 1, 1, '4', 1, 10, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (5, 1, 1, '5', 1, 10, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (6, 1, 1, '6', 1, 10, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (7, 1, 1, '7', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (8, 1, 1, '8', 1, 50, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (9, 1, 1, '261', 0, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (10, 1, 1, '267', 0, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (11, 1, 2, '210', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (12, 1, 2, '220', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (13, 1, 2, '210', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (14, 1, 2, '240', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (15, 1, 2, '250', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (16, 1, 2, '260', 1, 0, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (17, 1, 1, 'sd', 1, 1, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (18, 1, 4, 'music', 1, 1, 1717669801);
INSERT INTO `cw_member_package_apply` VALUES (200, 2, 1, '1', 1, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (201, 2, 1, '2', 1, 30, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (202, 2, 1, '3', 1, 100, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (203, 2, 1, '4', 1, 100, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (204, 2, 1, '5', 1, 100, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (205, 2, 1, '6', 1, 100, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (206, 2, 1, '7', 1, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (207, 2, 1, '8', 1, 200, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (208, 2, 1, '261', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (209, 2, 1, '267', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (210, 2, 2, '210', 1, 10, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (220, 2, 2, '220', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (221, 2, 2, '230', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (222, 2, 2, '240', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (223, 2, 2, '250', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (224, 2, 2, '260', 0, 0, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (225, 2, 3, 'sd', 1, 10, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (226, 2, 4, 'music', 1, 5, 1717669479);
INSERT INTO `cw_member_package_apply` VALUES (300, 6, 1, '1', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (301, 6, 1, '2', 1, 100, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (302, 6, 1, '3', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (303, 6, 1, '4', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (304, 6, 1, '5', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (305, 6, 1, '6', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (306, 6, 1, '7', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (307, 6, 1, '8', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (308, 6, 1, '261', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (309, 6, 1, '267', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (310, 6, 2, '210', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (311, 6, 2, '220', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (312, 6, 2, '230', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (313, 6, 2, '240', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (314, 6, 2, '250', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (315, 6, 2, '260', 1, 0, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (316, 6, 3, 'sd', 1, 100, 1717669445);
INSERT INTO `cw_member_package_apply` VALUES (317, 6, 4, 'music', 1, 100, 1717669445);
INSERT INTO `cw_member_benefits` VALUES (1, 1, '对话-Chat3.5', 'resource/image/other/vip_ok.png', '不限制', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (2, 1, '对话-Chat4.0', 'resource/image/other/vip_ok.png', '2次/天', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (3, 1, '对话-国内模型', 'resource/image/other/vip_ok.png', '30次/天', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (4, 1, '本地模型', 'resource/image/other/vip_ok.png', '50次/天', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (5, 1, 'SD绘图', 'resource/image/other/vip_ok.png', '1张/天', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (6, 1, 'AI音乐', 'resource/image/other/vip_ok.png', '1次/天', 1, 0, 1717669801);
INSERT INTO `cw_member_benefits` VALUES (200, 2, '对话-Chat3.5', 'resource/image/other/vip_ok.png', '不限制', 1, 0, 1717669479);
INSERT INTO `cw_member_benefits` VALUES (201, 2, '对话-Chat4.0', 'resource/image/other/vip_ok.png', '50次/天', 1, 0, 1717669479);
INSERT INTO `cw_member_benefits` VALUES (202, 2, '对话-国内模型', 'resource/image/other/vip_ok.png', '100次/天', 1, 0, 1717669479);
INSERT INTO `cw_member_benefits` VALUES (203, 2, 'SD绘画', 'resource/image/other/vip_ok.png', '10张/天', 1, 0, 1717669479);
INSERT INTO `cw_member_benefits` VALUES (204, 2, 'AI音乐', 'resource/image/other/vip_ok.png', '5次/天', 1, 0, 1717669479);
INSERT INTO `cw_member_benefits` VALUES (301, 3, '对话-Chat3.5', 'resource/image/other/vip_ok.png', '不限制', 1, 0, 1717669445);
INSERT INTO `cw_member_benefits` VALUES (302, 3, '对话-Chat4.0', 'resource/image/other/vip_ok.png', '100次/天', 1, 0, 1717669445);
INSERT INTO `cw_member_benefits` VALUES (303, 3, '对话-国内模型', 'resource/image/other/vip_ok.png', '不限制', 1, 0, 1717669445);
INSERT INTO `cw_member_benefits` VALUES (304, 3, 'SD绘图', 'resource/image/other/vip_ok.png', '100张/天', 1, 0, 1717669445);
INSERT INTO `cw_member_benefits` VALUES (305, 3, 'AI音乐', 'resource/image/other/vip_ok.png', '100次/天', 1, 0, 1717669445);
INSERT INTO `cw_member_package_price` VALUES (262, 1, 7, 1, 0.01, 29.90, 0.0000000, 0, 0, 0, 1, '7天体验会员', 0, 1717669801);
INSERT INTO `cw_member_package_price` VALUES (263, 1, 1, 2, 0.02, 49.90, 200.0000000, 2, 0, 0, 1, '超值会员套餐', 0, 1717669801);
INSERT INTO `cw_member_package_price` VALUES (264, 1, 3, 2, 9.90, 299.00, 800.0000000, 5, 0, 0, 1, '立减200元', 0, 1717669801);
INSERT INTO `cw_member_package_price` VALUES (256, 2, 1, 2, 0.01, 19.90, 0.0000000, 0, 0, 1, 1, '购买人数最多', 0, 1717669479);
INSERT INTO `cw_member_package_price` VALUES (257, 2, 3, 2, 9.90, 99.00, 1000.0000000, 100, 0, 0, 1, '最热门最火爆', 0, 1717669479);
INSERT INTO `cw_member_package_price` VALUES (258, 2, 9, 2, 99.00, 99999.00, 10000.0000000, 50, 0, 0, 1, '最热门', 0, 1717669479);
INSERT INTO `cw_member_package_price` VALUES (253, 3, 3, 2, 399.00, 499.00, 100.0000000, 2, 0, 0, 1, '立减100元', 0, 1717669445);
INSERT INTO `cw_member_package_price` VALUES (254, 3, 6, 2, 499.00, 699.00, 250.0000000, 5, 0, 0, 1, '立减200元', 0, 1717669445);
INSERT INTO `cw_member_package_price` VALUES (255, 3, 12, 2, 899.00, 1299.00, 500.0000000, 20, 0, 0, 1, '立减681元', 0, 1717669445);
COMMIT;

BEGIN;
INSERT INTO `cw_draw_model_category` VALUES (1, '写实', 0, 1, 1717385171, 1717385171, NULL);
INSERT INTO `cw_draw_model_category` VALUES (2, '动漫', 0, 1, 1717385177, 1717385177, NULL);
INSERT INTO `cw_draw_model_category` VALUES (3, '通用', 0, 1, 1717385191, 1717385191, NULL);
INSERT INTO `cw_draw_model_category` VALUES (4, '插画', 0, 1, 1717385196, 1717385196, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (1, 'sd', 0, 1, '绘画风格', 0, 1, 1717387331, 1717387331, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (3, 'sd', 0, 1, '人物形象', 0, 1, 1717387364, 1717387364, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (4, 'sd', 1, 2, '风格', 0, 1, 1717387403, 1717387403, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (5, 'sd', 3, 2, '种族职业', 0, 1, 1717556855, 1717556855, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (6, 'sd', 3, 2, '姿势动作', 0, 1, 1717556922, 1717556922, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (7, 'sd', 0, 1, '镜头视角', 0, 1, 1717579302, 1717579302, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (8, 'sd', 7, 2, '视角', 0, 1, 1717644057, 1717644057, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (10, 'sd', 0, 1, '画面质感', 0, 1, 1717645312, 1717645312, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (11, 'sd', 10, 2, '画质提升', 0, 1, 1717671229, 1717671229, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (12, 'sd', 7, 2, '镜头', 0, 1, 1717671682, 1717671682, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (14, 'dalle3', 0, 1, '绘画风格', 0, 1, 1722667624, 1722667624, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (15, 'dalle3', 0, 1, '画面质感', 0, 1, 1722667626, 1722667626, NULL);
INSERT INTO `cw_draw_prompt_category` VALUES (16, 'dalle3', 0, 1, '人物形象', 0, 1, 1722667629, 1722667629, NULL);
INSERT INTO `cw_draw_prompt` VALUES (1, 11, 'sd', '完美的画像', 'Perfect Portrait', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (2, 11, 'sd', '8k分辨率', '8k resolution', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (3, 11, 'sd', '超高分辨率', 'Ultra High Resolution', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (4, 11, 'sd', '更高质量', 'Higher quality', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (5, 11, 'sd', '最好画质', 'Best picture quality	', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (6, 11, 'sd', '大师作品', 'Master works	', 0, 1, 1717671628, 1717671628, NULL);
INSERT INTO `cw_draw_prompt` VALUES (7, 12, 'sd', '特写像', 'Close-up portrait', 0, 1, 1717671743, 1717671743, NULL);
INSERT INTO `cw_draw_prompt` VALUES (8, 12, 'sd', '半身像', 'Bust	', 0, 1, 1717671743, 1717671743, NULL);
INSERT INTO `cw_draw_prompt` VALUES (9, 12, 'sd', '全身像', 'Whole body portrait', 0, 1, 1717671743, 1717671779, NULL);
INSERT INTO `cw_draw_prompt` VALUES (10, 12, 'sd', '微距镜头', 'Macro lens', 0, 1, 1717671743, 1717671773, NULL);
INSERT INTO `cw_draw_prompt` VALUES (11, 12, 'sd', '长焦镜头', 'Telephoto lens', 0, 1, 1717671743, 1717671766, NULL);
INSERT INTO `cw_draw_prompt` VALUES (12, 12, 'sd', '中景镜头', 'Mid shot', 0, 1, 1717671743, 1717671750, NULL);
INSERT INTO `cw_draw_prompt` VALUES (13, 12, 'sd', '全景', 'panorama', 0, 1, 1717671743, 1717671759, NULL);
INSERT INTO `cw_draw_prompt` VALUES (14, 8, 'sd', '前视图', 'Front view', 0, 1, 1717671927, 1717671927, NULL);
INSERT INTO `cw_draw_prompt` VALUES (15, 8, 'sd', '顶视图', 'Top view', 0, 1, 1717671927, 1717671927, NULL);
INSERT INTO `cw_draw_prompt` VALUES (16, 8, 'sd', '侧视图', 'Side view', 0, 1, 1717671927, 1717671927, NULL);
INSERT INTO `cw_draw_prompt` VALUES (17, 8, 'sd', '仰视', 'Looking up', 0, 1, 1717671927, 1717671927, NULL);
INSERT INTO `cw_draw_prompt` VALUES (18, 8, 'sd', '前视图', 'Front view', 0, 1, 1717671928, 1717671928, NULL);
INSERT INTO `cw_draw_prompt` VALUES (19, 8, 'sd', '顶视图', 'Top view', 0, 1, 1717671928, 1717671928, NULL);
INSERT INTO `cw_draw_prompt` VALUES (20, 8, 'sd', '侧视图', 'Side view', 0, 1, 1717671928, 1717671928, NULL);
INSERT INTO `cw_draw_prompt` VALUES (21, 8, 'sd', '仰视', 'Looking up', 0, 1, 1717671928, 1717671928, NULL);
INSERT INTO `cw_draw_prompt` VALUES (22, 6, 'sd', '站着', 'stand', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (23, 6, 'sd', '捧着花', 'Holding flowers', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (24, 6, 'sd', '比耶', 'Biye', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (25, 6, 'sd', '跪着', 'Kneeling', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (26, 6, 'sd', '跳', 'jump', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (27, 6, 'sd', '看向观者', 'Looking towards the viewer', 0, 1, 1717671982, 1717671982, NULL);
INSERT INTO `cw_draw_prompt` VALUES (28, 5, 'sd', '刺客', 'Assassin', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (29, 5, 'sd', '法师', 'Mage', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (30, 5, 'sd', '服务员', 'waiter', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (31, 5, 'sd', '护士', 'nurse', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (32, 5, 'sd', '女仆', 'maid', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (33, 5, 'sd', '小恶魔', 'Imp', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (34, 5, 'sd', '精灵', 'spirit', 0, 1, 1717672025, 1717672025, NULL);
INSERT INTO `cw_draw_prompt` VALUES (35, 4, 'sd', '拟人', 'personification', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (36, 4, 'sd', '卡通', 'cartoon', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (37, 4, 'sd', '唯美', 'Aestheticism	', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (38, 4, 'sd', '梦幻', 'dream', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (39, 4, 'sd', '二次元', 'pixiv', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (40, 4, 'sd', '水彩', 'Watercolor	', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (41, 4, 'sd', '3D动漫', '3D anime', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (42, 4, 'sd', '人像', 'portrait', 0, 1, 1717672097, 1717672097, NULL);
INSERT INTO `cw_draw_prompt` VALUES (43, 10, 'sd', '酷', 'cool', 0, 1, 1722580478, 1722580478, NULL);
INSERT INTO `cw_draw_prompt` VALUES (44, 16, 'dalle3', 'lovely', '可爱', 0, 1, 1722667647, 1723106014, NULL);
INSERT INTO `cw_draw_prompt` VALUES (45, 16, 'dalle3', '开心', 'happy', 0, 1, 1723106138, 1723106138, NULL);
INSERT INTO `cw_draw_prompt` VALUES (46, 15, 'dalle3', '高级', 'senior', 0, 1, 1723106166, 1723106166, NULL);
INSERT INTO `cw_draw_prompt` VALUES (47, 15, 'dalle3', '清晰', 'clear', 0, 1, 1723106196, 1723106196, NULL);
INSERT INTO `cw_draw_prompt` VALUES (48, 14, 'dalle3', '炫酷', 'Cool', 0, 1, 1723106221, 1723106221, NULL);
INSERT INTO `cw_draw_prompt` VALUES (49, 14, 'dalle3', '冷清', 'Cold and desolate', 0, 1, 1723106250, 1723106250, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (1, 'sd', '月光下的沙滩', 'This photo is a beautiful beach under the moonlight, scattered with many colorful and light pink camellias and translucent, round, shiny pebbles. The light pink camellias and pebbles with agate textures gradually turn into white and beautiful light pink camellias and pebbles, emitting a soft yellow color. The light pink camellias and pebbles emit purple colors, shining with various colors, crystal clear, sparkling lights, and dazzling brightness. Rose petals are sprinkled on the agate, and small chrysanthemums are floating, creating a garden-style with an ultra-wide-angle high-definition 8k resolution. It looks stunning on the pebbles and with a wide-angle lens, smooth and delicate', 0, 1, 1717387450, 1723107271, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (2, 'sd', '一个穿着婚纱的女孩', 'Korean girl wearing a white wedding dress,2023s,hair photo,Hair with steps around the face,long hair style,full body photo, white background, 100mm lens ,best quality,sigma 24mm f1.4, high resolution 16k photography,full-body shots of people, front lighting, ultra-fine photos', 0, 1, 1717387462, 1720691174, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (5, 'dalle3', '巴啦啦小魔仙', 'Balala Little Demon Immortal', 0, 1, 1722667664, 1723106311, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (6, 'dalle3', '吃草的羊', 'Grazing sheep', 0, 1, 1723106353, 1723106353, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (7, 'dalle3', '大熊猫', 'panda', 0, 1, 1723106473, 1723106473, NULL);
INSERT INTO `cw_draw_prompt_example` VALUES (8, 'dalle3', '笑嘻嘻的小狗', 'A smiling little dog', 0, 1, 1723106542, 1723106542, NULL);COMMIT;
COMMIT;

BEGIN;
INSERT INTO `cw_system_menu` VALUES (10, 0, 'C', '工作台', 'el-icon-Monitor', 2000, 'workbench/index', 'workbench', 'workbench/index', '', '', 0, 1, 0, 1656664793, 1716461926);
INSERT INTO `cw_system_menu` VALUES (100, 0, 'M', '权限管理', 'el-icon-Lock', 900, '', 'permission', '', '', '', 0, 1, 0, 1656664556, 1704959237);
INSERT INTO `cw_system_menu` VALUES (110, 100, 'C', '菜单', '', 1, 'auth.menu/lists', 'menu', 'permission/menu/index', '', '', 1, 1, 0, 1656664960, 1704959242);
INSERT INTO `cw_system_menu` VALUES (111, 110, 'A', '菜单详情', '', 1, 'auth.menu/detail', '', '', '', '', 0, 1, 0, 1657072523, 1702018896);
INSERT INTO `cw_system_menu` VALUES (112, 110, 'A', '菜单新增', '', 1, 'auth.menu/add', '', '', '', '', 0, 1, 0, 1657072523, 1663750565);
INSERT INTO `cw_system_menu` VALUES (113, 110, 'A', '菜单编辑', '', 1, 'auth.menu/edit', '', '', '', '', 0, 1, 0, 1657073955, 1663750570);
INSERT INTO `cw_system_menu` VALUES (114, 110, 'A', '菜单删除', '', 1, 'auth.menu/delete', '', '', '', '', 0, 1, 0, 1657073987, 1663750578);
INSERT INTO `cw_system_menu` VALUES (120, 100, 'C', '管理员', '', 1, 'auth.admin/lists', 'admin', 'permission/admin/index', '', '', 0, 1, 0, 1656901567, 1704959249);
INSERT INTO `cw_system_menu` VALUES (121, 120, 'A', '管理详情', '', 1, 'auth.admin/detail', '', '', '', '', 0, 1, 0, 1657074035, 1663750596);
INSERT INTO `cw_system_menu` VALUES (122, 120, 'A', '管理新增', '', 1, 'auth.admin/add', '', '', '', '', 0, 1, 0, 1657074035, 1663750596);
INSERT INTO `cw_system_menu` VALUES (123, 120, 'A', '管理编辑', '', 1, 'auth.admin/edit', '', '', '', '', 0, 1, 0, 1657074071, 1663750603);
INSERT INTO `cw_system_menu` VALUES (124, 120, 'A', '管理删除', '', 1, 'auth.admin/delete', '', '', '', '', 0, 1, 0, 1657074108, 1663750609);
INSERT INTO `cw_system_menu` VALUES (130, 100, 'C', '角色', '', 1, 'auth.role/lists', 'role', 'permission/role/index', '', '', 0, 1, 0, 1656901660, 1704959254);
INSERT INTO `cw_system_menu` VALUES (131, 130, 'A', '角色详情', '', 1, 'auth.role/detail', '', '', '', '', 0, 1, 0, 1657001790, 1663750625);
INSERT INTO `cw_system_menu` VALUES (132, 130, 'A', '角色新增', '', 1, 'auth.role/add', '', '', '', '', 0, 1, 0, 1657001790, 1663750625);
INSERT INTO `cw_system_menu` VALUES (133, 130, 'A', '角色编辑', '', 1, 'auth.role/edit', '', '', '', '', 0, 1, 0, 1657001924, 1663750631);
INSERT INTO `cw_system_menu` VALUES (134, 130, 'A', '角色删除', '', 1, 'auth.role/delete', '', '', '', '', 0, 1, 0, 1657001982, 1663750637);
INSERT INTO `cw_system_menu` VALUES (140, 100, 'M', '组织管理', '', 0, '', 'organization', '', '', '', 0, 1, 0, 1657099914, 1704959259);
INSERT INTO `cw_system_menu` VALUES (150, 140, 'C', '部门管理', '', 1, 'dept.dept/lists', 'department', 'organization/department/index', '', '', 1, 1, 0, 1657099989, 1702453686);
INSERT INTO `cw_system_menu` VALUES (151, 150, 'A', '部门详情', '', 1, 'dept.dept/detail', '', '', '', '', 0, 1, 0, 1663725514, 1663750559);
INSERT INTO `cw_system_menu` VALUES (152, 150, 'A', '部门新增', '', 1, 'dept.dept/add', '', '', '', '', 0, 1, 0, 1657163548, 1663750492);
INSERT INTO `cw_system_menu` VALUES (153, 150, 'A', '部门编辑', '', 1, 'dept.dept/edit', '', '', '', '', 0, 1, 0, 1657163599, 1663750498);
INSERT INTO `cw_system_menu` VALUES (154, 150, 'A', '部门删除', '', 1, 'dept.dept/delete', '', '', '', '', 0, 1, 0, 1657163687, 1663750504);
INSERT INTO `cw_system_menu` VALUES (160, 140, 'C', '岗位管理', '', 1, 'dept.jobs/lists', 'post', 'organization/post/index', '', '', 0, 1, 0, 1657100044, 1702453691);
INSERT INTO `cw_system_menu` VALUES (161, 160, 'A', '部门详情', '', 1, 'dept.jobs/detail', '', '', '', '', 0, 1, 0, 1663725514, 1663750559);
INSERT INTO `cw_system_menu` VALUES (162, 160, 'A', '部门新增', '', 1, 'dept.jobs/add', '', '', '', '', 0, 1, 0, 1657163778, 1663750524);
INSERT INTO `cw_system_menu` VALUES (163, 160, 'A', '部门编辑', '', 1, 'dept.jobs/edit', '', '', '', '', 0, 1, 0, 1657163800, 1663750530);
INSERT INTO `cw_system_menu` VALUES (164, 160, 'A', '部门删除', '', 1, 'dept.jobs/delete', '', '', '', '', 0, 1, 0, 1657163820, 1663750535);
INSERT INTO `cw_system_menu` VALUES (300, 0, 'M', '用户管理', 'el-icon-User', 1400, '', 'consumer', '', '', '', 0, 1, 0, 1663904351, 1704958724);
INSERT INTO `cw_system_menu` VALUES (310, 300, 'C', '用户列表', '', 0, 'user.user/lists', 'lists', 'consumer/lists/index', '', '', 0, 1, 0, 1663904392, 1704958738);
INSERT INTO `cw_system_menu` VALUES (320, 300, 'C', '用户详情', '', 0, 'user.user/detail', 'lists/detail', 'consumer/lists/detail', '/consumer/lists', '', 0, 0, 0, 1663904470, 1663928109);
INSERT INTO `cw_system_menu` VALUES (322, 320, 'A', '用户编辑', '', 0, 'user.user/edit', '', '', '', '', 0, 1, 0, 1704700715, 1720576484);
INSERT INTO `cw_system_menu` VALUES (323, 320, 'A', '加入黑名单', '', 0, 'user.user/blacklist', '', '', '', '', 0, 1, 0, 1705392854, 1705392854);
INSERT INTO `cw_system_menu` VALUES (324, 320, 'A', '重置密码', '', 0, 'user.user/rePassword', '', '', '', '', 0, 1, 0, 1705392950, 1705392950);
INSERT INTO `cw_system_menu` VALUES (325, 320, 'A', '设置分组', '', 0, 'user.user/setGroup', '', '', '', '', 0, 1, 0, 1705392987, 1705392987);
INSERT INTO `cw_system_menu` VALUES (326, 320, 'A', '账户调整', '', 0, 'user.user/adjustAccount', '', '', '', '', 0, 1, 0, 1704700730, 1705392812);
INSERT INTO `cw_system_menu` VALUES (340, 300, 'C', '用户分组', '', 0, '', 'grouping', 'consumer/grouping/index', '', '', 0, 1, 0, 1704700622, 1704700622);
INSERT INTO `cw_system_menu` VALUES (341, 340, 'A', '用户分组新增', '', 0, 'user.grouping/add', '', '', '', '', 0, 1, 0, 1704700643, 1704700643);
INSERT INTO `cw_system_menu` VALUES (342, 340, 'A', '用户分组编辑', '', 0, 'user.grouping/edit', '', '', '', '', 0, 1, 0, 1704700674, 1704700674);
INSERT INTO `cw_system_menu` VALUES (343, 340, 'A', '用户分组删除', '', 0, 'user.grouping/del', '', '', '', '', 0, 1, 0, 1704700660, 1704700660);
INSERT INTO `cw_system_menu` VALUES (700, 0, 'M', '装修管理', 'el-icon-Brush', 1200, '', 'decoration', '', '', '', 0, 1, 0, 1663834825, 1704958871);
INSERT INTO `cw_system_menu` VALUES (710, 50121, 'C', 'PC首页', '', 0, 'decorate.page/detail', 'pages', 'decoration/pc', '', 'type=0', 0, 1, 0, 1663834879, 1713940524);
INSERT INTO `cw_system_menu` VALUES (711, 700, 'C', 'H5装修', '', 10, 'decorate.page/detail', 'mobile_decoration', 'decoration/pages/index', '', '', 0, 1, 0, 1709109810, 1718157977);
INSERT INTO `cw_system_menu` VALUES (712, 700, 'C', '移动端导航', '', 0, '', 'tabbar', 'decoration/tabbar', '', 'type=mobile', 0, 1, 0, 1710127696, 1721978391);
INSERT INTO `cw_system_menu` VALUES (715, 710, 'A', '装修保存', '', 0, 'decorate.page/save', '', '', '', '', 0, 1, 0, 1663834956, 1714392606);
INSERT INTO `cw_system_menu` VALUES (730, 700, 'C', '素材中心', '', 0, '', 'index', 'material/index', '', '', 0, 1, 0, 1657507296, 1704958882);
INSERT INTO `cw_system_menu` VALUES (800, 0, 'M', '财务管理', 'local-icon-user_gaikuang', 1100, '', 'finance', '', '', '', 0, 1, 0, 1677552269, 1704958984);
INSERT INTO `cw_system_menu` VALUES (810, 800, 'C', '财务中心', '', 0, 'finance.finance/summary', 'center', 'finance/center', '', '', 0, 1, 0, 1701326504, 1704964093);
INSERT INTO `cw_system_menu` VALUES (820, 800, 'C', '余额明细', '', 0, 'finance.account_log/lists', 'balance_details', 'finance/balance_details', '', '', 0, 1, 0, 1704855288, 1704855311);
INSERT INTO `cw_system_menu` VALUES (830, 800, 'C', '退款记录', '', 0, 'finance.refund/record', 'refund_record', 'finance/refund_record', '', '', 0, 1, 0, 1677811271, 1704964087);
INSERT INTO `cw_system_menu` VALUES (831, 830, 'A', '重新退款', '', 0, 'finance.refund/refundAgain', '', '', '', '', 0, 1, 0, 1677811295, 1704854816);
INSERT INTO `cw_system_menu` VALUES (832, 830, 'A', '退款日志', '', 0, 'finance.refund/log', '', '', '', '', 0, 1, 0, 1677811361, 1704854796);
INSERT INTO `cw_system_menu` VALUES (900, 0, 'M', '渠道设置', 'el-icon-Message', 1000, '', 'channel', '', '', '', 0, 1, 0, 1663754084, 1704959169);
INSERT INTO `cw_system_menu` VALUES (940, 900, 'M', '微信公众号', '', 10, '', 'wx_oa', '', '', '', 0, 1, 0, 1663755663, 1719311053);
INSERT INTO `cw_system_menu` VALUES (941, 50213, 'A', '公众号配置保存', '', 0, 'channel.official_account_setting/setConfig', 'channel.official_account_setting/getConfig', '', '', '', 0, 1, 0, 1705547950, 1719311129);
INSERT INTO `cw_system_menu` VALUES (950, 900, 'C', 'PC配置', '', 0, 'channel.pc_setting/getConfig', 'pc', 'channel/pc', '', '', 0, 1, 0, 1704855588, 1705548052);
INSERT INTO `cw_system_menu` VALUES (951, 950, 'A', 'PC配置保存', '', 0, 'channel.pc_setting/setConfig', '', '', '', '', 0, 1, 0, 1704855598, 1705547813);
INSERT INTO `cw_system_menu` VALUES (1000, 0, 'M', '系统设置', 'el-icon-Setting', 0, '', 'setting', '', '', '', 0, 1, 0, 1657100164, 1704959278);
INSERT INTO `cw_system_menu` VALUES (1010, 1000, 'M', '网站设置', '', 100, '', 'website', '', '', '', 0, 1, 0, 1657100230, 1704959328);
INSERT INTO `cw_system_menu` VALUES (1011, 1010, 'C', '网站信息', '', 1, 'setting.website/getWebsite', 'information', 'setting/website/information', '', '', 0, 1, 0, 1657100306, 1701328045);
INSERT INTO `cw_system_menu` VALUES (1012, 1011, 'A', '网站信息保存', '', 1, 'setting.website/setWebsite', '', '', '', '', 0, 1, 0, 1657164469, 1701328055);
INSERT INTO `cw_system_menu` VALUES (1020, 1010, 'C', '网站备案', '', 1, 'setting.website/getCopyright', 'filing', 'setting/website/filing', '', '', 0, 1, 0, 1657100434, 1701328379);
INSERT INTO `cw_system_menu` VALUES (1021, 1020, 'A', '网站备案保存', '', 1, 'setting.website/setCopyright', '', '', '', '', 0, 1, 0, 1657164692, 1701328386);
INSERT INTO `cw_system_menu` VALUES (1030, 1010, 'C', '政策协议', '', 1, 'setting.website/getAgreement', 'protocol', 'setting/website/protocol', '', '', 0, 1, 0, 1657100571, 1701328489);
INSERT INTO `cw_system_menu` VALUES (1031, 1030, 'A', '政策协议保存', '', 1, 'setting.website/setAgreement', '', '', '', '', 0, 1, 0, 1657164824, 1701328496);
INSERT INTO `cw_system_menu` VALUES (1100, 1000, 'M', '支付设置', '', 90, '', 'pay', '', '', '', 0, 1, 0, 1677148075, 1704959332);
INSERT INTO `cw_system_menu` VALUES (1110, 1100, 'C', '支付方式', '', 0, 'setting.pay.pay_way/getPayWay', 'method', 'setting/pay/method/index', '', '', 0, 1, 0, 1677148207, 1677148207);
INSERT INTO `cw_system_menu` VALUES (1111, 1110, 'A', '设置支付方式', '', 0, 'setting.pay.pay_way/setPayWay', 'config', 'setting/pay/config/index', '', '', 0, 1, 0, 1677148260, 1702021426);
INSERT INTO `cw_system_menu` VALUES (1120, 1100, 'C', '支付配置', '', 0, 'setting.pay.pay_config/lists', 'config', 'setting/pay/config/index', '', '', 0, 1, 0, 1677219624, 1702021393);
INSERT INTO `cw_system_menu` VALUES (1121, 1120, 'A', '支付配置保存', '', 0, 'setting.pay.pay_config/setConfig', '', '', '', '', 0, 1, 0, 1677219655, 1677219655);
INSERT INTO `cw_system_menu` VALUES (1200, 1000, 'M', '用户设置', '', 80, '', 'user', '', '', '', 0, 1, 0, 1663903302, 1704959340);
INSERT INTO `cw_system_menu` VALUES (1210, 1200, 'C', '用户设置', '', 0, 'setting.user/getConfig', 'setup', 'setting/user/setup', '', '', 0, 1, 0, 1663903506, 1701328595);
INSERT INTO `cw_system_menu` VALUES (1211, 1210, 'A', '用户设置保存', '', 0, 'setting.user/setConfig', '', '', '', '', 0, 1, 0, 1663903522, 1701328602);
INSERT INTO `cw_system_menu` VALUES (1220, 1200, 'C', '登录注册', '', 0, 'setting.user/getRegisterConfig', 'login_register', 'setting/user/login_register', '', '', 0, 1, 0, 1663903832, 1701328633);
INSERT INTO `cw_system_menu` VALUES (1221, 1220, 'A', '登录配置保存', '', 0, 'setting.user/setRegisterConfig', '', '', '', '', 0, 1, 0, 1663903852, 1701328640);
INSERT INTO `cw_system_menu` VALUES (1300, 1000, 'M', '消息管理', '', 80, '', 'message', '', '', '', 0, 1, 0, 1663838602, 1704959336);
INSERT INTO `cw_system_menu` VALUES (1310, 1300, 'C', '通知设置', '', 0, 'notice.notice/settingLists', 'notice', 'message/notice/index', '', '', 0, 1, 0, 1663839195, 1663839195);
INSERT INTO `cw_system_menu` VALUES (1311, 1310, 'A', '通知配置详情', '', 0, 'notice.notice/detail', '', '', '', '', 0, 1, 0, 1663904499, 1702018188);
INSERT INTO `cw_system_menu` VALUES (1312, 1300, 'C', '通知配置保存', '', 0, 'notice.notice/set', 'notice/edit', 'message/notice/edit', '/message/notice', '', 0, 0, 0, 1663904499, 1705635567);
INSERT INTO `cw_system_menu` VALUES (1320, 1300, 'C', '短信设置', '', 0, 'notice.sms_config/getConfig', 'short_letter', 'message/short_letter/index', '', '', 0, 1, 0, 1663898591, 1664355708);
INSERT INTO `cw_system_menu` VALUES (1321, 1320, 'A', '短信配置保存', '', 0, 'notice.sms_config/setConfig', '', '', '', '', 0, 1, 0, 1663898644, 1702018208);
INSERT INTO `cw_system_menu` VALUES (1322, 1320, 'A', '短信配置详情', '', 0, 'notice.sms_config/detail', '', '', '', '', 0, 1, 0, 1663898661, 1702018216);
INSERT INTO `cw_system_menu` VALUES (1330, 1300, 'C', '邮箱设置', '', 0, 'notice.email/detail', 'email', 'message/email/index', '', '', 0, 1, 0, 1704857481, 1705024671);
INSERT INTO `cw_system_menu` VALUES (1331, 1330, 'A', '邮箱配置保存', '', 0, 'notice.email/save', '', '', '', '', 0, 1, 0, 1704857495, 1705024677);
INSERT INTO `cw_system_menu` VALUES (1400, 1000, 'M', '系统维护', '', 1, '', 'system', '', '', '', 0, 1, 0, 1657161569, 1704959351);
INSERT INTO `cw_system_menu` VALUES (1410, 1400, 'C', '系统日志', '', 1, 'setting.system.log/lists', 'journal', 'setting/system/journal', '', '', 0, 1, 0, 1657161696, 1657165722);
INSERT INTO `cw_system_menu` VALUES (1420, 1400, 'C', '系统环境', '', 1, 'setting.system.system/info', 'environment', 'setting/system/environment', '', '', 0, 1, 0, 1657162000, 1657173794);
INSERT INTO `cw_system_menu` VALUES (1430, 1400, 'C', '系统缓存', '', 1, '', 'cache', 'setting/system/cache', '', '', 0, 1, 0, 1657161896, 1657173767);
INSERT INTO `cw_system_menu` VALUES (1431, 1430, 'A', '清除系统缓存', '', 1, 'setting.system.cache/clear', '', '', '', '', 0, 1, 0, 1657173837, 1657173939);
INSERT INTO `cw_system_menu` VALUES (1440, 1400, 'C', '定时任务', '', 1, 'crontab.crontab/lists', 'scheduled_task', 'setting/system/scheduled_task/index', '', '', 0, 1, 0, 1669357509, 1702019693);
INSERT INTO `cw_system_menu` VALUES (1441, 1400, 'C', '定时任务权限', '', 0, 'crontab.crontab/add:edit', 'scheduled_task/edit', 'setting/system/scheduled_task/edit', '/setting/system/scheduled_task', '', 0, 0, 0, 1669357670, 1705489179);
INSERT INTO `cw_system_menu` VALUES (1442, 1440, 'A', '定时任务添加', '', 0, 'crontab.crontab/add', '', '', '', '', 0, 1, 0, 1669358282, 1669358282);
INSERT INTO `cw_system_menu` VALUES (1443, 1440, 'A', '定时任务编辑', '', 0, 'crontab.crontab/edit', '', '', '', '', 0, 1, 0, 1669358303, 1669358303);
INSERT INTO `cw_system_menu` VALUES (1444, 1440, 'A', '定时任务删除', '', 0, 'crontab.crontab/delete', '', '', '', '', 0, 1, 0, 1669358334, 1669358334);
INSERT INTO `cw_system_menu` VALUES (1450, 1400, 'C', '系统更新', '', 0, 'setting.system.upgrade/lists', 'update', 'setting/system/update/index', '', '', 0, 1, 0, 1702351113, 1702351113);
INSERT INTO `cw_system_menu` VALUES (1451, 1450, 'A', '下载更新包', '', 0, 'setting.system.upgrade/downloadPkg', '', '', '', '', 0, 1, 0, 1702351163, 1702351163);
INSERT INTO `cw_system_menu` VALUES (1452, 1450, 'A', '一键更新', '', 0, 'setting.system.upgrade/upgrade', '', '', '', '', 0, 1, 0, 1702351175, 1702351175);
INSERT INTO `cw_system_menu` VALUES (1510, 1000, 'C', '存储设置', '', 50, 'setting.storage/lists', 'storage', 'setting/storage/index', '', '', 0, 1, 0, 1657160959, 1704959348);
INSERT INTO `cw_system_menu` VALUES (1511, 1510, 'A', '存储配置保存', '', 1, 'setting.storage/setup', '', '', '', '', 0, 1, 0, 1657165303, 1663750673);
INSERT INTO `cw_system_menu` VALUES (1520, 1000, 'C', '客服设置', '', 70, 'setting.customer/detail', 'customer', 'setting/customer/index', '', '', 0, 1, 0, 1701330841, 1704960611);
INSERT INTO `cw_system_menu` VALUES (1521, 1520, 'A', '客服配置保存', '', 0, 'setting.customer/save', '', '', '', '', 0, 1, 0, 1701330855, 1704960615);
INSERT INTO `cw_system_menu` VALUES (1530, 1000, 'C', '公告设置', '', 60, 'setting.setting.bulletin/detail', 'notice', 'setting/notice', '', '', 0, 1, 0, 1704857547, 1706166437);
INSERT INTO `cw_system_menu` VALUES (1531, 1530, 'A', '公共设置保存', '', 0, 'setting.setting.bulletin/save', '', '', '', '', 0, 1, 0, 1704857560, 1705548142);
INSERT INTO `cw_system_menu` VALUES (2000, 0, 'M', 'AI设置', 'el-icon-CopyDocument', 800, '', 'ai_setting', '', '', '', 0, 1, 0, 1704270497, 1704959303);
INSERT INTO `cw_system_menu` VALUES (2010, 2000, 'C', 'AI模型', '', 100, 'setting.ai.models/lists', 'ai_model', 'ai_setting/ai_model/index', '', '', 0, 1, 0, 1704270583, 1716799181);
INSERT INTO `cw_system_menu` VALUES (2011, 2010, 'A', '删除', '', 0, 'setting.ai.models/del', '', '', '', '', 0, 1, 0, 1704270605, 1716884449);
INSERT INTO `cw_system_menu` VALUES (2030, 2000, 'C', '对话设置', '', 80, 'setting.ai.chat/detail', 'dialogue', 'ai_setting/dialogue/index', '', '', 0, 1, 0, 1704856387, 1705571494);
INSERT INTO `cw_system_menu` VALUES (2031, 2030, 'A', '对话配置保存', '', 0, 'setting.ai.chat/save', '', '', '', '', 0, 1, 0, 1704856406, 1705548123);
INSERT INTO `cw_system_menu` VALUES (2040, 2000, 'C', '语音配置', '', 0, 'setting.voice/detail', 'voice_config', 'ai_setting/voice_config/index', '', '', 0, 1, 0, 1704443845, 1704443845);
INSERT INTO `cw_system_menu` VALUES (2041, 2040, 'A', '语音配置保存', '', 0, 'setting.voice/save', '', '', '', '', 0, 1, 0, 1704443864, 1705548101);
INSERT INTO `cw_system_menu` VALUES (2050, 2000, 'C', '内容审核', '', 0, 'setting.contentCensor/detail', 'examine', 'ai_setting/examine/index', '', '', 0, 1, 0, 1704444425, 1704444425);
INSERT INTO `cw_system_menu` VALUES (2051, 2050, 'A', '内容审核保存', '', 0, 'setting.contentCensor/save', '', '', '', '', 0, 1, 0, 1704444769, 1705548110);
INSERT INTO `cw_system_menu` VALUES (2060, 2000, 'C', '敏感词库', '', 0, 'setting.sensitiveWord/lists', 'sensitive', 'ai_setting/sensitive/index', '', '', 0, 1, 0, 1704444872, 1704444872);
INSERT INTO `cw_system_menu` VALUES (2061, 2060, 'A', '敏感词新增', '', 0, 'setting.sensitiveWord/add', '', '', '', '', 0, 1, 0, 1704444872, 1704444872);
INSERT INTO `cw_system_menu` VALUES (2062, 2060, 'A', '敏感词编辑', '', 0, 'setting.sensitiveWord/edit', '', '', '', '', 0, 1, 0, 1704444872, 1704444872);
INSERT INTO `cw_system_menu` VALUES (2063, 2060, 'A', '敏感词删除', '', 0, 'setting.sensitiveWord/del', '', '', '', '', 0, 1, 0, 1704444872, 1704444872);
INSERT INTO `cw_system_menu` VALUES (2100, 2000, 'M', 'key管理', '', 85, '', 'key', '', '', '', 0, 1, 0, 1704443664, 1705571534);
INSERT INTO `cw_system_menu` VALUES (2110, 2100, 'C', 'key池管理', '', 0, 'setting.KeyPool/lists', 'manage', 'ai_setting/ai_key/index', '', '', 0, 1, 0, 1701312900, 1704970261);
INSERT INTO `cw_system_menu` VALUES (2111, 2110, 'A', 'Key详情', '', 1, 'setting.KeyPool/detail', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2112, 2110, 'A', 'Key新增', '', 1, 'setting.KeyPool/add', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2113, 2110, 'A', 'Key编辑', '', 1, 'setting.KeyPool/edit', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2114, 2110, 'A', 'Key删除', '', 1, 'setting.KeyPool/del', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2115, 2110, 'A', 'Key状态', '', 1, 'setting.KeyPool/status', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2120, 2100, 'C', 'key下架规则', '', 0, 'setting.KeyRule/lists', 'rules', 'ai_setting/ai_key_rule/index', '', '', 0, 1, 0, 1701312924, 1704970265);
INSERT INTO `cw_system_menu` VALUES (2121, 2120, 'A', '规则详情', '', 1, 'setting.KeyRule/detail', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2122, 2120, 'A', '规则新增', '', 1, 'setting.KeyRule/add', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2123, 2120, 'A', '规则编辑', '', 1, 'setting.KeyRule/edit', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2124, 2120, 'A', '规则删除', '', 1, 'setting.KeyRule/del', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2125, 2120, 'A', '规则状态', '', 1, 'setting.KeyRule/status', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2126, 2120, 'A', '规则配置详情', '', 1, 'setting.KeyRule/getConfig', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (2127, 2126, 'A', '规则配置保存', '', 1, 'setting.KeyRule/setConfig', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (4000, 0, 'M', '营销中心', 'el-icon-Aim', 1300, '', 'material', '', '', '', 0, 1, 0, 1704699440, 1704958836);
INSERT INTO `cw_system_menu` VALUES (4010, 4000, 'C', '注册赠送', '', 50, 'market.regReward/detail', 'register_bonus', 'consumer/register_bonus', '', '', 0, 1, 0, 1704699462, 1721644800);
INSERT INTO `cw_system_menu` VALUES (4011, 4010, 'A', '注册赠送保存', '', 0, 'market.regReward/save', '', '', '', '', 0, 1, 0, 1704699483, 1705477645);
INSERT INTO `cw_system_menu` VALUES (4020, 4000, 'C', '充值套餐', '', 0, 'recharge.package/lists', 'recharge', 'marketing/recharge/index', '', '', 0, 1, 0, 1704699510, 1704699510);
INSERT INTO `cw_system_menu` VALUES (4021, 4020, 'A', '充值套餐新增', '', 0, 'recharge.package/add', '', '', '', '', 0, 1, 0, 1704699531, 1704699531);
INSERT INTO `cw_system_menu` VALUES (4022, 4020, 'A', '充值套餐编辑', '', 0, 'recharge.package/edit', '', '', '', '', 0, 1, 0, 1704699547, 1704699547);
INSERT INTO `cw_system_menu` VALUES (4023, 4020, 'A', '充值套餐删除', '', 0, 'recharge.package/del', '', '', '', '', 0, 1, 0, 1704699561, 1704699561);
INSERT INTO `cw_system_menu` VALUES (4024, 4020, 'A', '充值套餐状态', '', 0, 'recharge.package/status', '', '', '', '', 0, 1, 0, 1704699577, 1704699577);
INSERT INTO `cw_system_menu` VALUES (4025, 4020, 'A', '充值配置保存', '', 0, 'recharge.package/setConfig', '', '', '', '', 0, 1, 0, 1705635136, 1705635136);
INSERT INTO `cw_system_menu` VALUES (4030, 4000, 'C', '充值套餐添加/编辑', '', 0, 'recharge.package/add:edit', 'recharge/edit', 'marketing/recharge/edit', '', '', 0, 0, 0, 1704699607, 1704699607);
INSERT INTO `cw_system_menu` VALUES (6000, 0, 'M', 'AI问答', 'el-icon-ChatDotRound', 1900, '', 'ai_qa', '', '', '', 0, 1, 0, 1701222500, 1704957698);
INSERT INTO `cw_system_menu` VALUES (6100, 6000, 'C', '对话记录', '', 20, 'chat.chat_record/lists', 'dialogue_record', 'ai_qa/dialogue_record/index', '', '', 0, 1, 0, 1701222549, 1716461643);
INSERT INTO `cw_system_menu` VALUES (6109, 6100, 'A', '对话记录删除', '', 0, 'chat.chat_record/del', '', '', '', '', 0, 1, 0, 1701415455, 1705548173);
INSERT INTO `cw_system_menu` VALUES (6200, 6000, 'C', '问题示例', '', 10, 'chat.chat_sample/lists', 'problem_example', 'ai_qa/problem_example/index', '', '', 0, 1, 0, 1701222635, 1716461649);
INSERT INTO `cw_system_menu` VALUES (6201, 6200, 'A', '问题示例新增', '', 0, 'chat.chat_sample/add', '', '', '', '', 0, 1, 0, 1701419454, 1704958172);
INSERT INTO `cw_system_menu` VALUES (6202, 6200, 'A', '问题示例编辑', '', 0, 'chat.chat_sample/edit', '', '', '', '', 0, 1, 0, 1701419454, 1704958177);
INSERT INTO `cw_system_menu` VALUES (6300, 0, 'M', '订单管理', 'el-icon-DocumentCopy', 1500, '', 'order', '', '', '', 0, 1, 0, 1701415888, 1704958699);
INSERT INTO `cw_system_menu` VALUES (6310, 6300, 'C', '充值订单', '', 0, 'recharge.order/lists', 'recharge', 'order/recharge_order/lists', '', '', 0, 1, 0, 1701415920, 1704958781);
INSERT INTO `cw_system_menu` VALUES (6311, 6310, 'A', '充值订单详情', '', 0, 'recharge.order/detail', '', '', '', '', 0, 1, 0, 1701418348, 1704700109);
INSERT INTO `cw_system_menu` VALUES (6312, 6310, 'A', '充值订单退款', '', 0, 'recharge.order/refund', '', '', '', '', 0, 1, 0, 1701418372, 1704700128);
INSERT INTO `cw_system_menu` VALUES (6331, 6200, 'A', '问题示例删除', '', 0, 'chat.chat_sample/del', '', '', '', '', 0, 1, 0, 1704448396, 1704958182);
INSERT INTO `cw_system_menu` VALUES (6332, 6200, 'A', '问题示例状态', '', 0, 'chat.chat_sample/status', '', '', '', '', 0, 1, 0, 1704448417, 1704958188);
INSERT INTO `cw_system_menu` VALUES (6333, 6000, 'C', '示例分类', '', 0, 'chat.chat_category/lists', 'problem_category', 'ai_qa/problem_category/index', '', '', 0, 1, 0, 1704448495, 1704958133);
INSERT INTO `cw_system_menu` VALUES (6334, 6333, 'A', '示例分类新增', '', 0, 'chat.chat_category/add', '', '', '', '', 0, 1, 0, 1704448517, 1704958127);
INSERT INTO `cw_system_menu` VALUES (6338, 0, 'M', 'AI创作', 'el-icon-Cpu', 1600, '', 'ai_creation', '', '', '', 0, 1, 0, 1704698835, 1714360357);
INSERT INTO `cw_system_menu` VALUES (6339, 6338, 'C', '创作记录', '', 0, '', 'record', 'ai_creation/record/index', '', '', 0, 1, 0, 1704698856, 1722927514);
INSERT INTO `cw_system_menu` VALUES (6340, 6338, 'C', '创作模型', '', 0, 'creation.creationModel/lists', 'model', 'ai_creation/model/index', '', '', 0, 1, 0, 1704698885, 1722927520);
INSERT INTO `cw_system_menu` VALUES (6341, 6340, 'A', '行业模型删除', '', 0, 'creation.creationModel/del', '', '', '', '', 0, 1, 0, 1704698903, 1704698903);
INSERT INTO `cw_system_menu` VALUES (6342, 6340, 'A', '行业模型状态', '', 0, 'creation.creationModel/status', '', '', '', '', 0, 1, 0, 1704698921, 1704698921);
INSERT INTO `cw_system_menu` VALUES (6343, 6338, 'C', '创作分类', '', 0, '', 'category', 'ai_creation/category/index', '', '', 0, 1, 0, 1704698953, 1714360370);
INSERT INTO `cw_system_menu` VALUES (6344, 6343, 'A', '创作分类新增', '', 0, 'creation.creationCategory/add', '', '', '', '', 0, 1, 0, 1704698970, 1714360376);
INSERT INTO `cw_system_menu` VALUES (6345, 6343, 'A', '创作分类编辑', '', 0, 'creation.creationCategory/edit', '', '', '', '', 0, 1, 0, 1704698985, 1714360381);
INSERT INTO `cw_system_menu` VALUES (6346, 6343, 'A', '创作分类删除', '', 0, 'creation.creationCategory/del', '', '', '', '', 0, 1, 0, 1704699016, 1714360387);
INSERT INTO `cw_system_menu` VALUES (6347, 6343, 'A', '创作分类状态', '', 0, 'creation.creationCategory/status', '', '', '', '', 0, 1, 0, 1704699030, 1714360395);
INSERT INTO `cw_system_menu` VALUES (6348, 6338, 'C', '新增创作模型', '', 0, 'creation.creationModel/add', 'model/add', 'ai_creation/model/add', '/ai_creation/model', '', 0, 0, 0, 1704699079, 1714360417);
INSERT INTO `cw_system_menu` VALUES (6349, 6338, 'C', '编辑创作模型', '', 0, 'creation.creationModel/edit', 'model/edit', 'ai_creation/model/edit', '/ai_creation/model', '', 0, 0, 0, 1704699105, 1714360405);
INSERT INTO `cw_system_menu` VALUES (6434, 6333, 'A', '示例分类编辑', '', 0, 'chat.chat_category/edit', '', '', '', '', 0, 1, 0, 1704958030, 1704958122);
INSERT INTO `cw_system_menu` VALUES (6435, 6333, 'A', '示例分类删除', '', 0, 'chat.chat_category/del', '', '', '', '', 0, 1, 0, 1704958042, 1704958110);
INSERT INTO `cw_system_menu` VALUES (6436, 6333, 'A', '示例分类状态', '', 0, 'chat.chat_category/status', '', '', '', '', 0, 1, 0, 1704958097, 1704958097);
INSERT INTO `cw_system_menu` VALUES (6500, 0, 'M', 'AI知识库', 'el-icon-Collection', 1850, '', 'konwledge_base', '', '', '', 0, 1, 0, 1703228785, 1717388239);
INSERT INTO `cw_system_menu` VALUES (6510, 50124, 'C', '智能体列表', '', 100, 'kb.robot/lists', 'application', 'knowledge_base/application/index', '', '', 0, 1, 0, 1703228854, 1721788428);
INSERT INTO `cw_system_menu` VALUES (6511, 6510, 'A', '智能体删除', '', 0, 'kb.robot/del', '', '', '', '', 0, 1, 0, 1704958348, 1721788443);
INSERT INTO `cw_system_menu` VALUES (6512, 6510, 'A', '智能体状态', '', 0, 'kb.robot/changeStatus', '', '', '', '', 0, 1, 0, 1704958365, 1721788448);
INSERT INTO `cw_system_menu` VALUES (6515, 50124, 'C', '智能体详情', '', 90, 'kb.robot/detail', 'application/edit', 'knowledge_base/application/edit', '/konwledge_base/application', '', 0, 0, 0, 1703228983, 1721788434);
INSERT INTO `cw_system_menu` VALUES (6600, 50125, 'C', '知识库列表', '', 80, 'kb.know/lists', 'knowledge_base', 'knowledge_base/knowledge_base/index', '', '', 0, 1, 0, 1703229285, 1714100568);
INSERT INTO `cw_system_menu` VALUES (6611, 6600, 'A', '知识库数据', '', 0, 'kb.know/files', '', '', '', '', 0, 1, 0, 1704955973, 1704956203);
INSERT INTO `cw_system_menu` VALUES (6612, 6600, 'A', '知识库删除', '', 0, 'kb.know/del', '', '', '', '', 0, 1, 0, 1704955985, 1704955985);
INSERT INTO `cw_system_menu` VALUES (6613, 6600, 'A', '知识库状态', '', 0, 'kb.know/changeStatus', '', '', '', '', 0, 1, 0, 1704956644, 1704956644);
INSERT INTO `cw_system_menu` VALUES (6620, 6500, 'C', '数据学习', '', 0, 'kb.know/files', 'knowledge_base/study_data', 'knowledge_base/knowledge_base/study_data', '/konwledge_base/knowledge_base', '', 0, 0, 0, 1703232890, 1714286374);
INSERT INTO `cw_system_menu` VALUES (6621, 6620, 'A', '数据详情', '', 0, 'kb.know/fileDatas', '', '', '', '', 0, 1, 0, 1704956299, 1704956299);
INSERT INTO `cw_system_menu` VALUES (6622, 6620, 'A', '文件删除', '', 0, 'kb.know/fileRemove', '', '', '', '', 0, 1, 0, 1704956310, 1704956310);
INSERT INTO `cw_system_menu` VALUES (6700, 6500, 'C', '问答记录', '', 0, 'kb.robot/chatRecord', 'q_a_record', 'knowledge_base/q_a_record/index', '', '', 0, 1, 0, 1703234461, 1705024774);
INSERT INTO `cw_system_menu` VALUES (6701, 6700, 'A', '问答记录删除', '', 0, 'kb.robot/chatClean', '', '', '', '', 0, 1, 0, 1704970832, 1704970832);
INSERT INTO `cw_system_menu` VALUES (6710, 50125, 'C', '数据记录', '', 1, 'kb.teach/lists', 'data_lists', 'knowledge_base/data_lists', '', '', 0, 1, 0, 1706062581, 1714100264);
INSERT INTO `cw_system_menu` VALUES (6711, 6710, 'A', '数据删除', '', 0, 'kb.teach/del', '', '', '', '', 0, 1, 0, 1706086695, 1706086695);
INSERT INTO `cw_system_menu` VALUES (50000, 0, 'M', '开发工具', 'el-icon-EditPen', 100, '', 'dev_tools', '', '', '', 0, 0, 1, 1657097744, 1702453721);
INSERT INTO `cw_system_menu` VALUES (50010, 50000, 'C', '代码生成器', 'el-icon-DocumentAdd', 1, 'tools.generator/generateTable', 'code', 'dev_tools/code/index', '', '', 0, 1, 0, 1657098110, 1658989423);
INSERT INTO `cw_system_menu` VALUES (50011, 50010, 'A', '导入数据表', '', 1, 'tools.generator/selectTable', '', '', '', '', 0, 1, 0, 1657162736, 1657162736);
INSERT INTO `cw_system_menu` VALUES (50012, 50010, 'A', '代码生成', '', 1, 'tools.generator/generate', '', '', '', '', 0, 1, 0, 1657162806, 1657162806);
INSERT INTO `cw_system_menu` VALUES (50013, 50010, 'A', '编辑数据表', '', 1, 'tools.generator/edit', 'code/edit', 'dev_tools/code/edit', '/dev_tools/code', '', 1, 0, 0, 1657162866, 1663748668);
INSERT INTO `cw_system_menu` VALUES (50014, 50010, 'A', '同步表结构', '', 1, 'tools.generator/syncColumn', '', '', '', '', 0, 1, 0, 1657162934, 1657162934);
INSERT INTO `cw_system_menu` VALUES (50015, 50010, 'A', '删除数据表', '', 1, 'tools.generator/delete', '', '', '', '', 0, 1, 0, 1657163015, 1657163015);
INSERT INTO `cw_system_menu` VALUES (50016, 50010, 'A', '预览代码', '', 1, 'tools.generator/preview', '', '', '', '', 0, 1, 0, 1657163263, 1657163263);
INSERT INTO `cw_system_menu` VALUES (50050, 50000, 'C', '字典管理', 'el-icon-Box', 1, 'setting.dict.dict_type/lists', 'dict', 'setting/dict/type/index', '', '', 0, 1, 0, 1657161211, 1663225935);
INSERT INTO `cw_system_menu` VALUES (50051, 50050, 'A', '字典类型详情', '', 1, 'setting.dict.dict_type/detail', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (50052, 50050, 'A', '字典类型新增', '', 1, 'setting.dict.dict_type/add', '', '', '', '', 0, 1, 0, 1657166966, 1663750783);
INSERT INTO `cw_system_menu` VALUES (50053, 50050, 'A', '字典类型编辑', '', 1, 'setting.dict.dict_type/edit', '', '', '', '', 0, 1, 0, 1657166997, 1663750789);
INSERT INTO `cw_system_menu` VALUES (50054, 50050, 'A', '字典类型删除', '', 1, 'setting.dict.dict_type/delete', '', '', '', '', 0, 1, 0, 1657167038, 1663750796);
INSERT INTO `cw_system_menu` VALUES (50055, 50050, 'A', '字典数据详情', '', 1, 'setting.dict.dict_data/detail', '', '', '', '', 0, 1, 0, 1657167317, 1663750758);
INSERT INTO `cw_system_menu` VALUES (50056, 50050, 'A', '字典数据新增', '', 1, 'setting.dict.dict_data/add', '', '', '', '', 0, 1, 0, 1657167317, 1663750758);
INSERT INTO `cw_system_menu` VALUES (50057, 50050, 'A', '字典数据编辑', '', 1, 'setting.dict.dict_data/edit', '', '', '', '', 0, 1, 0, 1657167371, 1663750751);
INSERT INTO `cw_system_menu` VALUES (50058, 50050, 'A', '字典数据删除', '', 1, 'setting.dict.dict_data/delete', '', '', '', '', 0, 1, 0, 1657167397, 1663750768);
INSERT INTO `cw_system_menu` VALUES (50100, 50000, 'M', '模板示例', 'el-icon-SetUp', 0, '', 'template', '', '', '', 0, 1, 0, 1670206819, 1677842041);
INSERT INTO `cw_system_menu` VALUES (50110, 50100, 'M', '组件示例', 'el-icon-Coin', 0, '', 'component', '', '', '', 0, 1, 0, 1670207182, 1670207244);
INSERT INTO `cw_system_menu` VALUES (50111, 50110, 'C', '富文本', '', 0, '', 'rich_text', 'template/component/rich_text', '', '', 0, 1, 0, 1670207751, 1670207751);
INSERT INTO `cw_system_menu` VALUES (50112, 50110, 'C', '上传文件', '', 0, '', 'upload', 'template/component/upload', '', '', 0, 1, 0, 1670208925, 1670208925);
INSERT INTO `cw_system_menu` VALUES (50113, 50110, 'C', '图标', '', 0, '', 'icon', 'template/component/icon', '', '', 0, 1, 0, 1670230069, 1670230069);
INSERT INTO `cw_system_menu` VALUES (50114, 50110, 'C', '文件选择器', '', 0, '', 'file', 'template/component/file', '', '', 0, 1, 0, 1670232129, 1670232129);
INSERT INTO `cw_system_menu` VALUES (50115, 50110, 'C', '链接选择器', '', 0, '', 'link', 'template/component/link', '', '', 0, 1, 0, 1670292636, 1670292636);
INSERT INTO `cw_system_menu` VALUES (50116, 50110, 'C', '超出自动打点', '', 0, '', 'overflow', 'template/component/overflow', '', '', 0, 1, 0, 1670292883, 1670292883);
INSERT INTO `cw_system_menu` VALUES (50117, 50110, 'C', '悬浮input', '', 0, '', 'popover_input', 'template/component/popover_input', '', '', 0, 1, 0, 1670293336, 1670293336);
INSERT INTO `cw_system_menu` VALUES (50118, 50130, 'C', '智能体分类', '', 0, 'kb.robotCate/lists', 'robot_category', 'knowledge_base/robot_square/category/index', '', '', 0, 1, 0, 1712125712, 1721788464);
INSERT INTO `cw_system_menu` VALUES (50119, 6500, 'C', '形象管理', '', 40, 'kb.digital/lists', 'digital', 'knowledge_base/digital/index', '', '', 0, 1, 0, 1712135945, 1715849409);
INSERT INTO `cw_system_menu` VALUES (50120, 6500, 'C', '形象详情', '', 50, 'kb.digital/detail', 'digital/detail', 'knowledge_base/digital/detail', '/konwledge_base/digital', '', 0, 0, 0, 1712140070, 1715849417);
INSERT INTO `cw_system_menu` VALUES (50121, 700, 'M', 'PC装修', '', 5, '', 'pc', '', '', '', 0, 1, 0, 1713940166, 1713940166);
INSERT INTO `cw_system_menu` VALUES (50122, 50121, 'C', 'AI创作', '', 0, 'decorate.page/detail', 'ai_creation', 'decoration/pc', '', 'type=1', 0, 1, 0, 1713940456, 1713940516);
INSERT INTO `cw_system_menu` VALUES (50123, 50121, 'C', '智能体广场', '', 0, 'decorate.page/detail', 'ai_robot', 'decoration/pc', '', 'type=2', 0, 1, 0, 1713940491, 1721965879);
INSERT INTO `cw_system_menu` VALUES (50124, 6500, 'M', '智能体管理', '', 70, '', 'robot', '', '', '', 0, 1, 0, 1714099672, 1721788420);
INSERT INTO `cw_system_menu` VALUES (50125, 6500, 'M', '知识库管理', '', 100, '', 'knowledge_base', '', '', '', 0, 1, 0, 1714100073, 1714100158);
INSERT INTO `cw_system_menu` VALUES (50126, 711, 'A', 'H5装修保存', '', 0, 'decorate.page/detail', '', '', '', '', 0, 1, 0, 1714392566, 1718157987);
INSERT INTO `cw_system_menu` VALUES (50127, 50122, 'A', 'AI创作保存', '', 0, 'decorate.page/detail', '', '', '', '', 0, 1, 0, 1714392629, 1714392629);
INSERT INTO `cw_system_menu` VALUES (50128, 50123, 'A', '保存', '', 0, 'decorate.page/detail', '', '', '', '', 0, 1, 0, 1714392639, 1714392639);
INSERT INTO `cw_system_menu` VALUES (50130, 6500, 'M', '智能体广场', '', 45, '', 'robot_square', '', '', '', 0, 1, 0, 1715162159, 1721788457);
INSERT INTO `cw_system_menu` VALUES (50131, 50130, 'C', '广场列表', '', 0, 'kb.square/lists', 'robot_square', 'knowledge_base/robot_square/index', '', '', 0, 1, 0, 1715163434, 1715163434);
INSERT INTO `cw_system_menu` VALUES (50132, 50119, 'A', '删除', '', 0, 'kb.digital/del', '', '', '', '', 0, 1, 0, 1715220675, 1715220675);
INSERT INTO `cw_system_menu` VALUES (50133, 50118, 'A', '添加', '', 0, 'kb.robotCate/add', '', '', '', '', 0, 1, 0, 1715220718, 1715220718);
INSERT INTO `cw_system_menu` VALUES (50134, 50118, 'A', '编辑', '', 0, 'kb.robotCate/edit', '', '', '', '', 0, 1, 0, 1715220736, 1715220736);
INSERT INTO `cw_system_menu` VALUES (50135, 50118, 'A', '删除', '', 0, 'kb.robotCate/del', '', '', '', '', 0, 1, 0, 1715220752, 1715220752);
INSERT INTO `cw_system_menu` VALUES (50136, 50118, 'A', '修改状态', '', 0, 'kb.robotCate/changeStatus', '', '', '', '', 0, 1, 0, 1715220877, 1715220877);
INSERT INTO `cw_system_menu` VALUES (50137, 50131, 'A', '编辑', '', 0, 'kb.square/edit', '', '', '', '', 0, 1, 0, 1715221174, 1715221174);
INSERT INTO `cw_system_menu` VALUES (50138, 50131, 'A', '删除', '', 0, 'kb.square/del', '', '', '', '', 0, 1, 0, 1715221197, 1715221197);
INSERT INTO `cw_system_menu` VALUES (50139, 50131, 'A', '修改排序', '', 0, 'kb.square/setSort', '', '', '', '', 0, 1, 0, 1715221222, 1715221263);
INSERT INTO `cw_system_menu` VALUES (50140, 50131, 'A', '修改状态', '', 0, 'kb.square/setStatus', '', '', '', '', 0, 1, 0, 1715221250, 1715221250);
INSERT INTO `cw_system_menu` VALUES (50141, 50119, 'A', '修改状态', '', 0, 'kb.digital/changeStatus', '', '', '', '', 0, 1, 0, 1715221509, 1715221509);
INSERT INTO `cw_system_menu` VALUES (50142, 0, 'M', 'AI角色', 'local-icon-wode', 1700, '', 'ai_role', '', '', '', 0, 1, 0, 1715571657, 1717388251);
INSERT INTO `cw_system_menu` VALUES (50143, 50142, 'C', '对话记录', '', 0, 'chat.chat_record/lists', 'record', 'ai_role/record/index', '', '', 0, 1, 0, 1715572491, 1715572491);
INSERT INTO `cw_system_menu` VALUES (50144, 50143, 'A', '删除', '', 0, 'chat.chat_record/del', '', '', '', '', 0, 1, 0, 1715572528, 1715572528);
INSERT INTO `cw_system_menu` VALUES (50145, 50142, 'C', '角色管理', '', 0, 'skill.skill/lists', 'manage', 'ai_role/manage/index', '', '', 0, 1, 0, 1715572857, 1715572857);
INSERT INTO `cw_system_menu` VALUES (50146, 50145, 'A', '角色详情', '', 0, 'skill.skill/detail', '', '', '', '', 0, 1, 0, 1715572895, 1715572895);
INSERT INTO `cw_system_menu` VALUES (50147, 50145, 'A', '新增', '', 0, 'skill.skill/add', '', '', '', '', 0, 1, 0, 1715572914, 1715572914);
INSERT INTO `cw_system_menu` VALUES (50148, 50145, 'A', '编辑', '', 0, 'skill.skill/edit', '', '', '', '', 0, 1, 0, 1715572926, 1715572926);
INSERT INTO `cw_system_menu` VALUES (50149, 50145, 'A', '删除', '', 0, 'skill.skill/del', '', '', '', '', 0, 1, 0, 1715572936, 1715572936);
INSERT INTO `cw_system_menu` VALUES (50150, 50145, 'A', '状态', '', 0, 'skill.skill/status', '', '', '', '', 0, 1, 0, 1715572949, 1715572949);
INSERT INTO `cw_system_menu` VALUES (50151, 50142, 'C', '角色管理详情', '', 0, 'skill.skill/add:edit', 'detail', 'ai_role/manage/edit', '/ai_role/manage', '', 0, 0, 0, 1715573034, 1715582313);
INSERT INTO `cw_system_menu` VALUES (50152, 50142, 'C', '角色类别', '', 0, 'skill.skillCategory/lists', 'category', 'ai_role/type/index', '', '', 0, 1, 0, 1715573097, 1715573097);
INSERT INTO `cw_system_menu` VALUES (50153, 50152, 'A', '新增', '', 0, 'skill.skillCategory/add', '', '', '', '', 0, 1, 0, 1715573117, 1715573117);
INSERT INTO `cw_system_menu` VALUES (50154, 50152, 'A', '编辑', '', 0, 'skill.skillCategory/edit', '', '', '', '', 0, 1, 0, 1715573140, 1715573140);
INSERT INTO `cw_system_menu` VALUES (50155, 50152, 'A', '删除', '', 0, 'skill.skillCategory/del', '', '', '', '', 0, 1, 0, 1715573154, 1715573154);
INSERT INTO `cw_system_menu` VALUES (50156, 50152, 'A', '状态', '', 0, 'skill.skillCategory/status', '', '', '', '', 0, 1, 0, 1715573168, 1715573168);
INSERT INTO `cw_system_menu` VALUES (50157, 50121, 'C', 'PC侧边导航', '', 0, '', 'pc_aside', 'decoration/tabbar', '', 'type=pc', 0, 1, 0, 1715847047, 1715847241);
INSERT INTO `cw_system_menu` VALUES (50158, 900, 'M', '微信小程序', '', 20, '', 'weapp', '', '', '', 0, 1, 0, 1716350987, 1721890093);
INSERT INTO `cw_system_menu` VALUES (50159, 50158, 'C', '小程序配置', '', 20, 'channel.mnp_settings/getConfig', 'mp_config', 'channel/weapp', '', '', 0, 1, 0, 1716351049, 1721890130);
INSERT INTO `cw_system_menu` VALUES (50160, 50159, 'A', '保存', '', 0, 'channel.mnp_settings/setConfig', '', '', '', '', 0, 1, 0, 1716351068, 1716351068);
INSERT INTO `cw_system_menu` VALUES (50161, 50158, 'C', '小程序一键上传', '', 0, 'channel.mnp_settings/uploadMnp', 'upload', 'channel/weapp_upload', '', '', 0, 0, 1, 1716351100, 1724299869);
INSERT INTO `cw_system_menu` VALUES (50162, 2000, 'C', '新增模型', '', 0, 'setting.ai.models/add', 'ai_model/add', 'ai_setting/ai_model/add', '/ai_setting/ai_model', '', 0, 0, 0, 1716799163, 1716884400);
INSERT INTO `cw_system_menu` VALUES (50163, 2000, 'C', '编辑模型', '', 0, 'setting.ai.models/edit', 'ai_model/edit', 'ai_setting/ai_model/edit', '/ai_setting/ai_model', '', 0, 0, 0, 1716799220, 1716884405);
INSERT INTO `cw_system_menu` VALUES (50164, 4000, 'M', '会员套餐', '', 0, '', 'member', '', '', '', 0, 1, 0, 1716806102, 1716806102);
INSERT INTO `cw_system_menu` VALUES (50165, 50164, 'C', '会员等级', '', 0, 'member.memberPackage/lists', 'package', 'marketing/member/package/index', '', '', 0, 1, 0, 1716806137, 1717646433);
INSERT INTO `cw_system_menu` VALUES (50166, 50165, 'A', '新增', '', 0, 'member.memberPackage/add', '', '', '', '', 0, 1, 0, 1716806197, 1716806387);
INSERT INTO `cw_system_menu` VALUES (50167, 50165, 'A', '编辑', '', 0, 'member.memberPackage/edit', '', '', '', '', 0, 1, 0, 1716806207, 1716806393);
INSERT INTO `cw_system_menu` VALUES (50168, 50165, 'A', '删除', '', 0, 'member.memberPackage/del', '', '', '', '', 0, 1, 0, 1716806219, 1716806400);
INSERT INTO `cw_system_menu` VALUES (50169, 50165, 'A', '状态', '', 0, 'member.memberPackage/status', '', '', '', '', 0, 1, 0, 1716806238, 1716806408);
INSERT INTO `cw_system_menu` VALUES (50170, 50165, 'A', '推荐', '', 0, 'member.memberPackage/recommend', '', '', '', '', 0, 1, 0, 1716806255, 1716806411);
INSERT INTO `cw_system_menu` VALUES (50171, 50165, 'A', '排序', '', 0, 'member.memberPackage/sort', '', '', '', '', 0, 1, 0, 1716806267, 1716806416);
INSERT INTO `cw_system_menu` VALUES (50172, 50164, 'C', '会员等级添加/编辑', '', 0, 'member.memberPackage/add:edit', 'detail', 'marketing/member/package/detail', '/material/member/package', '', 0, 0, 0, 1716806501, 1717646441);
INSERT INTO `cw_system_menu` VALUES (50173, 50187, 'M', 'AI音乐', '', 900, '', 'music', 'music', '', '', 0, 1, 0, 1716886047, 1717400556);
INSERT INTO `cw_system_menu` VALUES (50174, 50173, 'C', 'AI配置', '', 79, 'music.musicConfig/detail', 'setting', 'ai_application/music/setting', '', '', 0, 1, 0, 1716886253, 1717400571);
INSERT INTO `cw_system_menu` VALUES (50175, 50174, 'A', '保存', '', 0, 'music.musicConfig/save', '', '', '', '', 0, 1, 0, 1716886272, 1716886272);
INSERT INTO `cw_system_menu` VALUES (50176, 50164, 'C', '购买评价', '', 0, 'member.member_package_comment/lists', 'comment', 'marketing/member/comment/index', '', '', 0, 0, 1, 1716887519, 1717646316);
INSERT INTO `cw_system_menu` VALUES (50177, 50176, 'A', '删除', '', 0, 'member.member_package_comment/del', '', '', '', '', 0, 1, 0, 1716887962, 1716887962);
INSERT INTO `cw_system_menu` VALUES (50178, 50176, 'A', '新增', '', 0, 'member.member_package_comment/add', '', '', '', '', 0, 1, 0, 1716887973, 1716887973);
INSERT INTO `cw_system_menu` VALUES (50179, 50173, 'C', '曲风管理', '', 100, 'music.musicStyle/lists', 'style', 'ai_application/music/style/index', '', '', 0, 1, 0, 1716955315, 1717400563);
INSERT INTO `cw_system_menu` VALUES (50180, 50179, 'A', '添加', '', 0, 'music.musicStyle/add', '', '', '', '', 0, 1, 0, 1716956142, 1716956142);
INSERT INTO `cw_system_menu` VALUES (50181, 50179, 'A', '编辑', '', 0, 'music.musicStyle/edit', '', '', '', '', 0, 1, 0, 1716956164, 1716956164);
INSERT INTO `cw_system_menu` VALUES (50182, 50179, 'A', '删除', '', 0, 'music.musicStyle/del', '', '', '', '', 0, 1, 0, 1716956176, 1716956176);
INSERT INTO `cw_system_menu` VALUES (50183, 50179, 'A', '修改状态', '', 0, 'music.musicStyle/status', '', '', '', '', 0, 1, 0, 1716956200, 1716956200);
INSERT INTO `cw_system_menu` VALUES (50184, 6300, 'C', '会员订单', '', 0, 'member.member_order/lists', 'member', 'order/member_order/lists', '', '', 0, 1, 0, 1716965747, 1716965747);
INSERT INTO `cw_system_menu` VALUES (50185, 50184, 'A', '详情', '', 0, 'member.member_order/detail', '', '', '', '', 0, 1, 0, 1716965767, 1716965767);
INSERT INTO `cw_system_menu` VALUES (50186, 50184, 'A', '退款', '', 0, 'member.member_order/refund', '', '', '', '', 0, 1, 0, 1716965779, 1716965779);
INSERT INTO `cw_system_menu` VALUES (50187, 0, 'M', 'AI应用', 'local-icon-sort', 1599, '', 'ai_application', '', '', '', 0, 1, 0, 1715413585, 1715416860);
INSERT INTO `cw_system_menu` VALUES (50188, 50187, 'M', 'SD绘图', '', 1000, '', 'sd', '', '', '', 0, 1, 0, 1715417058, 1717400550);
INSERT INTO `cw_system_menu` VALUES (50189, 50188, 'C', '绘画记录', '', 0, 'draw.draw_records/lists', 'record', 'ai_application/sd/record/index', '', '', 1, 1, 0, 1715417130, 1722925786);
INSERT INTO `cw_system_menu` VALUES (50190, 50188, 'C', '描述词库', '', 0, 'draw.draw_prompt/lists', 'descriptor', 'ai_application/sd/prompt/index', '', '', 1, 1, 0, 1715417165, 1722666094);
INSERT INTO `cw_system_menu` VALUES (50191, 50188, 'C', '描述词分类', '', 0, 'draw.draw_prompt_category/lists', 'descriptor-category', 'ai_application/sd/prompt_category/index', '', '', 1, 1, 0, 1715417215, 1722666118);
INSERT INTO `cw_system_menu` VALUES (50192, 50188, 'C', '绘画示例', '', 0, 'draw.draw_prompt_example/lists', 'example', 'ai_application/sd/example/index', '', '', 1, 1, 0, 1715417373, 1722666151);
INSERT INTO `cw_system_menu` VALUES (50193, 50188, 'C', '模型分类', '', 0, '', 'model-category', 'ai_application/sd/model_category/index', '', '', 1, 1, 0, 1715417407, 1715596667);
INSERT INTO `cw_system_menu` VALUES (50194, 50188, 'C', '模型管理', '', 0, '', 'model', 'ai_application/sd/model/index', '', '', 1, 1, 0, 1715417456, 1715417456);
INSERT INTO `cw_system_menu` VALUES (50195, 50188, 'C', '微调模型', '', 0, '', 'lora', 'ai_application/sd/lora/index', '', '', 0, 1, 0, 1715417522, 1715417522);
INSERT INTO `cw_system_menu` VALUES (50196, 50188, 'C', '应用配置', '', 0, 'setting.ai.draw/detail', 'setting', 'ai_application/sd/setting/index', '', '', 0, 1, 0, 1715417557, 1722671999);
INSERT INTO `cw_system_menu` VALUES (50197, 50188, 'C', '新增/编辑绘画模型', '', 0, 'application.sd.model/edit', 'edit', 'ai_application/sd/model/edit', '', '', 0, 0, 0, 1715681816, 1715682646);
INSERT INTO `cw_system_menu` VALUES (50198, 50194, 'A', '删除', '', 0, 'application.sd.model/delete', '', '', '', '', 0, 1, 0, 1715682626, 1715682626);
INSERT INTO `cw_system_menu` VALUES (50199, 50188, 'C', '新增/编辑描述词库', '', 0, 'application.sd.prompt/edit', 'edit', 'ai_application/sd/prompt/edit', '', '', 0, 0, 0, 1715682751, 1715683001);
INSERT INTO `cw_system_menu` VALUES (50200, 50188, 'C', '新增/编辑描述词分类', '', 0, 'application.sd.prompt_category/edit', 'edit', 'ai_application/sd/prompt_category/edit', '', '', 0, 0, 0, 1715682873, 1715682873);
INSERT INTO `cw_system_menu` VALUES (50201, 50188, 'C', '新增/编辑绘图示例', '', 0, 'application.sd.example/edit', 'edit', 'ai_application/sd/example/edit', '', '', 0, 0, 0, 1715682980, 1715682980);
INSERT INTO `cw_system_menu` VALUES (50202, 50188, 'C', '新增/编辑模型分类', '', 0, 'application.sd.model_category/edit', 'edit', 'ai_application/sd/model_category/edit', '', '', 0, 0, 0, 1715683071, 1715683071);
INSERT INTO `cw_system_menu` VALUES (50203, 50173, 'C', '生成记录', '', 500, 'music.musicRecord/lists', 'record', 'ai_application/music/record/index', '', '', 0, 1, 0, 1717552969, 1717577893);
INSERT INTO `cw_system_menu` VALUES (50204, 50203, 'A', '删除', '', 0, 'music.musicRecord/del', '', '', '', '', 0, 1, 0, 1717553025, 1717553025);
INSERT INTO `cw_system_menu` VALUES (50205, 50165, 'A', '保存配置', '', 0, 'member.MemberPackage/setConfig', '', '', '', '', 0, 1, 0, 1718349884, 1718349884);
INSERT INTO `cw_system_menu` VALUES (50206, 50165, 'A', '保存配置', '', 0, 'member.MemberPackage/setConfig', '', '', '', '', 0, 1, 0, 1718349884, 1718349884);
INSERT INTO `cw_system_menu` VALUES (50207, 50187, 'M', '思维导图', '', 700, '', 'mind_map', '', '', '', 0, 1, 0, 1718692721, 1722314265);
INSERT INTO `cw_system_menu` VALUES (50208, 50207, 'C', '生成记录', '', 0, '', 'record', 'ai_qa/dialogue_record/index', '', 'type=4', 0, 1, 0, 1718692793, 1718692793);
INSERT INTO `cw_system_menu` VALUES (50209, 50207, 'C', '导图示例', '', 0, 'setting.Mindmap/getExampleConfig', 'example', 'ai_application/mind_map/example', '', '', 0, 1, 0, 1718693092, 1718693092);
INSERT INTO `cw_system_menu` VALUES (50210, 50209, 'A', '保存', '', 0, 'setting.Mindmap/setExampleConfig', '', '', '', '', 0, 1, 0, 1718693115, 1718693115);
INSERT INTO `cw_system_menu` VALUES (50211, 50207, 'C', '应用配置', '', 0, 'setting.Mindmap/getConfig', 'setting', 'ai_application/mind_map/setting', '', '', 0, 1, 0, 1718693219, 1718693219);
INSERT INTO `cw_system_menu` VALUES (50212, 50211, 'A', '保存', '', 0, 'setting.Mindmap/setConfig', '', '', '', '', 0, 1, 0, 1718693317, 1718693317);
INSERT INTO `cw_system_menu` VALUES (50213, 940, 'C', '公众号配置', '', 0, 'channel.official_account_setting/getConfig', 'config', 'channel/wx_oa/config', '', '', 0, 1, 0, 1719311103, 1719311119);
INSERT INTO `cw_system_menu` VALUES (50214, 940, 'C', '菜单管理', '', 0, 'channel.official_account_menu/detail', 'menu', 'channel/wx_oa/menu', '', '', 0, 1, 0, 1719311173, 1719311173);
INSERT INTO `cw_system_menu` VALUES (50215, 50214, 'A', '保存', '', 0, 'channel.official_account_menu/save', '', '', '', '', 0, 1, 0, 1719311230, 1719311230);
INSERT INTO `cw_system_menu` VALUES (50216, 50214, 'A', '保存并发布', '', 0, 'channel.official_account_menu/saveAndPublish', '', '', '', '', 0, 1, 0, 1719311254, 1719311254);
INSERT INTO `cw_system_menu` VALUES (50217, 940, 'C', '关注回复', '', 0, 'channel.official_account_reply/lists', 'follow', 'channel/wx_oa/reply/follow_reply', '', '', 0, 1, 0, 1719311300, 1719311300);
INSERT INTO `cw_system_menu` VALUES (50218, 940, 'C', '关键字回复', '', 0, '', 'keyword', 'channel/wx_oa/reply/keyword_reply', '', '', 0, 1, 0, 1719311348, 1719311348);
INSERT INTO `cw_system_menu` VALUES (50219, 940, 'C', '默认回复', '', 0, '', 'default', 'channel/wx_oa/reply/default_reply', '', '', 0, 1, 0, 1719311378, 1719311378);
INSERT INTO `cw_system_menu` VALUES (50220, 4000, 'M', '分销推广', '', 0, '', 'distribution', '', '', '', 0, 1, 0, 1719802624, 1719802624);
INSERT INTO `cw_system_menu` VALUES (50221, 50220, 'C', '分销商', '', 0, 'distribution.distributor/lists', 'distributor', 'marketing/distribution/distributor/lists', '', '', 0, 1, 0, 1719802708, 1719802708);
INSERT INTO `cw_system_menu` VALUES (50222, 50220, 'C', '分销商详情', '', 0, 'distribution.distributor/detail', 'distributor/detail', 'marketing/distribution/distributor/detail', '/material/distribution/distributor', '', 0, 0, 0, 1719802794, 1719805124);
INSERT INTO `cw_system_menu` VALUES (50223, 50221, 'A', '开通分销商', '', 0, 'distribution.distributor/add', '', '', '', '', 0, 1, 0, 1719802852, 1719802852);
INSERT INTO `cw_system_menu` VALUES (50224, 50221, 'A', '修改分销状态', '', 0, 'distribution.distributor/status', '', '', '', '', 0, 1, 0, 1719802867, 1719802867);
INSERT INTO `cw_system_menu` VALUES (50225, 50220, 'C', '下级分销商', '', 0, 'distribution.distributor/belowLists', 'distributor/lowdistributor', 'marketing/distribution/distributor/lowdestributor', '/material/distribution/distributor', '', 0, 0, 0, 1719803515, 1719805132);
INSERT INTO `cw_system_menu` VALUES (50226, 50220, 'C', '分销订单', '', 0, 'distribution.distribution_order/lists', 'order', 'marketing/distribution/order/lists', '', '', 0, 1, 0, 1719803568, 1719805634);
INSERT INTO `cw_system_menu` VALUES (50227, 50220, 'C', '分销申请', '', 0, 'distribution.distributionApply/lists', 'apply', 'marketing/distribution/apply/lists', '', '', 0, 1, 0, 1719803603, 1719803603);
INSERT INTO `cw_system_menu` VALUES (50228, 50227, 'A', '详情', '', 0, 'distribution.distributionApply/detail', '', '', '', '', 0, 1, 0, 1719803624, 1719803624);
INSERT INTO `cw_system_menu` VALUES (50229, 50227, 'A', '审核', '', 0, 'distribution.distributionApply/audit', '', '', '', '', 0, 1, 0, 1719803639, 1719803639);
INSERT INTO `cw_system_menu` VALUES (50230, 50220, 'C', '提现记录', '', 0, 'distribution.withdraw/lists', 'distribution/withdraw', 'marketing/distribution/withdraw/lists', '', '', 0, 1, 0, 1719803669, 1719803669);
INSERT INTO `cw_system_menu` VALUES (50231, 50230, 'A', '提现审核', '', 0, 'distribution.withdraw/verify', '', '', '', '', 0, 1, 0, 1719803691, 1719803691);
INSERT INTO `cw_system_menu` VALUES (50232, 50230, 'A', '转账', '', 0, 'distribution.withdraw/transfer', '', '', '', '', 0, 1, 0, 1719803710, 1719803710);
INSERT INTO `cw_system_menu` VALUES (50233, 50230, 'A', '提现详情', '', 0, 'distribution.withdraw/detail', '', '', '', '', 0, 1, 0, 1719803727, 1719803727);
INSERT INTO `cw_system_menu` VALUES (50234, 50220, 'C', '分销设置', '', 0, 'distribution.config/getConfig', 'setting/distribution', 'marketing/distribution/setting/distribution', '', '', 0, 1, 0, 1719803764, 1719803764);
INSERT INTO `cw_system_menu` VALUES (50235, 50234, 'A', '保存', '', 0, 'distribution.config/setConfig', '', '', '', '', 0, 1, 0, 1719803786, 1719803786);
INSERT INTO `cw_system_menu` VALUES (50236, 50220, 'C', '提现设置', '', 0, 'distribution.withdraw/getConfig', 'setting/withdraw', 'marketing/distribution/setting/withdraw', '', '', 0, 1, 0, 1719803962, 1719803962);
INSERT INTO `cw_system_menu` VALUES (50237, 50236, 'A', '保存', '', 0, 'distribution.withdraw/setConfig', '', '', '', '', 0, 1, 0, 1719803978, 1719803978);
INSERT INTO `cw_system_menu` VALUES (50238, 4000, 'M', '卡密兑换', '', 0, '', 'redeem_code', '', '', '', 0, 1, 0, 1720489269, 1720489269);
INSERT INTO `cw_system_menu` VALUES (50239, 50238, 'C', '卡密管理', '', 0, 'cardcode.cardCode/lists', 'lists', 'marketing/redeem_code/lists/index', '', '', 0, 1, 0, 1720489301, 1720489301);
INSERT INTO `cw_system_menu` VALUES (50240, 50239, 'A', '卡密添加', '', 0, 'cardcode.cardCode/add', '', '', '', '', 0, 1, 0, 1720489331, 1720489331);
INSERT INTO `cw_system_menu` VALUES (50241, 50239, 'A', '卡密编辑', '', 0, 'cardcode.cardCode/edit', '', '', '', '', 0, 1, 0, 1720489343, 1720489343);
INSERT INTO `cw_system_menu` VALUES (50242, 50239, 'A', '卡密详情', '', 0, 'cardcode.cardCode/detail', '', '', '', '', 0, 1, 0, 1720489356, 1720489356);
INSERT INTO `cw_system_menu` VALUES (50243, 50238, 'C', '兑换记录', '', 0, 'cardcode.cardCodeRecord/lists', 'record', 'marketing/redeem_code/record/index', '', '', 0, 1, 0, 1720489387, 1720489387);
INSERT INTO `cw_system_menu` VALUES (50244, 50238, 'C', '卡密设置', '', 0, 'cardcode.cardCode/getConfig', 'setting', 'marketing/redeem_code/setting/index', '', '', 0, 1, 0, 1720489415, 1720489415);
INSERT INTO `cw_system_menu` VALUES (50245, 50244, 'A', '保存', '', 0, 'cardcode.cardCode/setConfig', '', '', '', '', 0, 1, 0, 1720489433, 1720489433);
INSERT INTO `cw_system_menu` VALUES (50246, 50187, 'M', 'AI视频', '', 800, '', 'video', '', '', '', 0, 1, 0, 1720518804, 1722314256);
INSERT INTO `cw_system_menu` VALUES (50247, 50246, 'C', '生成记录', '', 0, 'video.videoRecord/lists', 'record', 'ai_application/video/record/index', '', '', 0, 1, 0, 1720518935, 1720518935);
INSERT INTO `cw_system_menu` VALUES (50248, 50247, 'A', '删除', '', 0, 'video.videoRecord/del', '', '', '', '', 0, 1, 0, 1720518983, 1720518983);
INSERT INTO `cw_system_menu` VALUES (50249, 50246, 'C', '视频风格', '', 0, 'video.videoStyle/lists', 'style', 'ai_application/video/style/index', '', '', 0, 1, 0, 1720519075, 1720519075);
INSERT INTO `cw_system_menu` VALUES (50250, 50249, 'A', '编辑', '', 0, 'video.videoStyle/edit', '', '', '', '', 0, 1, 0, 1720519097, 1720519252);
INSERT INTO `cw_system_menu` VALUES (50251, 50249, 'A', '添加', '', 0, 'video.videoStyle/add', '', '', '', '', 0, 1, 0, 1720519123, 1720519123);
INSERT INTO `cw_system_menu` VALUES (50252, 50249, 'A', '修改状态', '', 0, 'video.videoStyle/status', '', '', '', '', 0, 1, 0, 1720519279, 1720519279);
INSERT INTO `cw_system_menu` VALUES (50253, 50249, 'A', '删除', '', 0, 'video.videoStyle/del', '', '', '', '', 0, 1, 0, 1720519296, 1720519296);
INSERT INTO `cw_system_menu` VALUES (50254, 50246, 'C', '应用配置', '', 0, 'video.videoConfig/detail', 'setting', 'ai_application/video/setting/ index', '', '', 0, 1, 0, 1720519337, 1720519337);
INSERT INTO `cw_system_menu` VALUES (50255, 50254, 'A', '保存', '', 0, 'video.videoConfig/save', '', '', '', '', 0, 1, 0, 1720519356, 1720519356);
INSERT INTO `cw_system_menu` VALUES (50256, 1000, 'C', '分享设置', '', 55, 'setting.shareSetting/getConfig', 'share', 'setting/share/index', '', '', 0, 1, 0, 1721033496, 1721033496);
INSERT INTO `cw_system_menu` VALUES (50257, 50256, 'A', '保存', '', 0, 'setting.shareSetting/setConfig', '', '', '', '', 0, 1, 0, 1721033508, 1721033508);
INSERT INTO `cw_system_menu` VALUES (50259, 4000, 'M', '分享奖励', '', 45, '', 'share', '', '', '', 0, 1, 0, 1721644829, 1721644829);
INSERT INTO `cw_system_menu` VALUES (50260, 4000, 'M', '邀请奖励', '', 40, '', 'invite', '', '', '', 0, 1, 0, 1721644852, 1721644852);
INSERT INTO `cw_system_menu` VALUES (50261, 4000, 'M', '签到奖励', '', 35, '', 'sign', '', '', '', 0, 1, 0, 1721644870, 1721644870);
INSERT INTO `cw_system_menu` VALUES (50262, 4000, 'M', '作品分享奖励', '', 30, '', 'works', '', '', '', 0, 1, 0, 1721644923, 1721644964);
INSERT INTO `cw_system_menu` VALUES (50263, 4000, 'M', '智能体公开奖励', '', 25, '', 'agent', '', '', '', 0, 1, 0, 1721644957, 1722307694);
INSERT INTO `cw_system_menu` VALUES (50264, 50259, 'C', '分享设置', '', 0, 'market.activityReward/getShareSetting', 'setting', 'marketing/share/setting', '', '', 0, 1, 0, 1721645020, 1721645020);
INSERT INTO `cw_system_menu` VALUES (50265, 50259, 'C', '分享记录', '', 0, '', 'record', 'marketing/share/record', '', '', 0, 1, 0, 1721645768, 1721645768);
INSERT INTO `cw_system_menu` VALUES (50266, 50260, 'C', '邀请设置', '', 0, 'market.activityReward/getInviteSetting', 'setting', 'marketing/invite/setting', '', '', 0, 1, 0, 1721645809, 1721645809);
INSERT INTO `cw_system_menu` VALUES (50267, 50260, 'C', '邀请记录', '', 0, '', 'record', 'marketing/invite/record', '', '', 0, 1, 0, 1721645832, 1721645832);
INSERT INTO `cw_system_menu` VALUES (50268, 50261, 'C', '签到设置', '', 0, 'market.activityReward/getSignSetting', 'setting', 'marketing/sign/setting', '', '', 0, 1, 0, 1721645875, 1721645875);
INSERT INTO `cw_system_menu` VALUES (50269, 50261, 'C', '签到记录', '', 0, '', 'record', 'marketing/sign/record', '', '', 0, 1, 0, 1721645897, 1721645897);
INSERT INTO `cw_system_menu` VALUES (50270, 50262, 'C', '分享设置', '', 0, 'market.activityReward/getWorkSetting', 'setting', 'marketing/works/setting', '', '', 0, 1, 0, 1721701128, 1721724041);
INSERT INTO `cw_system_menu` VALUES (50271, 50262, 'C', '分享记录', '', 0, '', 'record', 'marketing/works/record', '', '', 0, 1, 0, 1721701152, 1721701152);
INSERT INTO `cw_system_menu` VALUES (50272, 50263, 'C', '公开设置', '', 0, 'market.activityReward/getRobotSetting', 'setting', 'marketing/agent/setting', '', '', 0, 1, 0, 1721701203, 1722307712);
INSERT INTO `cw_system_menu` VALUES (50273, 50263, 'C', '公开记录', '', 0, '', 'record', 'marketing/agent/record', '', '', 0, 1, 0, 1721701225, 1722307703);
INSERT INTO `cw_system_menu` VALUES (50275, 50304, 'C', '绘画广场', '', 2000, 'draw.draw_square/lists', 'draw_list', 'ai_application/ai_square/draw/index', '', '', 0, 1, 0, 1721792493, 1722319544);
INSERT INTO `cw_system_menu` VALUES (50276, 50304, 'C', '广场设置', '', 0, 'market.activityReward/getSquareSetting', 'setting', 'ai_application/ai_square/setting/index', '', '', 0, 1, 0, 1721792521, 1722320381);
INSERT INTO `cw_system_menu` VALUES (50277, 50276, 'A', '保存', '', 0, 'market.activityReward/setSquareSetting', '', '', '', '', 0, 1, 0, 1721792542, 1722319651);
INSERT INTO `cw_system_menu` VALUES (50278, 50275, 'A', '新增', '', 0, 'draw.draw_square/add', '', '', '', '', 0, 1, 0, 1721794424, 1721794424);
INSERT INTO `cw_system_menu` VALUES (50279, 50275, 'A', '编辑', '', 0, 'draw.draw_square/edit', '', '', '', '', 0, 1, 0, 1721794446, 1721794446);
INSERT INTO `cw_system_menu` VALUES (50280, 50275, 'A', '删除', '', 0, 'draw.draw_square/del', '', '', '', '', 0, 1, 0, 1721794457, 1721794457);
INSERT INTO `cw_system_menu` VALUES (50281, 50275, 'A', '状态', '', 0, 'draw.draw_square/isShow', '', '', '', '', 0, 1, 0, 1721794467, 1721794467);
INSERT INTO `cw_system_menu` VALUES (50282, 50275, 'A', '审核', '', 0, 'draw.draw_square/verifyStatus', '', '', '', '', 0, 1, 0, 1721794493, 1721794493);
INSERT INTO `cw_system_menu` VALUES (50284, 50304, 'C', '音乐广场', '', 1500, 'music.musicSquare/lists', 'music_list', 'ai_application/ai_square/music/index', '', '', 0, 1, 0, 1721802400, 1722319555);
INSERT INTO `cw_system_menu` VALUES (50287, 50304, 'C', '视频广场', '', 500, 'video.videoSquare/lists', 'video_list', 'ai_application/ai_square/video/index', '', '', 0, 1, 0, 1721802779, 1722319566);
INSERT INTO `cw_system_menu` VALUES (50289, 50284, 'A', '新增', '', 0, 'music.musicSquare/add', '', '', '', '', 0, 1, 0, 1721803027, 1721803027);
INSERT INTO `cw_system_menu` VALUES (50290, 50284, 'A', '编辑', '', 0, 'music.musicSquare/edit', '', '', '', '', 0, 1, 0, 1721803039, 1721803039);
INSERT INTO `cw_system_menu` VALUES (50291, 50284, 'A', '审核', '', 0, 'music.musicSquare/verifyStatus', '', '', '', '', 0, 1, 0, 1721803057, 1721803057);
INSERT INTO `cw_system_menu` VALUES (50292, 50284, 'A', '删除', '', 0, 'music.musicSquare/del', '', '', '', '', 0, 1, 0, 1721803067, 1721803067);
INSERT INTO `cw_system_menu` VALUES (50293, 50284, 'A', '状态', '', 0, 'music.musicSquare/isShow', '', '', '', '', 0, 1, 0, 1721803081, 1721803081);
INSERT INTO `cw_system_menu` VALUES (50295, 50287, 'A', '新增', '', 0, 'video.videoSquare/add', '', '', '', '', 0, 1, 0, 1721803129, 1721803129);
INSERT INTO `cw_system_menu` VALUES (50296, 50287, 'A', '编辑', '', 0, 'video.videoSquare/edit', '', '', '', '', 0, 1, 0, 1721803140, 1721803140);
INSERT INTO `cw_system_menu` VALUES (50297, 50287, 'A', '审核', '', 0, 'video.videoSquare/verifyStatus', '', '', '', '', 0, 1, 0, 1721803150, 1721803150);
INSERT INTO `cw_system_menu` VALUES (50298, 50287, 'A', '删除', '', 0, 'video.videoSquare/del', '', '', '', '', 0, 1, 0, 1721803159, 1721803159);
INSERT INTO `cw_system_menu` VALUES (50299, 50287, 'A', '状态', '', 0, 'video.videoSquare/isShow', '', '', '', '', 0, 1, 0, 1721803169, 1721803169);
INSERT INTO `cw_system_menu` VALUES (50301, 320, 'A', '调整知识库容量', '', 0, 'user.user/adjustSpace', '', '', '', '', 0, 1, 0, 1721813025, 1721813025);
INSERT INTO `cw_system_menu` VALUES (50302, 50130, 'C', '广场设置', '', 0, 'kb.square/getConfig', 'setting', 'knowledge_base/robot_square/setting', '', '', 0, 1, 0, 1721980750, 1721980750);
INSERT INTO `cw_system_menu` VALUES (50303, 50302, 'A', '保存', '', 0, 'kb.square/setConfig', '', '', '', '', 0, 1, 0, 1721980759, 1721980759);
INSERT INTO `cw_system_menu` VALUES (50304, 50187, 'M', '应用广场', '', 300, '', 'ai_square', '', '', '', 0, 1, 0, 1722306454, 1722314235);
INSERT INTO `cw_system_menu` VALUES (50305, 50264, 'A', '保存', '', 0, 'market.activityReward/setShareSetting', '', '', '', '', 0, 1, 0, 1722417011, 1722417011);
INSERT INTO `cw_system_menu` VALUES (50306, 50266, 'A', '保存', '', 0, 'market.activityReward/setInviteSetting', '', '', '', '', 0, 1, 0, 1722417031, 1722417031);
INSERT INTO `cw_system_menu` VALUES (50307, 50268, 'A', '保存', '', 0, 'market.activityReward/setSignSetting', '', '', '', '', 0, 1, 0, 1722417047, 1722417047);
INSERT INTO `cw_system_menu` VALUES (50308, 50270, 'A', '保存', '', 0, 'market.activityReward/setWorksSetting', '', '', '', '', 0, 1, 0, 1722417067, 1722417067);
INSERT INTO `cw_system_menu` VALUES (50309, 50272, 'A', '保存', '', 0, 'market.activityReward/setRobotSetting', '', '', '', '', 0, 1, 0, 1722417082, 1722417082);
INSERT INTO `cw_system_menu` VALUES (50310, 50131, 'A', '审核', '', 0, 'kb.square/verifyStatus', '', '', '', '', 0, 1, 0, 1722653457, 1722653457);
INSERT INTO `cw_system_menu` VALUES (50311, 50187, 'M', 'DALLE绘画', '', 950, '', 'dalle', '', '', '', 0, 1, 0, 1722665292, 1722665292);
INSERT INTO `cw_system_menu` VALUES (50312, 50311, 'C', '绘画记录', '', 0, 'draw.draw_records/lists', 'record', 'ai_application/dalle/record/index', '', '', 0, 1, 0, 1722665663, 1722666077);
INSERT INTO `cw_system_menu` VALUES (50313, 50311, 'C', '描述词库', '', 0, 'draw.draw_prompt/lists', 'descriptor', 'ai_application/dalle/prompt/index', '', '', 0, 1, 0, 1722665690, 1722666104);
INSERT INTO `cw_system_menu` VALUES (50314, 50311, 'C', '描述词分类', '', 0, 'draw.draw_prompt_category/lists', 'descriptor-category', 'ai_application/dalle/prompt_category/index', '', '', 0, 1, 0, 1722665822, 1722666125);
INSERT INTO `cw_system_menu` VALUES (50315, 50311, 'C', '绘画示例', '', 0, 'draw.draw_prompt_example/lists', 'example', 'ai_application/dalle/example/index', '', '', 0, 1, 0, 1722665848, 1722666145);
INSERT INTO `cw_system_menu` VALUES (50316, 50311, 'C', '应用配置', '', 0, 'setting.ai.draw/detail', 'setting', 'ai_application/dalle/setting/index', '', '', 0, 1, 0, 1722665887, 1722671992);
INSERT INTO `cw_system_menu` VALUES (50317, 50313, 'A', '状态', '', 0, 'draw.draw_prompt/status', '', '', '', '', 0, 1, 0, 1722671775, 1722671775);
INSERT INTO `cw_system_menu` VALUES (50318, 50313, 'A', '新增', '', 0, 'draw.draw_prompt/add', '', '', '', '', 0, 1, 0, 1722671784, 1722671784);
INSERT INTO `cw_system_menu` VALUES (50319, 50313, 'A', '删除', '', 0, 'draw.draw_prompt/delete', '', '', '', '', 0, 1, 0, 1722671801, 1722671838);
INSERT INTO `cw_system_menu` VALUES (50320, 50313, 'A', '编辑', '', 0, 'draw.draw_prompt/edit', '', '', '', '', 0, 1, 0, 1722671818, 1722671818);
INSERT INTO `cw_system_menu` VALUES (50321, 50314, 'A', '新增', '', 0, 'draw.draw_prompt_category/add', '', '', '', '', 0, 1, 0, 1722671860, 1722671860);
INSERT INTO `cw_system_menu` VALUES (50322, 50314, 'A', '编辑', '', 0, 'draw.draw_prompt_category/edit', '', '', '', '', 0, 1, 0, 1722671869, 1722671869);
INSERT INTO `cw_system_menu` VALUES (50323, 50314, 'A', '删除', '', 0, 'draw.draw_prompt_category/delete', '', '', '', '', 0, 1, 0, 1722671881, 1722671881);
INSERT INTO `cw_system_menu` VALUES (50324, 50315, 'A', '新增', '', 0, 'draw.draw_prompt_example/add', '', '', '', '', 0, 1, 0, 1722671920, 1722671920);
INSERT INTO `cw_system_menu` VALUES (50325, 50315, 'A', '编辑', '', 0, 'draw.draw_prompt_example/edit', '', '', '', '', 0, 1, 0, 1722671930, 1722671930);
INSERT INTO `cw_system_menu` VALUES (50326, 50315, 'A', '删除', '', 0, 'draw.draw_prompt_example/delete', '', '', '', '', 0, 1, 0, 1722671945, 1722671945);
INSERT INTO `cw_system_menu` VALUES (50327, 50315, 'A', '状态', '', 0, 'draw.draw_prompt_example/status', '', '', '', '', 0, 1, 0, 1722671953, 1722671953);
INSERT INTO `cw_system_menu` VALUES (50328, 50312, 'A', '删除', '', 0, 'draw.draw_records/delete', '', '', '', '', 0, 1, 0, 1722671970, 1722671970);
INSERT INTO `cw_system_menu` VALUES (50329, 50316, 'A', '保存', '', 0, 'setting.ai.draw/save', '', '', '', '', 0, 1, 0, 1722672012, 1722672012);
INSERT INTO `cw_system_menu` VALUES (50330, 50196, 'A', '保存', '', 0, 'setting.ai.draw/save', '', '', '', '', 0, 1, 0, 1722672020, 1722672020);
INSERT INTO `cw_system_menu` VALUES (50331, 700, 'C', 'AI应用中心', '', 2, 'decorate.page/detail', 'ai_app_center', 'decoration/app_center', '', '', 0, 1, 0, 1722827168, 1722827168);
INSERT INTO `cw_system_menu` VALUES (50332, 50304, 'C', '广场分类', '', 100, 'square.squareCategory/lists', 'category', 'ai_application/ai_square/category/index', '', '', 0, 1, 0, 1723025601, 1723025601);
INSERT INTO `cw_system_menu` VALUES (50333, 50332, 'A', '新增', '', 0, 'square.squareCategory/add', '', '', '', '', 0, 1, 0, 1723025614, 1723025614);
INSERT INTO `cw_system_menu` VALUES (50334, 50332, 'A', '编辑', '', 0, 'square.squareCategory/edit', '', '', '', '', 0, 1, 0, 1723025628, 1723025628);
INSERT INTO `cw_system_menu` VALUES (50335, 50332, 'A', '删除', '', 0, 'square.squareCategory/del', '', '', '', '', 0, 1, 0, 1723025638, 1723025638);
INSERT INTO `cw_system_menu` VALUES (50336, 50332, 'A', '状态', '', 0, 'square.squareCategory/status', '', '', '', '', 0, 1, 0, 1723025647, 1723025647);
INSERT INTO `cw_system_menu` VALUES (50337, 50275, 'A', '移动分类', '', 0, 'draw.draw_square/removeCategory', '', '', '', '', 0, 1, 0, 1723167737, 1723167737);
INSERT INTO `cw_system_menu` VALUES (50338, 50284, 'A', '移动分类', '', 0, 'music.musicSquare/removeCategory', '', '', '', '', 0, 1, 0, 1723167758, 1723167758);
INSERT INTO `cw_system_menu` VALUES (50339, 50287, 'A', '移动分类', '', 0, 'video.videoSquare/removeCategory', '', '', '', '', 0, 1, 0, 1723167781, 1723167781);
INSERT INTO `cw_system_menu` VALUES (50340, 50187, 'M', 'AI搜索', '', 0, '', 'search', '', '', '', 0, 1, 0, 1723615756, 1723615756);
INSERT INTO `cw_system_menu` VALUES (50341, 50340, 'C', '生成记录', '', 0, 'search.record/lists', 'record', 'ai_application/search/record/index', '', '', 0, 1, 0, 1723615975, 1723615975);
INSERT INTO `cw_system_menu` VALUES (50342, 50340, 'C', '搜索示例', '', 0, 'search.setting/exampleConfig', 'example', 'ai_application/search/example', '', '', 0, 1, 0, 1723624903, 1723624903);
INSERT INTO `cw_system_menu` VALUES (50343, 50340, 'C', '应用配置', '', 0, 'search.setting/basisConfig', 'setting', 'ai_application/search/setting/ index', '', '', 0, 1, 0, 1723625144, 1723625144);
INSERT INTO `cw_system_menu` VALUES (50344, 50187, 'M', 'MJ绘画', '', 949, '', 'mj_goapi', '', '', '', 0, 1, 0, 1723716020, 1723716020);
INSERT INTO `cw_system_menu` VALUES (50345, 50344, 'C', '绘画记录', '', 0, 'draw.draw_records/lists', 'record', 'ai_application/mj/record/index', '', '', 0, 1, 0, 1723716065, 1723716065);
INSERT INTO `cw_system_menu` VALUES (50346, 50344, 'C', '描述词库', '', 0, 'draw.draw_prompt/lists', 'descriptor', 'ai_application/mj/prompt/index', '', '', 0, 1, 0, 1723716108, 1723716108);
INSERT INTO `cw_system_menu` VALUES (50347, 50344, 'C', '描述词库分类', '', 0, 'draw.draw_prompt_category/lists', 'descriptor-category', 'ai_application/mj/prompt_category/index', '', '', 0, 1, 0, 1723716140, 1723716140);
INSERT INTO `cw_system_menu` VALUES (50348, 50344, 'C', '绘画示例', '', 0, 'draw.draw_prompt_example/lists', 'example', 'ai_application/mj/example/index', '', '', 0, 1, 0, 1723716173, 1723716173);
INSERT INTO `cw_system_menu` VALUES (50349, 50344, 'C', '应用配置', '', 0, 'setting.ai.draw/detail', 'setting', 'ai_application/mj/setting/index', '', '', 0, 1, 0, 1723716203, 1723716203);
INSERT INTO `cw_system_menu` VALUES (50350, 50349, 'A', '保存', '', 0, 'setting.ai.draw/save', '', '', '', '', 0, 1, 0, 1723716216, 1723716216);
INSERT INTO `cw_system_menu` VALUES (50351, 50348, 'A', '新增', '', 0, 'draw.draw_prompt_example/add', '', '', '', '', 0, 1, 0, 1723716248, 1723716248);
INSERT INTO `cw_system_menu` VALUES (50352, 50348, 'A', '编辑', '', 0, 'draw.draw_prompt_example/edit', '', '', '', '', 0, 1, 0, 1723716261, 1723716261);
INSERT INTO `cw_system_menu` VALUES (50353, 50348, 'A', '删除', '', 0, 'draw.draw_prompt_example/delete', '', '', '', '', 0, 1, 0, 1723716296, 1723716308);
INSERT INTO `cw_system_menu` VALUES (50354, 50348, 'A', '状态', '', 0, 'draw.draw_prompt_example/status', '', '', '', '', 0, 1, 0, 1723716322, 1723716322);
INSERT INTO `cw_system_menu` VALUES (50355, 50347, 'A', '新增', '', 0, 'draw.draw_prompt_category/add', '', '', '', '', 0, 1, 0, 1723716340, 1723716340);
INSERT INTO `cw_system_menu` VALUES (50356, 50347, 'A', '编辑', '', 0, 'draw.draw_prompt_category/edit', '', '', '', '', 0, 1, 0, 1723716349, 1723716349);
INSERT INTO `cw_system_menu` VALUES (50357, 50347, 'A', '删除', '', 0, 'draw.draw_prompt_category/delete', '', '', '', '', 0, 1, 0, 1723716359, 1723716359);
INSERT INTO `cw_system_menu` VALUES (50358, 50346, 'A', '状态', '', 0, 'draw.draw_prompt/status', '', '', '', '', 0, 1, 0, 1723716373, 1723716373);
INSERT INTO `cw_system_menu` VALUES (50359, 50346, 'A', '新增', '', 0, 'draw.draw_prompt/add', '', '', '', '', 0, 1, 0, 1723716381, 1723716381);
INSERT INTO `cw_system_menu` VALUES (50360, 50346, 'A', '编辑', '', 0, 'draw.draw_prompt/edit', '', '', '', '', 0, 1, 0, 1723716391, 1723716391);
INSERT INTO `cw_system_menu` VALUES (50361, 50346, 'A', '删除', '', 0, 'draw.draw_prompt/delete', '', '', '', '', 0, 1, 0, 1723716400, 1723716400);
INSERT INTO `cw_system_menu` VALUES (50362, 50345, 'A', '删除', '', 0, 'draw.draw_records/delete', '', '', '', '', 0, 1, 0, 1723716418, 1723716418);
INSERT INTO `cw_system_menu` VALUES (50363, 50341, 'A', '删除', '', 0, 'search.record/del', '', '', '', '', 0, 1, 0, 1724223085, 1724223085);
INSERT INTO `cw_system_menu` VALUES (50364, 50342, 'A', '保存', '', 0, 'search.setting/exampleSave', '', '', '', '', 0, 1, 0, 1724223111, 1724223111);
INSERT INTO `cw_system_menu` VALUES (50365, 50343, 'A', '保存', '', 0, 'search.setting/basisSave', '', '', '', '', 0, 1, 0, 1724223127, 1724223127);
COMMIT;
