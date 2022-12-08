<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header("Location: $BASE_URL");
}

if (!isset($_GET['username'])) {
    header('Location: index.php');
}
$username = $_GET['username'];

try {
    $result = db_delete('pengguna', "username='$username'");
    if ($result) {
        session_flash('message', 'Data berhasil dihapus');
    } else {
        session_flash('error', 'Data gagal dihapus');
    }
} catch (Exception $e) {
    session_flash('error', "Gagal: " . $e->getMessage());
}
header('Location: index.php');