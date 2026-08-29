<?php
include '../koneksi.php';
include '../library/dashboard_query.php';
include '../library/permission.php';

$data=dashboard_summary($koneksi);
?>
<link rel="stylesheet" href="../assets/css/ispfinance-dashboard.css">

<div class="isp-dashboard-section">
<h2>ISPfinance Dashboard</h2>
<p>Ringkasan operasional keuangan ISP</p>

<div class="isp-kpi-grid">
<div class="isp-kpi-card"><div class="isp-kpi-title">Saldo Kas & Bank</div><div class="isp-kpi-value">Rp <?=number_format($data['saldo_kas']);?></div></div>
<div class="isp-kpi-card"><div class="isp-kpi-title">Pendapatan Bulan Ini</div><div class="isp-kpi-value">Rp <?=number_format($data['pendapatan']);?></div></div>
<div class="isp-kpi-card"><div class="isp-kpi-title">Pengeluaran Bulan Ini</div><div class="isp-kpi-value">Rp <?=number_format($data['pengeluaran']);?></div></div>
<div class="isp-kpi-card"><div class="isp-kpi-title">Laba/Rugi Sementara</div><div class="isp-kpi-value">Rp <?=number_format($data['laba']);?></div></div>
<div class="isp-kpi-card"><div class="isp-kpi-title">Jumlah Piutang</div><div class="isp-kpi-value"><?=$data['piutang'];?></div></div>
</div>
</div>
