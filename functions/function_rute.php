<?php
require_once 'koneksi.php';

function getAllRute() {
    global $conn;
    $query = "SELECT * FROM tb_rute ORDER BY id_rute DESC";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi untuk membersihkan input harga format rupiah ke angka murni
function convertRupiahToInt($rupiah) {
    $angka = preg_replace('/[^0-9]/', '', $rupiah);
    return (int) $angka;
}

function tambahRute($asal, $tujuan, $harga_dasar) {
    global $conn;
    $harga_dasar = convertRupiahToInt($harga_dasar);
    $query = "INSERT INTO tb_rute (asal, tujuan, harga_dasar) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $asal, $tujuan, $harga_dasar);
    return mysqli_stmt_execute($stmt);
}

function updateRute($id_rute, $asal, $tujuan, $harga_dasar) {
    global $conn;
    $harga_dasar = convertRupiahToInt($harga_dasar);
    $query = "UPDATE tb_rute SET asal=?, tujuan=?, harga_dasar=? WHERE id_rute=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssii", $asal, $tujuan, $harga_dasar, $id_rute);
    return mysqli_stmt_execute($stmt);
}

function deleteRute($id_rute) {
    global $conn;
    $query = "DELETE FROM tb_rute WHERE id_rute=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_rute);
    return mysqli_stmt_execute($stmt);
}

function handleRuteActions() {
    if (isset($_POST['tambah'])) {
        if (tambahRute($_POST['asal'], $_POST['tujuan'], $_POST['harga_dasar'])) {
            header("Location: rute.php?status=tambah_sukses");
            exit;
        }
    }

    if (isset($_POST['update'])) {
        if (updateRute($_POST['id_rute'], $_POST['asal'], $_POST['tujuan'], $_POST['harga_dasar'])) {
            header("Location: rute.php?status=edit_sukses");
            exit;
        }
    }

    if (isset($_POST['delete'])) {
        if (deleteRute($_POST['id_rute'])) {
            header("Location: rute.php?status=hapus_sukses");
            exit;
        }
    }
}


