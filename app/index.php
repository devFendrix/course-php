<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', "1024M");
ini_set('max_execution_time', 600);

require './config/database.php';
require './entities/Car.php';
require './entities/User.php';

$db = connect();
?>