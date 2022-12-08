<?php

require_once 'init.php';

// Jika sudah login, hapus sesi
if (session_is_login()) {
    session_logout();
}
// arahkan ke halaman login
header("Location: ${BASE_URL}/login.php");
