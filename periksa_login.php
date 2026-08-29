<?php

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string) ($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: index.php?alert=gagal');
    exit;
}

$stmt = mysqli_prepare($koneksi, 'SELECT * FROM user WHERE user_username = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$login = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($login);

$passwordValid = false;
if ($data) {
    $storedHash = (string) $data['user_password'];
    $passwordValid = password_verify($password, $storedHash)
        || (strlen($storedHash) === 32 && hash_equals($storedHash, md5($password)));

    if ($passwordValid && strlen($storedHash) === 32) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upgrade = mysqli_prepare($koneksi, 'UPDATE user SET user_password = ? WHERE user_id = ?');
        mysqli_stmt_bind_param($upgrade, 'si', $newHash, $data['user_id']);
        mysqli_stmt_execute($upgrade);
    }
}

if (!$passwordValid) {
    header('Location: index.php?alert=gagal');
    exit;
}

session_start();
session_regenerate_id(true);
$_SESSION['id'] = $data['user_id'];
$_SESSION['nama'] = $data['user_nama'];
$_SESSION['username'] = $data['user_username'];
$_SESSION['level'] = $data['user_level'];

if ($data['user_level'] === 'administrator') {
    $_SESSION['status'] = 'administrator_logedin';
    header('Location: admin/');
    exit;
}

if ($data['user_level'] === 'manajemen') {
    $_SESSION['status'] = 'manajemen_logedin';
    header('Location: manajemen/');
    exit;
}

session_destroy();
header('Location: index.php?alert=gagal');
exit;
