
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

-- postgresql

CREATE TABLE public.t_user (
   id int NOT NULL,
   g_id int NOT NULL,
   username varchar(40) NOT NULL,
   email varchar(150) NOT NULL,
   sort_num int NOT NULL,
   activated smallint NOT NULL DEFAULT 0,
   created int NOT NULL,
   PRIMARY KEY (id)
);
CREATE INDEX public.index_g_id
ON public.t_user (g_id);

CREATE TABLE public.t_user_group (
   id int NOT NULL,
   c_id int NOT NULL,
   groupname varchar(40) NOT NULL,
   sort_num int NOT NULL,
   created int NOT NULL,
   PRIMARY KEY (id)
);

CREATE TABLE public.t_company (
   id int NOT NULL,
   companyname varchar(40) NOT NULL,
   sort_num int NOT NULL,
   created int NOT NULL,
   PRIMARY KEY (id)
);

-- sqlite
CREATE TABLE t_user (
   id INTEGER NOT NULL,
   g_id INTEGER NOT NULL,
   username TEXT NOT NULL,
   email TEXT NOT NULL,
   sort_num INTEGER NOT NULL,
   activated INTEGER NOT NULL DEFAULT 0,
   created INTEGER NOT NULL,
   PRIMARY KEY (id ASC)
);
CREATE INDEX index_g_id
ON t_user (g_id);

CREATE TABLE t_user_group (
   id INTEGER NOT NULL,
   c_id INTEGER NOT NULL,
   groupname TEXT NOT NULL,
   sort_num INTEGER NOT NULL,
   created INTEGER NOT NULL,
   PRIMARY KEY (id ASC)
);

CREATE TABLE t_company (
   id INTEGER NOT NULL,
   companyname TEXT NOT NULL,
   sort_num INTEGER NOT NULL,
   created INTEGER NOT NULL,
   PRIMARY KEY (id ASC)
);
