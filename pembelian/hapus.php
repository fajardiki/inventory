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

if (!isset($_GET['no_faktur'])) {
    header('Location: index.php');
    exit;
}
$no_faktur = $_GET['no_faktur'];

try {
    db_delete('pembelian_barang', "no_faktur = '$no_faktur'");
    $result = db_delete('pembelian', "no_faktur='$no_faktur'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        throw new Exception('Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', $e->getMessage());
}
header('Location: index.php');