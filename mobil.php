<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
require 'functions/function_mobil.php';
handleMobilActions(); // Tangani aksi POST dan GET

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
        text: 'Data mobil berhasil disimpan.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php elseif ($_GET['status'] === 'edit_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Update Berhasil!',
        text: 'Data mobil berhasil diubah.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php elseif ($_GET['status'] === 'hapus_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Terhapus!',
        text: 'Data mobil berhasil dihapus.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>
});
</script>
<?php endif; ?>

<?php
$dataMobil = getAllMobil();
?>

<!-- Mulai isi konten utama -->
<h4 class="mb-4">Data Mobil</h4>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Mobil</div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Jenis Mobil</label>
                    <input type="text" name="jenis_mobil" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>No Polisi</label>
                    <input type="text" name="no_polisi" class="form-control" required>
                </div>
            </div>
            <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-secondary text-white">Daftar Mobil</div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Mobil</th>
                    <th>No Polisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = $dataMobil->fetch_assoc()): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['jenis_mobil']) ?></td>
                    <td><?= htmlspecialchars($row['no_polisi']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_mobil'] ?>">Edit</button>
                        <a href="mobil.php?hapus=<?= $row['id_mobil'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_mobil'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Mobil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id_mobil" value="<?= $row['id_mobil'] ?>">
                                    <div class="mb-3">
                                        <label>Jenis Mobil</label>
                                        <input type="text" name="jenis_mobil" class="form-control" value="<?= htmlspecialchars($row['jenis_mobil']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>No Polisi</label>
                                        <input type="text" name="no_polisi" class="form-control" value="<?= htmlspecialchars($row['no_polisi']) ?>" required>
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
