<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require 'functions/function_perusahaan.php';
handlePerusahaanActions();
include_once 'layouts/header.php';
?>

<?php if (isset($_GET['status'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    <?php if ($_GET['status'] === 'tambah_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data perusahaan berhasil disimpan.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php elseif ($_GET['status'] === 'edit_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Update Berhasil!',
        text: 'Data perusahaan berhasil diubah.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php elseif ($_GET['status'] === 'hapus_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Terhapus!',
        text: 'Data perusahaan berhasil dihapus.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>
});
</script>
<?php endif; ?>

<?php
$dataPerusahaan = getAllPerusahaan();
?>

<h4 class="mb-4">Data Perusahaan</h4>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Perusahaan</div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Kode Perusahaan</label>
                    <input type="text" name="kode_perusahaan" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Nama Perusahaan</label>
                    <input type="text" name="nama_perusahaan" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Alamat</label>
                    <input type="text" name="alamat" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Kontak</label>
                    <input type="text" name="kontak" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-secondary text-white">Daftar Perusahaan</div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Perusahaan</th>
                    <th>Alamat</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $dataPerusahaan->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['kode_perusahaan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td><?= htmlspecialchars($row['kontak']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_perusahaan'] ?>">Edit</button>
                        <a href="perusahaan.php?hapus=<?= $row['id_perusahaan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_perusahaan'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Perusahaan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_perusahaan" value="<?= $row['id_perusahaan'] ?>">
                                    <div class="mb-3">
                                        <label>Kode Perusahaan</label>
                                        <input type="text" name="kode_perusahaan" class="form-control" value="<?= htmlspecialchars($row['kode_perusahaan']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Perusahaan</label>
                                        <input type="text" name="nama_perusahaan" class="form-control" value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Alamat</label>
                                        <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($row['alamat']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Kontak</label>
                                        <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($row['kontak']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
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

<?php include_once 'layouts/footer.php'; ?>
