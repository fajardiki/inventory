<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['no_transaksi'])) {
    header('Location: index.php');
    exit;
}
$no_transaksi = $_GET['no_transaksi'];

try {
    db_delete('penjualan_barang', "no_transaksi = '$no_transaksi'");
    $result = db_delete('penjualan', "no_transaksi='$no_transaksi'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        throw new Exception('Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', $e->getMessage());
}
header('Location: index.php');