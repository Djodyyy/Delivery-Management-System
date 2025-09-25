<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'functions/function_pengiriman.php';

// Handler Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'edit') {
    updatePengiriman($_POST);
    header("Location: detail_invoice.php?tanggal=" . $_GET['tanggal'] . "&status=edit");
    exit;
}

// Handler Hapus
if (isset($_GET['hapus'])) {
    deletePengiriman($_GET['hapus']);
    header("Location: detail_invoice.php?tanggal=" . $_GET['tanggal'] . "&status=hapus");
    exit;
}

require_once 'layouts/header.php';

$tanggal = $_GET['tanggal'] ?? '';
?>

<div class="container mt-4">
    <h4 class="mb-4">Detail Invoice Pengiriman</h4>

    <form method="get" class="row mb-4">
        <div class="col-md-4">
            <label for="tanggal" class="form-label">Tanggal Pengiriman</label>
            <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>" required>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Cari</button>
            <a href="detail_invoice.php" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <?php if (!empty($tanggal)): ?>
        <?php
        $query_invoice = $conn->prepare("SELECT DISTINCT no_invoice_utama FROM tb_pengiriman WHERE tanggal = ?");
        $query_invoice->bind_param("s", $tanggal);
        $query_invoice->execute();
        $result_invoice = $query_invoice->get_result();
        ?>

        <?php if ($result_invoice->num_rows > 0): ?>
            <?php while ($inv = $result_invoice->fetch_assoc()): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                        <div>
                            Invoice Utama: <strong><?= $inv['no_invoice_utama'] ?></strong> (<?= $tanggal ?>)
                        </div>
                        <a href="cetak_invoice.php?tanggal=<?= $tanggal ?>" class="btn btn-light btn-sm" target="_blank">
                            🖨 Cetak Invoice (PDF)
                        </a>
                    </div>

                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>No. Invoice</th>
                                    <th>Tujuan (Perusahaan)</th>
                                    <th>Mobil</th>
                                    <th>Rute</th>
                                    <th>CT</th>
                                    <th>Qty</th>
                                    <th>Biaya Lain</th>
                                    <th>Keterangan</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $detail = $conn->prepare("
                                SELECT pg.*, pr.nama_perusahaan, mb.no_polisi, rt.tujuan
                                FROM tb_pengiriman pg
                                JOIN tb_perusahaan pr ON pg.id_perusahaan = pr.id_perusahaan
                                JOIN tb_mobil mb ON pg.id_mobil = mb.id_mobil
                                JOIN tb_rute rt ON pg.id_rute = rt.id_rute
                                WHERE pg.no_invoice_utama = ?
                            ");
                                $detail->bind_param("s", $inv['no_invoice_utama']);
                                $detail->execute();
                                $res = $detail->get_result();
                                while ($row = $res->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['no_invoice_pengiriman'] ?></td>
                                        <td><?= $row['nama_perusahaan'] ?></td>
                                        <td><?= $row['no_polisi'] ?></td>
                                        <td><?= $row['tujuan'] ?></td>
                                        <td><?= $row['ct'] ?></td>
                                        <td><?= $row['qty'] ?></td>
                                        <td>Rp <?= number_format($row['biaya_lain'], 0, ',', '.') ?></td>
                                        <td><?= $row['keterangan'] ?></td>
                                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_pengiriman'] ?>">Edit</button>
                                            <button class="btn btn-sm btn-danger btn-hapus"
                                                data-id="<?= $row['id_pengiriman'] ?>"
                                                data-tanggal="<?= $tanggal ?>">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal Edit -->
                                    <div class="modal fade" id="modalEdit<?= $row['id_pengiriman'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post" action="detail_invoice.php?tanggal=<?= $tanggal ?>">
                                                    <input type="hidden" name="aksi" value="edit">
                                                    <input type="hidden" name="id_pengiriman" value="<?= $row['id_pengiriman'] ?>">
                                                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                                                    <input type="hidden" name="id_perusahaan" value="<?= $row['id_perusahaan'] ?>">
                                                    <input type="hidden" name="id_mobil" value="<?= $row['id_mobil'] ?>">
                                                    <input type="hidden" name="id_rute" value="<?= $row['id_rute'] ?>">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Pengiriman</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label>CT</label>
                                                            <input type="number" name="ct" class="form-control" value="<?= $row['ct'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Qty</label>
                                                            <input type="number" name="qty" class="form-control" value="<?= $row['qty'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Biaya Lain</label>
                                                            <input type="number" name="biaya_lain" class="form-control" value="<?= $row['biaya_lain'] ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label>Keterangan</label>
                                                            <textarea name="keterangan" class="form-control"><?= $row['keterangan'] ?></textarea>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning">Tidak ada pengiriman pada tanggal ini.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include_once 'layouts/footer.php'; ?>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const tanggal = this.dataset.tanggal;
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'detail_invoice.php?hapus=' + id + '&tanggal=' + tanggal;
                }
            });
        });
    });

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'edit'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil diupdate!',
                timer: 2000,
                showConfirmButton: false
            });
        <?php elseif ($_GET['status'] === 'hapus'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Data berhasil dihapus!',
                timer: 2000,
                showConfirmButton: false
            });
        <?php endif; ?>
    <?php endif; ?>
</script>
