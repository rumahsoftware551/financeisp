<?php
/**
 * ISPfinance V1.0 Permission Helper
 * RBAC permission checker
 */

if (!function_exists('cek_permission')) {
    function cek_permission($permission)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        global $koneksi;

        $user_id = (int) $_SESSION['user_id'];

        $sql = "SELECT COUNT(*) AS total
                FROM user_roles ur
                JOIN role_permissions rp ON rp.role_id = ur.role_id
                JOIN permissions p ON p.id = rp.permission_id
                WHERE ur.user_id = ?
                AND p.permission_key = ?";

        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, 'is', $user_id, $permission);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);

        return ($data['total'] ?? 0) > 0;
    }
}
?>