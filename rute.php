<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}
require 'functions/function_rute.php';
// Jalankan handler
handleRuteActions();

include_once 'layouts/header.php';
?>

<?php if (isset($_GET['status'])): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      <?php if ($_GET['status'] === 'tambah_sukses'): ?>
        Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: 'Data rute berhasil disimpan.',
          timer: 2000,
          showConfirmButton: false
        });
      <?php elseif ($_GET['status'] === 'edit_sukses'): ?>
        Swal.fire({
          icon: 'success',
          title: 'Update Berhasil!',
          text: 'Data rute berhasil diubah.',
          timer: 2000,
          showConfirmButton: false
        });
      <?php elseif ($_GET['status'] === 'hapus_sukses'): ?>
        Swal.fire({
          icon: 'success',
          title: 'Terhapus!',
          text: 'Data rute berhasil dihapus.',
          timer: 2000,
          showConfirmButton: false
        });
      <?php endif; ?>
    });
  </script>
<?php endif; ?>

<?php
$dataRute = getAllRute();
?>

<!-- Mulai isi konten utama -->
<h4 class="mb-4">Data Rute</h4>

<div class="card mb-4">
  <div class="card-header bg-primary text-white">Tambah Rute</div>
  <div class="card-body">
    <form method="POST">
      <div class="row">
        <div class="col-md-4 mb-3">
          <label>Asal</label>
          <input type="text" name="asal" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
          <label>Tujuan</label>
          <input type="text" name="tujuan" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
          <label>Harga Dasar</label>
          <input type="number" name="harga_dasar" class="form-control" required>
        </div>
      </div>
      <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header bg-secondary text-white">Daftar Rute</div>
  <div class="card-body">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>Asal</th>
          <th>Tujuan</th>
          <th>Harga Dasar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1;
        foreach ($dataRute as $row): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['asal']) ?></td>
            <td><?= htmlspecialchars($row['tujuan']) ?></td>
            <td>Rp<?= number_format($row['harga_dasar'], 0, ',', '.') ?></td>
            <td>
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_rute'] ?>">Edit</button>
              <a href="rute.php?hapus=<?= $row['id_rute'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="editModal<?= $row['id_rute'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="POST">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Rute</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id_rute" value="<?= $row['id_rute'] ?>">
                    <div class="mb-3">
                      <label>Asal</label>
                      <input type="text" name="asal" class="form-control" value="<?= htmlspecialchars($row['asal']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Tujuan</label>
                      <input type="text" name="tujuan" class="form-control" value="<?= htmlspecialchars($row['tujuan']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label for="harga_dasar" class="form-label">Harga Dasar</label>
                      <input type="text" class="form-control" id="harga_dasar" name="harga_dasar" required>
                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                  </div>
                </form>
                <script>
                  const hargaDasarInput = document.getElementById('harga_dasar');

                  hargaDasarInput.addEventListener('input', function(e) {
                    let value = this.value.replace(/[^,\d]/g, '');
                    let parts = value.split(',');
                    let sisa = parts[0].length % 3;
                    let rupiah = parts[0].substr(0, sisa);
                    let ribuan = parts[0].substr(sisa).match(/\d{3}/g);

                    if (ribuan) {
                      let separator = sisa ? '.' : '';
                      rupiah += separator + ribuan.join('.');
                    }

                    this.value = 'Rp ' + rupiah;
                  });
                </script>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once 'layouts/footer.php'; ?>