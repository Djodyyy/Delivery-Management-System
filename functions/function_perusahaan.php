<?php
include_once 'functions/koneksi.php';

// Tambah perusahaan (dengan kode perusahaan)
function tambahPerusahaan($kode, $nama, $alamat, $kontak) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tb_perusahaan (kode_perusahaan, nama_perusahaan, alamat, kontak) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $kode, $nama, $alamat, $kontak);
    return $stmt->execute();
}

// Ambil semua data perusahaan
function getAllPerusahaan() {
    global $conn;
    $sql = "SELECT * FROM tb_perusahaan ORDER BY id_perusahaan DESC";
    $result = $conn->query($sql);
    return $result;
}

// Ambil data perusahaan by ID
function getPerusahaanById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tb_perusahaan WHERE id_perusahaan = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update perusahaan (dengan kode perusahaan)
function updatePerusahaan($id, $kode, $nama, $alamat, $kontak) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tb_perusahaan SET kode_perusahaan=?, nama_perusahaan=?, alamat=?, kontak=? WHERE id_perusahaan=?");
    $stmt->bind_param("ssssi", $kode, $nama, $alamat, $kontak, $id);
    return $stmt->execute();
}

// Hapus perusahaan
function deletePerusahaan($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tb_perusahaan WHERE id_perusahaan=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handler logic
function handlePerusahaanActions() {
    if (isset($_POST['tambah'])) {
        tambahPerusahaan(
            $_POST['kode_perusahaan'],
            $_POST['nama_perusahaan'],
            $_POST['alamat'],
            $_POST['kontak']
        );
        header("Location: perusahaan.php?status=tambah_sukses");
        exit;
    }

    if (isset($_POST['edit'])) {
        updatePerusahaan(
            $_POST['id_perusahaan'],
            $_POST['kode_perusahaan'],
            $_POST['nama_perusahaan'],
            $_POST['alamat'],
            $_POST['kontak']
        );
        header("Location: perusahaan.php?status=edit_sukses");
        exit;
    }

    if (isset($_GET['hapus'])) {
        deletePerusahaan($_GET['hapus']);
        header("Location: perusahaan.php?status=hapus_sukses");
        exit;
    }
}
?>
