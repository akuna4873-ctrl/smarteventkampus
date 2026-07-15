<?php
/**
 * KONEKSI DATABASE
 * Smart Event Campus
 */

define('DB_HOST', 'sql206.infinityfree.com');
define('DB_USER', 'if0_42392162');
define('DB_PASS', 'ucuppunya123');
define('DB_NAME', 'if0_42392162_smartcampus');

function getConnection()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
