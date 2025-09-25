<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

require_once 'functions/koneksi.php';

// Tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['aksi'] === 'tambah') {
  $username = trim($_POST['username']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $role     = $_POST['role'];

  $stmt = $conn->prepare("INSERT INTO tb_admin (username, password, role) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $password, $role);
  $stmt->execute();
  header("Location: users.php?status=tambah");
  exit;
}

// Edit user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['aksi'] === 'edit') {
  $id       = intval($_POST['id_admin']);
  $username = trim($_POST['username']);
  $role     = $_POST['role'];

  if (!empty($_POST['password'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE tb_admin SET username = ?, password = ?, role = ? WHERE id_admin = ?");
    $stmt->bind_param("sssi", $username, $password, $role, $id);
  } else {
    $stmt = $conn->prepare("UPDATE tb_admin SET username = ?, role = ? WHERE id_admin = ?");
    $stmt->bind_param("ssi", $username, $role, $id);
  }
  $stmt->execute();
  header("Location: users.php?status=edit");
  exit;
}

// Hapus user
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  $conn->query("DELETE FROM tb_admin WHERE id_admin = $id");
  header("Location: users.php?status=hapus");
  exit;
}

// Ambil data user
$data = $conn->query("SELECT * FROM tb_admin ORDER BY id_admin DESC");

// Setelah semua logic selesai, baru tampilkan layout
require_once 'layouts/header.php';
?>

<div class="container mt-4">
  <h4 class="mb-4">👤 Data User / Admin</h4>

  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah User</button>

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Username</th>
            <th>Role</th>
            <th>Password</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($row = $data->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= $row['username'] ?></td>
              <td><?= ucfirst($row['role']) ?></td>
              <td>••••••••</td>
              <td>
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_admin'] ?>">Edit</button>
                <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_admin'] ?>">Hapus</button>
              </td>
            </tr>

            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit<?= $row['id_admin'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="post">
                    <input type="hidden" name="aksi" value="edit">
                    <input type="hidden" name="id_admin" value="<?= $row['id_admin'] ?>">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit User</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $row['username'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label>Password (kosongkan jika tidak diganti)</label>
                        <input type="password" name="password" class="form-control">
                      </div>
                      <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                          <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                          <option value="superadmin" <?= $row['role'] == 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
                        </select>
                      </div>
                    </div>
                    <div class="modal-footer">
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
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <input type="hidden" name="aksi" value="tambah">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
              <option value="admin">Admin</option>
              <option value="superadmin">Superadmin</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include 'layouts/footer.php'; ?>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-hapus').forEach(btn => {
  btn.addEventListener('click', function () {
    const id = this.dataset.id;
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: "Data akan dihapus permanen!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'users.php?hapus=' + id;
      }
    });
  });
});

// SweetAlert feedback
<?php if (isset($_GET['status'])): ?>
  Swal.fire({
    icon: 'success',
    title: 'Sukses',
    text: '<?= ucfirst($_GET['status']) ?> data berhasil!',
    timer: 2000,
    showConfirmButton: false
  });
<?php endif; ?>
</script>
