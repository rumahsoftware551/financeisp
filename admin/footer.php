  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>&copy; <?php echo date('Y'); ?> FinanceISP</strong> &middot; Sistem Informasi Keuangan
  </footer>

  
</div>


<script src="../assets/bower_components/jquery/dist/jquery.min.js"></script>

<script src="../assets/bower_components/jquery-ui/jquery-ui.min.js"></script>

<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>

<script src="../assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script src="../assets/bower_components/raphael/raphael.min.js"></script>
<script src="../assets/bower_components/morris.js/morris.min.js"></script>

<script src="../assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>


<script src="../assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

<script src="../assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

<script src="../assets/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>

<script src="../assets/bower_components/moment/min/moment.min.js"></script>
<script src="../assets/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="../assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script src="../assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>

<script src="../assets/bower_components/fastclick/lib/fastclick.js"></script>

<script src="../assets/dist/js/adminlte.min.js"></script>

<script src="../assets/dist/js/pages/dashboard.js"></script>

<script src="../assets/dist/js/demo.js"></script>
<script src="../assets/bower_components/ckeditor/ckeditor.js"></script>
<script src="../assets/bower_components/chart.js/Chart.min.js"></script>

<script>
  $(document).ready(function(){

   var currentPage = window.location.pathname.split('/').pop() || 'index.php';
   $('.sidebar-menu a').each(function(){
     var target = ($(this).attr('href') || '').split('?')[0];
     if(target === currentPage){
       $(this).closest('li').addClass('active');
       $(this).closest('.treeview').addClass('active menu-open');
     }
   });

   // $(".edit").hide();

   $('#table-datatable').DataTable({
    'paging'      : true,
    'lengthChange': false,
    'searching'   : true,
    'ordering'    : false,
    'info'        : true,
    'autoWidth'   : true,
    "pageLength": 50
  });



 });
  
  $('#datepicker').datepicker({
    autoclose: true,
    format: 'dd/mm/yyyy',
  }).datepicker("setDate", new Date());

  $('.datepicker2').datepicker({
    autoclose: true,
    format: 'yyyy/mm/dd',
  });


</script>


