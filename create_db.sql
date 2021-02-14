#CAUTION!!! will DROP current DB!!
drop database if exists user_config;
create database user_config;
use user_config;

# DISCLAIMER - column sizes might not be optimal
# oh well! Q)

# this is both for regular users and admin users
# (admins have 'type' field set (not null) )
# not sure if this is a bad practise
# NOTE: because it was abiguous in the exersize
# I made the convention that admin is inserted in
# the database "by hand" 
# (since admin has access via "desktop computer")
# but after that he can access the site by the same 
# login page as the regular users
create table users(
	id int(11) not null auto_increment,
    username varchar(100) not null,
    email varchar(200) unique not null,
    password varchar(255) not null,
    type int default null,
    primary key (id)
);

# all files reside here
create table files(
    Id int(11) not null,
    arch_id int(11) not null auto_increment,
    name varchar(255),
    size int(11),
    downloads int(11),
    method varchar(255),
    serverIP varchar(255),
    # 15 total digits, 12 after the ','
    server_lat float(15, 12),
    server_long float(15, 12),
    domain varchar(255),
    age varchar(255),
    status varchar(255),
    req_content_type varchar(255),
    content_type varchar(255),
    expires varchar(255),
    last_modified varchar(255),
    max_age varchar(255),
    cache_private tinyint(10),
    cache_public tinyint(10),
    cache_no_store tinyint(10),
    cache_no_cache tinyint(10),
    upload_date date,
    req_max_stale int(11),
    req_min_fresh int (11),
    foreign key (Id) references users(id),
    primary key(arch_id)
);

# keep ip log for every user
create table ips(
	u_id int(11) not null,
    # IPv4 -> 32 bit
    # IPv6 -> 128 bits
    # tinytext holds up to 256 bytes/255 characters - is this enough? *evil face*
    ip tinytext,
    foreign key(u_id) references users(id)
);

#admin insert
#insert into users values('0', 'admin', 'admin@gmail.com', '123456789^&%$A', 1);
