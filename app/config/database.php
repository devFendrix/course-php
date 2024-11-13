<?php

function connect(){
    try {
        $db = new PDO('mysql:host=php-course-db;port=3306;dbname=php_course', 'dev', 'dev');
    } catch(Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('SET CHARACTER SET utf8');

    return $db;
}