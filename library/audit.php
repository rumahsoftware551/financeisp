<?php
/**
 * ISPfinance V1.0 Audit Logger
 */

function audit_log($module, $action, $reference_id = null, $old_data = null, $new_data = null)
{
    global $koneksi;

    if (!$koneksi) {
        return false;
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';

    $sql = "INSERT INTO audit_logs
            (user_id,module,action,reference_id,old_data,new_data,ip_address)
            VALUES (?,?,?,?,?,?,?)";

    $stmt = mysqli_prepare($koneksi, $sql);

    $old = $old_data ? json_encode($old_data) : null;
    $new = $new_data ? json_encode($new_data) : null;

    mysqli_stmt_bind_param(
        $stmt,
        'issssss',
        $user_id,
        $module,
        $action,
        $reference_id,
        $old,
        $new,
        $ip
    );

    return mysqli_stmt_execute($stmt);
}
?>