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

if (!isset($_GET['kode_brg'])) {
    header('Location: index.php');
    exit;
}
$kode_brg = $_GET['kode_brg'];

try {
    $penjualan = db_get('penjualan_barang', "kode_brg='$kode_brg'");
    if ($penjualan) {
        throw new Exception('Barang tidak bisa dihapus karena terdapat penjualan dengan barang ini');
    }
    $pembelian = db_get('pembelian_barang', "kode_brg='$kode_brg'");
    if ($pembelian) {
        throw new Exception('Barang tidak bisa dihapus karena terdapat pembelian dengan barang ini');
    }
    $result = db_delete('barang', "kode_brg='$kode_brg'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        throw new Exception('Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', $e->getMessage());
}
header('Location: index.php');