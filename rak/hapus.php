<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header('Location: index.php');
}

if (!isset($_GET['kode_rak'])) {
    header('Location: index.php');
}
$kode_rak = $_GET['kode_rak'];

try {
    $barang = db_get('barang', "kode_rak='$kode_rak'");
    if ($barang) {
        session_flash('error', 'Rak tidak bisa dihapus karena masih ada barang');
        header('Location: index.php');
        exit;
    }
    $rak = db_get_one('rak', "kode_rak='$kode_rak'");
    if (!$rak) {
        session_flash('error', 'Rak tidak ditemukan');
        header('Location: index.php');
        exit;
    }
    $result = db_delete('rak', "kode_rak='$kode_rak'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        session_flash('error', 'Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', "Gagal: " . $e->getMessage());
}
header('Location: index.php');