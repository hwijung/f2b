CREATE DATABASE f2b;
use f2b;
 
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
	wp_hostname varchar(255) NOT NULL,
	wp_apipath varchar(255) NOT NULL,
	wp_id varchar(255) NOT NULL,
	wp_password varchar(255) NOT NULL,
	PRIMARY KEY (user)
);
