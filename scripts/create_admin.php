<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/koneksi.php';

$username = trim(readline('Username administrator [admin]: '));
if ($username === '') {
    $username = 'admin';
}

$name = trim(readline('Nama administrator [Administrator]: '));
if ($name === '') {
    $name = 'Administrator';
}

fwrite(STDOUT, 'Password baru: ');
shell_exec('stty -echo');
$password = trim((string) fgets(STDIN));
shell_exec('stty echo');
fwrite(STDOUT, PHP_EOL);

if (strlen($password) < 12) {
    fwrite(STDERR, "Password minimal 12 karakter.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$level = 'administrator';

$stmt = mysqli_prepare(
    $koneksi,
    'INSERT INTO user (user_nama, user_username, user_password, user_foto, user_level)
     VALUES (?, ?, ?, "", ?)
     ON DUPLICATE KEY UPDATE
       user_nama = VALUES(user_nama),
       user_password = VALUES(user_password),
       user_level = VALUES(user_level)'
);
mysqli_stmt_bind_param($stmt, 'ssss', $name, $username, $hash, $level);
mysqli_stmt_execute($stmt);

fwrite(STDOUT, "Administrator berhasil dibuat atau diperbarui.\n");
