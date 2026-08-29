<?php include 'header.php'; ?>

<?php
function finance_sum_value($connection, $query, $field)
{
  $result = mysqli_query($connection, $query);
  $row = mysqli_fetch_assoc($result);
  return (float) ($row[$field] ?? 0);
}

$month = date('m');
$year = date('Y');
$monthNames = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$dayNames = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
$todayLabel = $dayNames[(int) date('w')] . ', ' . date('d') . ' ' . $monthNames[(int) date('n') - 1] . ' ' . date('Y');

$totalBalance = finance_sum_value($koneksi, "SELECT COALESCE(SUM(bank_saldo), 0) AS total FROM bank", 'total');
$monthlyIncome = finance_sum_value($koneksi, "SELECT COALESCE(SUM(transaksi_nominal), 0) AS total FROM transaksi WHERE transaksi_jenis='Pemasukan' AND MONTH(transaksi_tanggal)='$month' AND YEAR(transaksi_tanggal)='$year'", 'total');
$monthlyExpense = finance_sum_value($koneksi, "SELECT COALESCE(SUM(transaksi_nominal), 0) AS total FROM transaksi WHERE transaksi_jenis='Pengeluaran' AND MONTH(transaksi_tanggal)='$month' AND YEAR(transaksi_tanggal)='$year'", 'total');
$allIncome = finance_sum_value($koneksi, "SELECT COALESCE(SUM(transaksi_nominal), 0) AS total FROM transaksi WHERE transaksi_jenis='Pemasukan'", 'total');
$allExpense = finance_sum_value($koneksi, "SELECT COALESCE(SUM(transaksi_nominal), 0) AS total FROM transaksi WHERE transaksi_jenis='Pengeluaran'", 'total');
$netCashflow = $allIncome - $allExpense;
$accountCount = finance_sum_value($koneksi, "SELECT COUNT(*) AS total FROM bank", 'total');

