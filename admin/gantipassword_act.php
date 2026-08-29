<?php 
include '../koneksi.php';
session_start();
$id = $_SESSION['id'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = mysqli_prepare($koneksi, "UPDATE user SET user_password = ? WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'si', $password, $id);
mysqli_stmt_execute($stmt);

header("location:gantipassword.php?alert=sukses");
exit;
