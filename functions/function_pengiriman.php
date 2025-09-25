<?php
require_once 'koneksi.php';

// Ambil semua perusahaan
function getAllPerusahaan()
{
    global $conn;
    return $conn->query("SELECT * FROM tb_perusahaan ORDER BY nama_perusahaan ASC");
}

// Ambil semua mobil
function getAllMobil()
{
    global $conn;
    return $conn->query("SELECT * FROM tb_mobil ORDER BY no_polisi ASC");
}

// Ambil semua rute
function getAllRute()
{
    global $conn;
    return $conn->query("SELECT * FROM tb_rute ORDER BY id_rute ASC");
}

// Ambil rute by ID
function getRuteById($id_rute)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tb_rute WHERE id_rute = ?");
    $stmt->bind_param("i", $id_rute);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Generate invoice
function generateNoInvoice($id_perusahaan)
{
    global $conn;
    $today = date("Ymd");

    $kode_perusahaan = getKodePerusahaanById($id_perusahaan);
    if (!$kode_perusahaan) return false;

    $result = $conn->query("SELECT COUNT(*) AS t FROM tb_pengiriman WHERE DATE(tanggal) = CURDATE()");
    $row = $result->fetch_assoc();
    $urut = str_pad($row['t'] + 1, 4, "0", STR_PAD_LEFT);

    return "INV-$today-$kode_perusahaan-$urut";
}

function getKodePerusahaanById($id_perusahaan)
{
    global $conn;
    $stmt = $conn->prepare("SELECT kode_perusahaan FROM tb_perusahaan WHERE id_perusahaan = ?");
    $stmt->bind_param("i", $id_perusahaan);
    $stmt->execute();
    $stmt->bind_result($kode);
    if ($stmt->fetch()) {
        return $kode;
    }
    return false;
}

// Tambah pengiriman
function tambahPengiriman($data)
{
    global $conn;

    $tanggal       = $data['tanggal'];
    $id_perusahaan = intval($data['id_perusahaan']);
    $id_mobil      = intval($data['id_mobil']);
    $id_rute       = intval($data['id_rute']);
    $ct            = intval($data['ct']);
    $qty           = intval($data['qty']);
    $biaya_lain    = floatval($data['biaya_lain']);
    $keterangan    = trim($data['keterangan']);

    $invoice_pengiriman = generateNoInvoice($id_perusahaan); 
    $invoice_utama = "HK-" . date("Ymd"); // invoice utama (fix untuk hari ini)

    // Ambil data rute untuk harga dasar
    $rute = getRuteById($id_rute);
    if (!$rute) return false;
    $harga_dasar = floatval($rute['harga_dasar']);

    // Ambil nama perusahaan sebagai tujuan
    $tujuan_pengiriman = getNamaPerusahaanById($id_perusahaan);
    if (!$tujuan_pengiriman) return false;

    // Total: harga_dasar + biaya_lain
    $total = $harga_dasar + $biaya_lain;

    // Query insert dengan tambahan kolom no_invoice_utama
    $stmt = $conn->prepare("INSERT INTO tb_pengiriman 
        (tanggal, id_perusahaan, id_mobil, id_rute, no_invoice_pengiriman, no_invoice_utama, tujuan_pengiriman, ct, qty, biaya_lain, keterangan, total)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "siiisssiiisd",
        $tanggal,
        $id_perusahaan,
        $id_mobil,
        $id_rute,
        $invoice_pengiriman,
        $invoice_utama,
        $tujuan_pengiriman,
        $ct,
        $qty,
        $biaya_lain,
        $keterangan,
        $total
    );

    return $stmt->execute();
}

function getNamaPerusahaanById($id_perusahaan)
{
    global $conn;
    $stmt = $conn->prepare("SELECT nama_perusahaan FROM tb_perusahaan WHERE id_perusahaan = ?");
    $stmt->bind_param("i", $id_perusahaan);
    $stmt->execute();
    $stmt->bind_result($nama);
    if ($stmt->fetch()) {
        return $nama;
    }
    return false;
}

// Ambil data pengiriman by ID
function getPengirimanById($id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tb_pengiriman WHERE id_pengiriman = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update pengiriman
function updatePengiriman($data)
{
    global $conn;

    $id_pengiriman = intval($data['id_pengiriman']);
    $tanggal       = $data['tanggal'];
    $id_perusahaan = intval($data['id_perusahaan']);
    $id_mobil      = intval($data['id_mobil']);
    $id_rute       = intval($data['id_rute']);
    $ct            = intval($data['ct']);
    $qty           = intval($data['qty']);
    $biaya_lain    = floatval($data['biaya_lain']);
    $keterangan    = trim($data['keterangan']);

    // Ambil data rute
    $rute = getRuteById($id_rute);
    if (!$rute) return false;
    $harga_dasar = floatval($rute['harga_dasar']);

    // Ambil nama perusahaan sebagai tujuan
    $tujuan_pengiriman = getNamaPerusahaanById($id_perusahaan);
    if (!$tujuan_pengiriman) return false;

    // Hitung total
    $total = $harga_dasar + $biaya_lain;

    $stmt = $conn->prepare("UPDATE tb_pengiriman SET 
        tanggal = ?, 
        id_perusahaan = ?, 
        id_mobil = ?, 
        id_rute = ?, 
        tujuan_pengiriman = ?, 
        ct = ?, 
        qty = ?, 
        biaya_lain = ?, 
        keterangan = ?, 
        total = ?
        WHERE id_pengiriman = ?");

    $stmt->bind_param(
        "siiisiiisdi",
        $tanggal,             
        $id_perusahaan,       
        $id_mobil,           
        $id_rute,             
        $tujuan_pengiriman,   
        $ct,                  
        $qty,                 
        $biaya_lain,          
        $keterangan,          
        $total,               
        $id_pengiriman        
    );

    return $stmt->execute();
}

// Hapus pengiriman
function deletePengiriman($id)
{
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tb_pengiriman WHERE id_pengiriman = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handler semua aksi pengiriman
function handlePengirimanActions()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $action = $_POST['action'];
        if ($action === 'tambah') {
            $ok = tambahPengiriman($_POST);
            header("Location: pengiriman.php?status=" . ($ok ? 'sukses' : 'gagal') . "&act=tambah");
            exit;
        } elseif ($action === 'update') {
            $ok = updatePengiriman($_POST);
            header("Location: pengiriman.php?status=" . ($ok ? 'sukses' : 'gagal') . "&act=update");
            exit;
        }
    }

    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $ok = deletePengiriman($id);
        header("Location: pengiriman.php?status=" . ($ok ? 'sukses' : 'gagal') . "&act=delete");
        exit;
    }
}
