
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
