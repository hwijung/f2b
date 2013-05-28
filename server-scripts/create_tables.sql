CREATE DATABASE f2w;
use f2w;
 
CREATE TABLE user
(
	user varchar(255) NOT NULL,
	password varchar(255) NOT NULL,
	PRIMARY KEY (user)
);

CREATE TABLE fb_account
(
	user varchar(255) NOT NULL,
	fb_user varchar(32) NOT NULL,
	fb_access_token varchar(255) NOT NULL,
	fb_name varchar (32) NOT NULL,
	PRIMARY KEY (user)
);

CREATE TABLE wp_account
(
	user varchar(255) NOT NULL,
	wp_address varchar(255) NOT NULL,
	wp_id varchar(255) NOT NULL,
	wp_password varchar(255) NOT NULL,
	PRIMARY KEY (user)
);
