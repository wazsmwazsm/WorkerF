
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
