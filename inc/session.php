<?php
/**
 * File untuk menghandle session
 */

session_start();

/**
 * Fungsi untuk menyimpan sesi user
 */
function session_set_user($username, $jenis) {
    $_SESSION['username'] = $username;
    $_SESSION['jenis'] = $jenis;
}

/**
 * Fungsi untuk mengecek apakah user sudah login
 */
function session_is_login() {
    return isset($_SESSION['username']);
}

/**
 * Fungsi untuk menghapus sesi user
 */
function session_logout() {
    session_destroy();
}

/**
 * Fungsi untuk mengambil username dari sesi
 */
function session_get_username() {
    return $_SESSION['username'];
}

/**
 * Fungsi untuk mengecek apakah user adalah admin
 */
function session_is_admin() {
    return $_SESSION['jenis'] == 'admin';
}

function session_flash($key, $value = null) {
    if ($value != null) {
        $_SESSION[$key] = $value;
    } else {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }
}