<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'functions/function_pengiriman.php';
require_once 'layouts/header.php';

$tanggal_hari_ini = date('Y-m-d');
$kode_invoice_utama = "HK-" . date('Ymd');

// Jumlah pengiriman & total biaya
$result_jumlah = $conn->query("SELECT COUNT(*) as total, SUM(total) as total_biaya FROM tb_pengiriman WHERE tanggal = '$tanggal_hari_ini'");
$row = $result_jumlah->fetch_assoc();
$jumlah_pengiriman = $row['total'];
$total_biaya = $row['total_biaya'] ?? 0;

// Ambil daftar pengiriman
$data_pengiriman = $conn->query("
    SELECT pg.*, pr.nama_perusahaan, mb.no_polisi, rt.tujuan
    FROM tb_pengiriman pg
    JOIN tb_perusahaan pr ON pg.id_perusahaan = pr.id_perusahaan
    JOIN tb_mobil mb ON pg.id_mobil = mb.id_mobil
    JOIN tb_rute rt ON pg.id_rute = rt.id_rute
    WHERE pg.tanggal = '$tanggal_hari_ini'
    ORDER BY pg.id_pengiriman DESC
");
?>

<div class="container mt-4">
    <h4 class="mb-4">📦 Pengiriman Hari Ini (<?= $tanggal_hari_ini ?>)</h4>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Invoice Utama:</strong> <?= $kode_invoice_utama ?></p>
            <p><strong>Jumlah Pengiriman:</strong> <?= $jumlah_pengiriman ?></p>
            <p><strong>Total Biaya:</strong> Rp <?= number_format($total_biaya, 0, ',', '.') ?></p>
            <a href="detail_invoice.php?tanggal=<?= $tanggal_hari_ini ?>" class="btn btn-primary">📄 Lihat Detail Invoice</a>
        </div>
    </div>

    <a href="tambah_pengiriman.php" class="btn btn-success mb-3">+ Tambah Pengiriman</a>

    <div class="card">
        <div class="card-body table-responsive">
            <h5 class="card-title mb-3">Daftar Pengiriman Hari Ini</h5>
            <?php if ($data_pengiriman->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Invoice</th>
                        <th>Perusahaan</th>
                        <th>Mobil</th>
                        <th>Rute</th>
                        <th>CT</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Keterangan</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $data_pengiriman->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['no_invoice_pengiriman'] ?></td>
                        <td><?= $row['nama_perusahaan'] ?></td>
                        <td><?= $row['no_polisi'] ?></td>
                        <td><?= $row['tujuan'] ?></td>
                        <td><?= $row['ct'] ?></td>
                        <td><?= $row['qty'] ?></td>
                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td>
                            <a href="detail_invoice.php?tanggal=<?= $tanggal_hari_ini ?>" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <div class="alert alert-warning">Belum ada pengiriman hari ini.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once 'layouts/footer.php'; ?>
