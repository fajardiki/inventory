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

if (!isset($_GET['kode_sup'])) {
    header('Location: index.php');
    exit;
}
$kode_sup = $_GET['kode_sup'];

try {
    $pembelian = db_get('pembelian_barang', "kode_sup='$kode_sup'");
    if ($pembelian) {
        session_flash('error', 'Supplier tidak bisa dihapus karena terdapat pembelian barang');
        header('Location: index.php');
        exit;
    }
    
    $supplier = db_get_one('supplier', "kode_sup='$kode_sup'");
    if (!$supplier) {
        session_flash('error', 'Supplier tidak ditemukan');
        header('Location: index.php');
        exit;
    }
    $result = db_delete('supplier', "kode_sup='$kode_sup'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        session_flash('error', 'Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', "Gagal: " . $e->getMessage());
}
header('Location: index.php');