$accounts = mysqli_query($koneksi, "SELECT * FROM bank ORDER BY bank_saldo DESC, bank_id ASC LIMIT 5");
$recentTransactions = mysqli_query($koneksi, "SELECT transaksi.*, kategori.kategori FROM transaksi LEFT JOIN kategori ON kategori_id=transaksi_kategori ORDER BY transaksi_id DESC LIMIT 7");
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Dashboard<small>Ringkasan dan aktivitas keuangan terbaru</small></h1>
    <ol class="breadcrumb">
      <li><a href="index.php"><i class="fa fa-dashboard"></i> FinanceISP</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <section class="content">
    <div class="finance-welcome">
      <div class="finance-welcome-content">
        <span class="finance-welcome-eyebrow"><i class="fa fa-shield"></i>&nbsp; Dashboard Administrator</span>
        <h2>Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?>.</h2>
        <p>Pantau arus kas, saldo rekening, dan transaksi terbaru dalam satu tampilan yang bersih dan terpusat.</p>
      </div>
      <div class="finance-welcome-date"><i class="fa fa-calendar-o"></i>&nbsp; <?php echo $todayLabel; ?></div>
    </div>

    <div class="row">
      <div class="col-lg-3 col-sm-6">
        <div class="finance-stat-card">
          <div class="finance-stat-top"><span class="finance-stat-label">Total Saldo Rekening</span><span class="finance-stat-icon"><i class="fa fa-university"></i></span></div>
          <div class="finance-stat-value">Rp <?php echo number_format($totalBalance, 0, ',', '.'); ?></div>
          <div class="finance-stat-meta"><?php echo (int) $accountCount; ?> rekening terdaftar</div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="finance-stat-card">
          <div class="finance-stat-top"><span class="finance-stat-label">Pemasukan Bulan Ini</span><span class="finance-stat-icon income"><i class="fa fa-arrow-down"></i></span></div>
          <div class="finance-stat-value">Rp <?php echo number_format($monthlyIncome, 0, ',', '.'); ?></div>
          <div class="finance-stat-meta positive"><i class="fa fa-calendar-check-o"></i>&nbsp; <?php echo $monthNames[(int) date('n') - 1]; ?></div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="finance-stat-card">
          <div class="finance-stat-top"><span class="finance-stat-label">Pengeluaran Bulan Ini</span><span class="finance-stat-icon expense"><i class="fa fa-arrow-up"></i></span></div>
          <div class="finance-stat-value">Rp <?php echo number_format($monthlyExpense, 0, ',', '.'); ?></div>
          <div class="finance-stat-meta negative"><i class="fa fa-calendar-check-o"></i>&nbsp; <?php echo $monthNames[(int) date('n') - 1]; ?></div>
        </div>
      </div>
      <div class="col-lg-3 col-sm-6">
        <div class="finance-stat-card">
          <div class="finance-stat-top"><span class="finance-stat-label">Arus Kas Bersih</span><span class="finance-stat-icon accounts"><i class="fa fa-line-chart"></i></span></div>
          <div class="finance-stat-value">Rp <?php echo number_format($netCashflow, 0, ',', '.'); ?></div>
          <div class="finance-stat-meta <?php echo $netCashflow >= 0 ? 'positive' : 'negative'; ?>"><?php echo $netCashflow >= 0 ? 'Saldo keseluruhan positif' : 'Pengeluaran melebihi pemasukan'; ?></div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <div class="finance-panel">
          <div class="finance-panel-header">
            <div><div class="finance-panel-title">Tren Keuangan Bulanan</div><div class="finance-panel-subtitle">Perbandingan pemasukan dan pengeluaran tahun <?php echo date('Y'); ?></div></div>
            <span class="finance-pill">12 bulan</span>
          </div>
          <div class="finance-panel-body finance-chart-wrap"><canvas id="grafik1" style="height: 280px;"></canvas></div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="finance-panel">
          <div class="finance-panel-header"><div><div class="finance-panel-title">Aksi Cepat</div><div class="finance-panel-subtitle">Kelola data utama</div></div></div>
          <div class="finance-panel-body">
            <div class="finance-quick-grid">
              <a class="finance-quick-action" href="transaksi.php"><i class="fa fa-exchange"></i><span>Tambah Transaksi</span></a>
              <a class="finance-quick-action" href="bank.php"><i class="fa fa-university"></i><span>Kelola Rekening</span></a>
              <a class="finance-quick-action" href="laporan.php"><i class="fa fa-file-text-o"></i><span>Buka Laporan</span></a>
              <a class="finance-quick-action" href="user.php"><i class="fa fa-users"></i><span>Kelola Pengguna</span></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-5">
        <div class="finance-panel">
          <div class="finance-panel-header">
            <div><div class="finance-panel-title">Ringkasan Rekening</div><div class="finance-panel-subtitle">Saldo rekening tertinggi</div></div>
            <a class="finance-panel-link" href="bank.php">Lihat semua</a>
          </div>
          <div class="finance-panel-body">
            <?php if (mysqli_num_rows($accounts) > 0) { ?>
              <?php while ($account = mysqli_fetch_assoc($accounts)) { ?>
                <div class="finance-account-item">
                  <span class="finance-list-icon"><i class="fa fa-credit-card"></i></span>
                  <div class="finance-list-main">
                    <div class="finance-list-title"><?php echo htmlspecialchars($account['bank_nama']); ?></div>
                    <div class="finance-list-meta"><?php echo htmlspecialchars($account['bank_pemilik']); ?><?php echo $account['bank_nomor'] !== '' ? ' &middot; ' . htmlspecialchars($account['bank_nomor']) : ''; ?></div>
                  </div>
                  <div class="finance-list-value">Rp <?php echo number_format($account['bank_saldo'], 0, ',', '.'); ?></div>
                </div>
              <?php } ?>
            <?php } else { ?>
              <div class="finance-empty"><i class="fa fa-university"></i>Belum ada rekening bank.</div>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="finance-panel">
          <div class="finance-panel-header">
            <div><div class="finance-panel-title">Transaksi Terbaru</div><div class="finance-panel-subtitle">Aktivitas finansial terkini</div></div>
            <a class="finance-panel-link" href="transaksi.php">Lihat semua</a>
          </div>
          <div class="finance-panel-body">
            <?php if (mysqli_num_rows($recentTransactions) > 0) { ?>
              <?php while ($transaction = mysqli_fetch_assoc($recentTransactions)) { $isIncome = $transaction['transaksi_jenis'] === 'Pemasukan'; ?>
                <div class="finance-transaction-item">
                  <span class="finance-list-icon"><i class="fa <?php echo $isIncome ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i></span>
                  <div class="finance-list-main">
                    <div class="finance-list-title"><?php echo htmlspecialchars($transaction['transaksi_keterangan']); ?></div>
                    <div class="finance-list-meta"><?php echo date('d M Y', strtotime($transaction['transaksi_tanggal'])); ?> &middot; <?php echo htmlspecialchars($transaction['kategori'] ?? 'Tanpa kategori'); ?></div>
                  </div>
                  <div class="finance-list-value <?php echo $isIncome ? 'income' : 'expense'; ?>"><?php echo $isIncome ? '+' : '-'; ?> Rp <?php echo number_format($transaction['transaksi_nominal'], 0, ',', '.'); ?></div>
                </div>
              <?php } ?>
            <?php } else { ?>
              <div class="finance-empty"><i class="fa fa-exchange"></i>Belum ada transaksi.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <div class="finance-panel">
      <div class="finance-panel-header">
        <div><div class="finance-panel-title">Performa Keuangan Tahunan</div><div class="finance-panel-subtitle">Riwayat pemasukan dan pengeluaran per tahun</div></div>
        <span class="finance-pill">Tahunan</span>
      </div>
      <div class="finance-panel-body finance-chart-wrap"><canvas id="grafik2" style="height: 280px;"></canvas></div>
    </div>
  </section>
</div>

<?php include 'footer.php'; ?>
