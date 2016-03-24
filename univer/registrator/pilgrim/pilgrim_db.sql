
#create database domains;

#use domains;

CREATE TABLE domain_zone_type (
id_table SERIAL PRIMARY KEY,
id_type integer NOT NULL,
type_descr varchar (255) NOT NULL);

INSERT INTO domain_zone_type (id_type, type_descr) VALUES (1, 'Standart Domain Zone');

CREATE TABLE country_info (
id_table SERIAL PRIMARY KEY,
id_lang varchar(255) NOT NULL,
country_code varchar (255) NOT NULL,
country varchar (255) NOT NULL );

CREATE TABLE admin_table (
id_table SERIAL PRIMARY KEY,
login varchar(255) NOT NULL,
passwd varchar (255) NOT NULL );

insert into admin_table(login, passwd) values('bazil', '$1$YUAYtr6u$OZov2uZ2/fkgUrPw0xFtc.');
insert into admin_table(login, passwd) values('kulik', '$1$jtI98V3o$gPlZEjiS.KHj0st0sFQZL0');

########################################################################
########################################################################
########################################################################
########################################################################
#### 	ROOT ZONE DEFINITION

CREATE TABLE ns_list (
id_table SERIAL PRIMARY KEY,
ns_title varchar (255) NOT NULL,
ns_domain varchar (255) NOT NULL UNIQUE,
ns_ip varchar (16) NOT NULL UNIQUE );

CREATE TABLE mx_list (
id_table SERIAL PRIMARY KEY,
mx_title varchar (255) NOT NULL,
mx_domain varchar (255) NOT NULL UNIQUE,
mx_ip varchar (16) NOT NULL UNIQUE );

CREATE TABLE root_zone (
id_table SERIAL PRIMARY KEY,
zone_name varchar (255) NOT NULL UNIQUE,
max_lease_time integer DEFAULT 2,
initial integer DEFAULT 60,
grace integer DEFAULT 14,
pending integer DEFAULT 40,
admin_email varchar (255) NOT NULL,
active boolean DEFAULT TRUE,
zone_title varchar (255) NOT NULL );

CREATE TABLE ns_of_root_zone (
id_table SERIAL PRIMARY KEY,
id_ns integer REFERENCES ns_list (id_table) ON DELETE CASCADE,
id_root_zone integer REFERENCES root_zone (id_table) ON DELETE CASCADE,
ns_master integer DEFAULT 0 );

CREATE TABLE mx_of_root_zone (
id_table SERIAL PRIMARY KEY,
id_mx integer REFERENCES mx_list (id_table) ON DELETE CASCADE,
id_root_zone integer REFERENCES root_zone (id_table) ON DELETE CASCADE,
mx_prior integer DEFAULT 10 );

########################################################################
########################################################################
########################################################################
########################################################################
#### 	CLIENT DEFINITION

CREATE TABLE client (
id_table SERIAL PRIMARY KEY,
login varchar (255) NOT NULL,
passwd varchar (255) NOT NULL,
register_date timestamp NOT NULL,
last_log_date timestamp DEFAULT NULL,
last_log_ip varchar (16) DEFAULT NULL,
active boolean DEFAULT TRUE,
cp_access boolean DEFAULT TRUE,
deleted boolean DEFAULT FALSE,
deleted_date timestamp DEFAULT NULL,
deleted_by_admin integer DEFAULT NULL );

CREATE TABLE client_info (
id_table SERIAL PRIMARY KEY,
id_client integer REFERENCES client (id_table) ON DELETE CASCADE,
firstname varchar(255) NOT NULL,
lastname varchar(255) NOT NULL,
company varchar (255) DEFAULT NULL,
country varchar (255) NOT NULL,
region varchar (255) DEFAULT NULL,
postal integer NOT NULL,
city varchar (255) NOT NULL,
address varchar (255) NOT NULL,
phone varchar (20) NOT NULL,
fax varchar (20) DEFAULT NULL,
email varchar (255) NOT NULL,
egrpou integer DEFAULT NULL,
userhdl varchar (255) DEFAULT NULL,
add_info text DEFAULT NULL);

######################################
######################################
#### 	CLIENTS ZONE DEFINITION

CREATE TABLE domain_zone (
id_table SERIAL PRIMARY KEY,
id_client integer REFERENCES client (id_table) ON DELETE CASCADE,
id_root_zone integer REFERENCES root_zone (id_table) ON DELETE CASCADE,
fqdn varchar (255) NOT NULL,
domain varchar (255) NOT NULL,
type integer NOT NULL,
register_date timestamp NOT NULL,
deleted boolean DEFAULT FALSE,
deleted_date timestamp DEFAULT NULL,
deleted_by_admin integer DEFAULT NULL );

CREATE TABLE domain_zone_cfg (
id_table SERIAL PRIMARY KEY,
id_domain_zone integer REFERENCES domain_zone (id_table) ON DELETE CASCADE,
ttl integer DEFAULT 86400,
authority_server varchar (255) NOT NULL,
root_email varchar (255) NOT NULL,
serial varchar (255) NOT NULL,
refresh integer DEFAULT 86400,
retry integer DEFAULT 7200,
expire integer DEFAULT 604800,
negative integer DEFAULT 86400);

CREATE TABLE domain_zone_state (
id_table SERIAL PRIMARY KEY,
id_domain_zone integer REFERENCES domain_zone (id_table) ON DELETE CASCADE,
active boolean DEFAULT TRUE,
state varchar (255) NOT NULL,
query_post_date timestamp NOT NULL,
confirm_date timestamp NOT NULL,
payed timestamp,
lease_time integer NOT NULL,
end_time timestamp,
released timestamp,
refused timestamp,
holded timestamp,
frozen timestamp );

CREATE TABLE domain_records (
id_table SERIAL PRIMARY KEY,
id_domain_zone integer REFERENCES domain_zone (id_table) ON DELETE CASCADE,
record_type VARCHAR(20) NOT NULL CHECK (record_type IN ('NS', 'MX', 'A', 'CNAME')),
fqdn varchar (255) NOT NULL,
domain varchar (255) NOT NULL,
type varchar (255) NOT NULL DEFAULT 'IN',
mx_prior integer DEFAULT 10,
record varchar (255) NOT NULL,
deleted boolean DEFAULT FALSE,
deleted_date timestamp DEFAULT NULL,
deleted_by_admin integer DEFAULT NULL );

########################################################################
########################################################################
########################################################################
########################################################################

CREATE TABLE temporary_file (
id_table SERIAL PRIMARY KEY,
full_name varchar (255) NOT NULL,
temp_file varchar (255) NOT NULL,
last_update timestamp);

########################################################################
########################################################################
########################################################################
########################################################################

