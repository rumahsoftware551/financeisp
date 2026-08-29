<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Masuk - FinanceISP</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <meta name="theme-color" content="#062a5b">
  <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/dist/css/financeisp-modern.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,600,700,800">
</head>
<body class="financeisp-login-page">
  <main class="finance-login-shell">
    <section class="finance-login-visual">
      <div class="finance-login-brand">
        <span class="brand-mark"><i class="fa fa-line-chart"></i></span>
        <span>FinanceISP</span>
      </div>

      <div class="finance-login-copy">
        <span class="eyebrow"><i class="fa fa-shield"></i>&nbsp; Keuangan digital yang lebih rapi</span>
        <h1>Kelola Keuangan dengan Lebih Cerdas.</h1>
        <p>Pantau pemasukan, pengeluaran, rekening, hutang, dan piutang dalam satu dashboard yang aman dan mudah digunakan.</p>
      </div>

      <div class="finance-login-features">
        <div class="finance-login-feature">
          <i class="fa fa-lock"></i>
          <strong>Akses Aman</strong>
          <span>Akun dan data dikelola dalam sistem terpusat.</span>
        </div>
        <div class="finance-login-feature">
          <i class="fa fa-bolt"></i>
          <strong>Praktis</strong>
          <span>Catat transaksi dan saldo dengan lebih cepat.</span>
        </div>
        <div class="finance-login-feature">
          <i class="fa fa-pie-chart"></i>
          <strong>Insight Jelas</strong>
          <span>Ringkasan finansial tersaji secara visual.</span>
        </div>
      </div>
    </section>

    <section class="finance-login-panel">
      <div class="finance-login-card">
        <h2>Selamat datang</h2>
        <p>Masuk ke akun Anda untuk melihat dan mengelola seluruh aktivitas keuangan.</p>

        <?php if (isset($_GET['alert'])) { ?>
          <?php if ($_GET['alert'] === 'gagal') { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp; Username atau password tidak sesuai.</div>
          <?php } elseif ($_GET['alert'] === 'logout') { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i>&nbsp; Anda berhasil keluar dari aplikasi.</div>
          <?php } elseif ($_GET['alert'] === 'belum_login') { ?>
            <div class="alert alert-warning"><i class="fa fa-info-circle"></i>&nbsp; Silakan masuk untuk mengakses dashboard.</div>
          <?php } ?>
        <?php } ?>

        <form action="periksa_login.php" method="POST" autocomplete="on">
          <div class="form-group">
            <label for="username">Username</label>
            <div class="finance-input-wrap">
              <i class="fa fa-user"></i>
              <input id="username" type="text" class="form-control" name="username" placeholder="Masukkan username" required autocomplete="username" autofocus>
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="finance-input-wrap">
              <i class="fa fa-lock"></i>
              <input id="password" type="password" class="form-control" name="password" placeholder="Masukkan password" required autocomplete="current-password">
            </div>
          </div>

          <button type="submit" class="finance-login-submit">Masuk ke Dashboard&nbsp; <i class="fa fa-arrow-right"></i></button>
        </form>

        <div class="finance-login-footer">&copy; <?php echo date('Y'); ?> FinanceISP &middot; Sistem Informasi Keuangan</div>
      </div>
    </section>
  </main>

  <script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