<script>
  var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

  var barChartData = {
    labels : ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
    datasets : [
    {
      label: 'Pemasukan',
      fillColor : "rgba(32, 183, 165, 0.12)",
      strokeColor : "rgba(32, 183, 165, 1)",
      pointColor: "rgba(32, 183, 165, 1)",
      pointStrokeColor: "#ffffff",
      highlightFill: "rgba(32, 183, 165, 0.2)",
      highlightStroke: "rgba(32, 183, 165, 1)",
      data : [
      <?php
      for($bulan=1;$bulan<=12;$bulan++){
        $thn_ini = date('Y');
        $pemasukan = mysqli_query($koneksi,"select sum(transaksi_nominal) as total_pemasukan from transaksi where transaksi_jenis='Pemasukan' and month(transaksi_tanggal)='$bulan' and year(transaksi_tanggal)='$thn_ini'");
        $pem = mysqli_fetch_assoc($pemasukan);

        // $total = str_replace(",", "44", number_format($pem['total_pemasukan']));
        $total = $pem['total_pemasukan'];
        if($pem['total_pemasukan'] == ""){
          echo "0,";
        }else{
          echo $total.",";
        }

      }
      ?>
      ]
    },
    {
      label: 'Pengeluaran',
      fillColor : "rgba(239, 91, 103, 0.1)",
      strokeColor : "rgba(239, 91, 103, 1)",
      pointColor: "rgba(239, 91, 103, 1)",
      pointStrokeColor: "#ffffff",
      highlightFill : "rgba(239, 91, 103, 0.2)",
      highlightStroke : "rgba(239, 91, 103, 1)",
      data : [
      <?php
      for($bulan=1;$bulan<=12;$bulan++){
        $thn_ini = date('Y');
        $pengeluaran = mysqli_query($koneksi,"select sum(transaksi_nominal) as total_pengeluaran from transaksi where transaksi_jenis='pengeluaran' and month(transaksi_tanggal)='$bulan' and year(transaksi_tanggal)='$thn_ini'");
        $peng = mysqli_fetch_assoc($pengeluaran);

        // $total = str_replace(",", "44", number_format($peng['total_pengeluaran']));
        $total = $peng['total_pengeluaran'];
        if($peng['total_pengeluaran'] == ""){
          echo "0,";
        }else{

          echo $total.",";
        }
      }
      ?>
      ]
    }
    ]

  }


  var barChartData2 = {
    labels : [
    <?php 
    $tahun = mysqli_query($koneksi,"select distinct year(transaksi_tanggal) as tahun from transaksi order by year(transaksi_tanggal) asc");
    while($t = mysqli_fetch_array($tahun)){
      ?>
      "<?php echo $t['tahun']; ?>",
      <?php 
    }
    ?>
    ],
    datasets : [
    {
      label: 'Pemasukan',
      fillColor : "rgba(32, 183, 165, 0.7)",
      strokeColor : "rgba(32, 183, 165, 1)",
      highlightFill: "rgba(32, 183, 165, 0.85)",
      highlightStroke: "rgba(32, 183, 165, 1)",
      data : [
      <?php
      $tahun = mysqli_query($koneksi,"select distinct year(transaksi_tanggal) as tahun from transaksi order by year(transaksi_tanggal) asc");
      while($t = mysqli_fetch_array($tahun)){
        $thn = $t['tahun'];
        $pemasukan = mysqli_query($koneksi,"select sum(transaksi_nominal) as total_pemasukan from transaksi where transaksi_jenis='Pemasukan' and year(transaksi_tanggal)='$thn'");
        $pem = mysqli_fetch_assoc($pemasukan);
        $total = $pem['total_pemasukan'];
        if($pem['total_pemasukan'] == ""){
          echo "0,";
        }else{
          echo $total.",";
        }

      }
      ?>
      ]
    },
    {
      label: 'Pengeluaran',
      fillColor : "rgba(239, 91, 103, 0.7)",
      strokeColor : "rgba(239, 91, 103, 1)",
      highlightFill : "rgba(239, 91, 103, 0.85)",
      highlightStroke : "rgba(239, 91, 103, 1)",
      data : [
      <?php
      $tahun = mysqli_query($koneksi,"select distinct year(transaksi_tanggal) as tahun from transaksi order by year(transaksi_tanggal) asc");
      while($t = mysqli_fetch_array($tahun)){
        $thn = $t['tahun'];
        $pemasukan = mysqli_query($koneksi,"select sum(transaksi_nominal) as total_pengeluaran from transaksi where transaksi_jenis='Pengeluaran' and year(transaksi_tanggal)='$thn'");
        $pem = mysqli_fetch_assoc($pemasukan);
        $total = $pem['total_pengeluaran'];
        if($pem['total_pengeluaran'] == ""){
          echo "0,";
        }else{
          echo $total.",";
        }

      }
      ?>
      ]
    }
    ]

  }



  window.onload = function(){
    var monthlyCanvas = document.getElementById("grafik1");
    if(monthlyCanvas){
      window.financeMonthlyChart = new Chart(monthlyCanvas.getContext("2d")).Line(barChartData, {
       responsive : true,
       animation: true,
       bezierCurve: true,
       datasetFill: false,
       pointDotRadius: 3,
       scaleGridLineColor: "rgba(229,235,243,0.7)",
       tooltipFillColor: "rgba(6,42,91,0.92)",
       multiTooltipTemplate: "<%= datasetLabel %> - Rp.<%= value.toLocaleString() %>,-"
     });
    }

    var yearlyCanvas = document.getElementById("grafik2");
    if(yearlyCanvas){
      window.financeYearlyChart = new Chart(yearlyCanvas.getContext("2d")).Bar(barChartData2, {
       responsive : true,
       animation: true,
       barValueSpacing : 10,
       barDatasetSpacing : 3,
       scaleGridLineColor: "rgba(229,235,243,0.7)",
       tooltipFillColor: "rgba(6,42,91,0.92)",
       multiTooltipTemplate: "<%= datasetLabel %> - Rp.<%= value.toLocaleString() %>,-"
     });
    }




    


  }












</script>

</body>
</html>
