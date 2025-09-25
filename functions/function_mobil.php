<?php
include_once 'functions/koneksi.php';

// Tambah mobil
function tambahMobil($jenis, $nopol) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO tb_mobil (jenis_mobil, no_polisi) VALUES (?, ?)");
    $stmt->bind_param("ss", $jenis, $nopol);
    return $stmt->execute();
}

// Ambil semua mobil
function getAllMobil() {
    global $conn;
    $sql = "SELECT * FROM tb_mobil ORDER BY id_mobil DESC";
    $result = $conn->query($sql);
    return $result;
}

// Ambil mobil by ID
function getMobilById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM tb_mobil WHERE id_mobil = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update mobil
function updateMobil($id, $jenis, $nopol) {
    global $conn;
    $stmt = $conn->prepare("UPDATE tb_mobil SET jenis_mobil=?, no_polisi=? WHERE id_mobil=?");
    $stmt->bind_param("ssi", $jenis, $nopol, $id);
    return $stmt->execute();
}

// Hapus mobil
function deleteMobil($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM tb_mobil WHERE id_mobil=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Handler logic
function handleMobilActions() {
    if (isset($_POST['tambah'])) {
        tambahMobil($_POST['jenis_mobil'], $_POST['no_polisi']);
        header("Location: mobil.php?status=tambah_sukses");
        exit;
    }

    if (isset($_POST['edit'])) {
        updateMobil($_POST['id_mobil'], $_POST['jenis_mobil'], $_POST['no_polisi']);
        header("Location: mobil.php?status=edit_sukses");
        exit;
    }

    if (isset($_GET['hapus'])) {
        deleteMobil($_GET['hapus']);
        header("Location: mobil.php?status=hapus_sukses");
        exit;
    }
}
