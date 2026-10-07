<?php

$host = 'localhost';
$dbName = 'file_archive_db';
$username = 'root';
$password = 'mysql1234';

$conn = new mysqli(
    $host,
    $username,
    $password,
    $dbName
);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');