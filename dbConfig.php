<?php

// Database connection settings
$host = "localhost";
$user = "root";
$password = "";
$dbname = "programmer_client_activity";
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

// Connect to the database using PDO
$pdo = new PDO($dsn, $user, $password);

// Set the timezone to the Philippines
$pdo->exec("SET time_zone = '+08:00'");
date_default_timezone_set("Asia/Manila");

?>
