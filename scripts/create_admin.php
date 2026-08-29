<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$nginxEnvironment = '/etc/nginx/snippets/financeisp-db.conf';
if (is_readable($nginxEnvironment)) {
    foreach (file($nginxEnvironment, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (preg_match('/^fastcgi_param\s+(FINANCEISP_DB_[A-Z]+)\s+(.+);$/', trim($line), $matches)) {
            putenv($matches[1] . '=' . trim($matches[2], "\"'"));
        }
    }
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

$find = mysqli_prepare(
    $koneksi,
    'SELECT user_id FROM user WHERE user_level = "administrator" ORDER BY user_id LIMIT 1'
);
mysqli_stmt_execute($find);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($find));

if ($existing) {
    $stmt = mysqli_prepare(
        $koneksi,
        'UPDATE user
         SET user_nama = ?, user_username = ?, user_password = ?, user_level = ?
         WHERE user_id = ?'
    );
    mysqli_stmt_bind_param($stmt, 'ssssi', $name, $username, $hash, $level, $existing['user_id']);
} else {
    $stmt = mysqli_prepare(
        $koneksi,
        'INSERT INTO user (user_nama, user_username, user_password, user_foto, user_level)
         VALUES (?, ?, ?, "", ?)'
    );
    mysqli_stmt_bind_param($stmt, 'ssss', $name, $username, $hash, $level);
}

mysqli_stmt_execute($stmt);

fwrite(STDOUT, "Administrator berhasil dibuat atau diperbarui.\n");
