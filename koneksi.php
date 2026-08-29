<?php

$dbHost = getenv('FINANCEISP_DB_HOST') ?: '127.0.0.1';
$dbPort = (int) (getenv('FINANCEISP_DB_PORT') ?: 3306);
$dbName = getenv('FINANCEISP_DB_NAME') ?: 'keuangan';
$dbUser = getenv('FINANCEISP_DB_USER') ?: 'financeisp';
$dbPass = getenv('FINANCEISP_DB_PASSWORD') ?: '';

$koneksi = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

if (!$koneksi) {
    http_response_code(500);
    error_log('Database connection failed: ' . mysqli_connect_error());
    exit('Koneksi database gagal. Silakan hubungi administrator.');
}

mysqli_set_charset($koneksi, 'utf8mb4');
