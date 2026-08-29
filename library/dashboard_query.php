<?php
// ISPfinance V1 dashboard data helper

function dashboard_summary($koneksi){
    $data=[];

    $q=mysqli_query($koneksi,"SELECT COALESCE(SUM(bank_saldo),0) total FROM bank");
    $data['saldo_kas']=mysqli_fetch_assoc($q)['total'] ?? 0;

    $bulan=date('Y-m');
    $q=mysqli_query($koneksi,"SELECT 
        COALESCE(SUM(CASE WHEN transaksi_jenis='Pemasukan' THEN transaksi_nominal ELSE 0 END),0) masuk,
        COALESCE(SUM(CASE WHEN transaksi_jenis='Pengeluaran' THEN transaksi_nominal ELSE 0 END),0) keluar
        FROM transaksi WHERE DATE_FORMAT(transaksi_tanggal,'%Y-%m')='$bulan'");
    $row=mysqli_fetch_assoc($q);

    $data['pendapatan']=$row['masuk'] ?? 0;
    $data['pengeluaran']=$row['keluar'] ?? 0;
    $data['laba']=$data['pendapatan']-$data['pengeluaran'];

    $q=mysqli_query($koneksi,"SELECT COUNT(*) jumlah FROM piutang");
    $data['piutang']=$row=mysqli_fetch_assoc($q)['jumlah'] ?? 0;

    return $data;
}
