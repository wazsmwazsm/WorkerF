
-- mysql
CREATE TABLE `t_user` (
    `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
    `g_id` int unsigned NOT NULL COMMENT '用户组 ID',
    `username` varchar(40) NOT NULL COMMENT '用户名',
    `email` varchar(150) NOT NULL COMMENT '用户邮箱',
    `sort_num` int unsigned NOT NULL COMMENT '排序字段',
    `activated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户是否可用',
    `created` int unsigned NOT NULL COMMENT '时间',
    PRIMARY KEY (`id`),
    KEY (`g_id`)
)ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE `t_user_group` (
    `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
    `c_id` int unsigned NOT NULL COMMENT '公司 ID',
    `groupname` varchar(40) NOT NULL COMMENT '用户组名',
    `sort_num` int unsigned NOT NULL COMMENT '排序字段',
    `created` int unsigned NOT NULL COMMENT '时间',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB CHARSET=utf8;

CREATE TABLE `t_company` (
    `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '自增 ID',
    `companyname` varchar(40) NOT NULL COMMENT '公司名',
    `sort_num` int unsigned NOT NULL COMMENT '排序字段',
    `created` int unsigned NOT NULL COMMENT '时间',
    PRIMARY KEY (`id`)
)ENGINE=InnoDB CHARSET=utf8;
