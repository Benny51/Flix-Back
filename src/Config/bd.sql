drop flix;
create database if not exists flix;

use flix;

create table if not exists user(
    id_user int not null auto_increment,
    email varchar(200) not null,
    username varchar(200) not null,
    password_ varchar(100) not null,
    primary key(id_user)
);

create table if not exists oeuvre(
    id_oeuvre int not null auto_increment,
    name_oeuvre varchar(150) not null,
    category varchar(150) not null,
    description_ varchar(300) not null,
    image_path varchar(200) not null,
    primary key(id_oeuvre)
);

insert into user (email,username,password_) values("admin","admin@admin.com","admin");