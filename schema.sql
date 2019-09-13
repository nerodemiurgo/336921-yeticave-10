CREATE DATABASE yeticave
	DEFAULT CHARACTER SET utf8
	DEFAULT COLLATE utf8_general_ci;
	
USE yeticave;
	
CREATE TABLE lot (
	id INT AUTO_INCREMENT PRIMARY KEY,
	created_at DATETIME DEFAULT NOW(),
	name CHAR(128) NOT NULL,
	description TEXT(2048) NOT NULL,
	img CHAR(128) NOT NULL,
	start_price INT NOT NULL,
	price INT NOT NULL,
	dt_finish DATE NOT NULL,
	rate_step INT NOT NULL,
	category_id INT NOT NULL,
	author_id INT NOT NULL,
	winner_id INT
);

CREATE TABLE user (
	id INT AUTO_INCREMENT PRIMARY KEY,
	registered_at DATETIME DEFAULT NOW(),
	user_name CHAR(128) NOT NULL,
	email CHAR(128) NOT NULL UNIQUE,
	password CHAR(128) NOT NULL,
	contact CHAR(255) NOT NULL
);

CREATE TABLE category (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name CHAR(128) NOT NULL UNIQUE,
	code CHAR(128) NOT NULL
);

CREATE TABLE rate (
	id INT AUTO_INCREMENT PRIMARY KEY,
	created_at DATETIME DEFAULT NOW(),
	bid INT NOT NULL,
	user_id INT NOT NULL,
	lot_id INT NOT NULL
);

/* Добавление индексов для фуллтекст поиска */
CREATE FULLTEXT INDEX lot_ft_search ON lot (name, description);