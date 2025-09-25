<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
function getNamaBulanIndo($bulanInggris) {
    $bulan = [
        'January' => 'Januari',
        'February' => 'Februari',
        'March' => 'Maret',
        'April' => 'April',
        'May' => 'Mei',
        'June' => 'Juni',
        'July' => 'Juli',
        'August' => 'Agustus',
        'September' => 'September',
        'October' => 'Oktober',
        'November' => 'November',
        'December' => 'Desember'
    ];
    return $bulan[$bulanInggris] ?? $bulanInggris;
}


require_once 'functions/koneksi.php';
include 'layouts/header.php';

$bulan_ini = date('Y-m');
$bulan_sekarang_label = date('F Y');

// REKAP PENGIRIMAN BULAN INI
$query_pengiriman = $conn->query("
  SELECT pg.tanggal, mb.no_polisi, pr.nama_perusahaan, rt.tujuan, pg.qty, pg.total
  FROM tb_pengiriman pg
  JOIN tb_mobil mb ON pg.id_mobil = mb.id_mobil
  JOIN tb_perusahaan pr ON pg.id_perusahaan = pr.id_perusahaan
  JOIN tb_rute rt ON pg.id_rute = rt.id_rute
  WHERE DATE_FORMAT(pg.tanggal, '%Y-%m') = '$bulan_ini'
  ORDER BY pg.tanggal DESC
");

// STATISTIK BULAN INI
$query_stats = $conn->query("
  SELECT COUNT(*) as total_pengiriman,
         SUM(pg.total) as total_pendapatan,
         COUNT(DISTINCT no_invoice_utama) as total_invoice
  FROM tb_pengiriman pg
  WHERE DATE_FORMAT(pg.tanggal, '%Y-%m') = '$bulan_ini'
");
$stats = $query_stats->fetch_assoc();

// INVOICE TERBARU
$query_invoice = $conn->query("
  SELECT
    inv.no_invoice_utama,
    inv.nama_perusahaan,
    inv.mulai,
    inv.akhir,
    inv.total
  FROM (
    SELECT 
      pg.no_invoice_utama,
      MIN(pg.tanggal) AS mulai,
      MAX(pg.tanggal) AS akhir,
      SUM(pg.total) AS total,
      (SELECT pr.nama_perusahaan
       FROM tb_pengiriman pg2
       JOIN tb_perusahaan pr ON pg2.id_perusahaan = pr.id_perusahaan
       WHERE pg2.no_invoice_utama = pg.no_invoice_utama
       LIMIT 1) AS nama_perusahaan
    FROM tb_pengiriman pg
    WHERE DATE_FORMAT(pg.tanggal, '%Y-%m') = '$bulan_ini'
    GROUP BY pg.no_invoice_utama
  ) AS inv
  ORDER BY inv.akhir DESC
  LIMIT 5
");

?>

<div class="row">

  <!-- Rekap Pengiriman -->
  <div class="col-lg-8">
    <div class="card w-100">
      <div class="card-body">
        <h4 class="card-title">Rekap Pengiriman</h4>
        <p class="card-subtitle">Periode <?= $bulan_sekarang_label ?></p>
        <div class="table-responsive mt-4">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>No Mobil</th>
                <th>Tujuan</th>
                <th>Qty (PCS)</th>
                <th>Ongkos</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $query_pengiriman->fetch_assoc()): ?>
              <tr>
                <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                <td><?= $row['no_polisi'] ?></td>
                <td><?= $row['nama_perusahaan'] ?></td>
                <td><?= $row['qty'] ?></td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistik Bulanan -->
  <div class="col-lg-4">
    <div class="card overflow-hidden">
      <div class="card-body pb-0">
        <h4 class="card-title mb-3">Statistik Bulan <?= getNamaBulanIndo(date('F')) . ' ' . date('Y') ?></h4>

        <div class="py-3 d-flex align-items-center">
          <span class="btn btn-primary rounded-circle hstack justify-content-center">
            <i class="ti ti-truck fs-6"></i>
          </span>
          <div class="ms-3">
            <h5 class="mb-0 fw-bolder fs-4">Total Pengiriman</h5>
            <span class="text-muted fs-3"><?= $stats['total_pengiriman'] ?? 0 ?> Pengiriman</span>
          </div>
        </div>

        <div class="py-3 d-flex align-items-center">
          <span class="btn btn-success rounded-circle hstack justify-content-center">
            <i class="ti ti-cash fs-6"></i>
          </span>
          <div class="ms-3">
            <h5 class="mb-0 fw-bolder fs-4">Total Pendapatan</h5>
            <span class="text-muted fs-3">Rp <?= number_format($stats['total_pendapatan'] ?? 0, 0, ',', '.') ?></span>
          </div>
        </div>

        <div class="py-3 d-flex align-items-center">
          <span class="btn btn-warning rounded-circle hstack justify-content-center">
            <i class="ti ti-file-invoice fs-6"></i>
          </span>
          <div class="ms-3">
            <h5 class="mb-0 fw-bolder fs-4">Invoice Terbit</h5>
            <span class="text-muted fs-3"><?= $stats['total_invoice'] ?? 0 ?> Invoice</span>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Invoice Terbaru -->
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Invoice Terbaru</h4>
        <div class="table-responsive mt-4">
          <table class="table align-middle mb-0">
            <thead class="text-dark fs-4">
              <tr>
                <th>#</th>
                <th>No Invoice</th>
                <th>Customer</th>
                <th>Periode</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 1; while ($inv = $query_invoice->fetch_assoc()): ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= $inv['no_invoice_utama'] ?></td>
                <td><?= $inv['nama_perusahaan'] ?></td>
                <td><?= date('d/m/Y', strtotime($inv['mulai'])) ?> - <?= date('d/m/Y', strtotime($inv['akhir'])) ?></td>
                <td>Rp <?= number_format($inv['total'], 0, ',', '.') ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'layouts/footer.php'; ?>
