CREATE DATABASE IF NOT EXISTS php_course;

USE php_course;

CREATE TABLE IF NOT EXISTS users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    username VARCHAR(255),
    mail VARCHAR(255),
    age INT(11)
);

CREATE TABLE IF NOT EXISTS cars (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    model VARCHAR(255),
    brand VARCHAR(255),
    price DECIMAL(8,2),
    build_at DATE
);

CREATE TABLE IF NOT EXISTS cars_users (
    id_user int NOT NULL,
    id_car int NOT NULL,
    assigned_at DATE,
    PRIMARY KEY (id_user, id_car),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_car) REFERENCES cars(id)
);
