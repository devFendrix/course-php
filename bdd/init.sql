CREATE DATABASE IF NOT EXISTS php_course;

USE php_course;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    username VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(255),
    brand VARCHAR(255),
    price DECIMAL(8,2),
    nb_seat INT(11)
);
