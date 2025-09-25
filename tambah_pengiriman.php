<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once 'functions/function_pengiriman.php';
handlePengirimanActions();
require_once 'layouts/header.php';

$perusahaan = getAllPerusahaan();
$mobil      = getAllMobil();
$rute       = getAllRute();
?>

<div class="container mt-4">
  <h4 class="mb-4">Tambah Pengiriman</h4>
  <form method="post">
    <input type="hidden" name="action" value="tambah">

    <div class="mb-3">
      <label>Tujuan Kirim ke PT</label>
     <select name="id_perusahaan" class="form-control" required>
        <option value="">- Pilih Tujuan PT -</option>
        <?php while ($tp = $perusahaan->fetch_assoc()): ?>
          <option value="<?= $tp['id_perusahaan'] ?>"><?= $tp['nama_perusahaan'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Tanggal Pengiriman</label>
      <input type="date" name="tanggal" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Mobil</label>
      <select name="id_mobil" class="form-control" required>
        <option value="">- Pilih Mobil -</option>
        <?php while ($m = $mobil->fetch_assoc()): ?>
          <option value="<?= $m['id_mobil'] ?>"><?= $m['no_polisi'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Rute</label>
      <select name="id_rute" id="id_rute" class="form-control" required>
        <option value="">- Pilih Rute -</option>
        <?php while ($r = $rute->fetch_assoc()): ?>
          <option value="<?= $r['id_rute'] ?>" data-tujuan="<?= $r['tujuan'] ?>" data-harga="<?= $r['harga_dasar'] ?>">
            <?= $r['id_rute'] ?> - <?= $r['tujuan'] ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Tujuan Pengiriman</label>
      <input type="text" id="tujuan_pengiriman" name="tujuan_pengiriman" class="form-control" readonly>
    </div>

    <div class="mb-3">
      <label>CT</label>
      <input type="number" name="ct" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Qty</label>
      <input type="number" id="qty" name="qty" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Jenis Biaya Lain</label>
      <select name="kategori_biaya_lain" class="form-control" required>
        <option value="">- Pilih Jenis Biaya -</option>
        <option value="Etoll">Biaya Etoll</option>
        <option value="Dokumen PED">Biaya Dokumen PED</option>
        <option value="Bongkaran">Biaya Bongkaran</option>
      </select>
    </div>

    <div class="mb-3">
      <label>Nominal Biaya Lain</label>
      <input type="number" id="biaya_lain" name="biaya_lain" class="form-control" value="0" required>
    </div>

    <div class="mb-3">
      <label>Keterangan</label>
      <textarea name="keterangan" class="form-control" rows="2"></textarea>
    </div>

    <div class="mb-3">
      <label>Total (Preview)</label>
      <input type="text" id="preview_total" class="form-control bg-light" readonly>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Pengiriman</button>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const ruteSelect     = document.getElementById('id_rute');
  const tujuanInput    = document.getElementById('tujuan_pengiriman');
  const biayaLainInput = document.getElementById('biaya_lain');
  const previewTotal   = document.getElementById('preview_total');

  let hargaDasar = 0;

  function hitungTotal() {
    const biayaLain = parseFloat(biayaLainInput.value) || 0;
    const total = hargaDasar + biayaLain;
    previewTotal.value = total.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
  }

  ruteSelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const tujuan = selected.getAttribute('data-tujuan');
    hargaDasar = parseFloat(selected.getAttribute('data-harga')) || 0;
    tujuanInput.value = tujuan;
    hitungTotal();
  });

  biayaLainInput.addEventListener('input', hitungTotal);
});
</script>

<?php if (isset($_GET['status'], $_GET['act'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
      icon: '<?= $_GET['status'] === 'sukses' ? 'success' : 'error' ?>',
      title: '<?= ucfirst($_GET['act']) ?> Data',
      text: '<?= $_GET['status'] === 'sukses' ? 'Berhasil!' : 'Gagal!' ?>',
      confirmButtonText: 'OK'
    });
  });
</script>
<?php endif; ?>

<?php include_once 'layouts/footer.php'; ?>
