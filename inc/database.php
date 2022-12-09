<?php

function db_get($table, $where = null, $order = null, $limit = null) {
    $sql = "SELECT * FROM $table";
    if ($where) {
        $sql .= " WHERE $where";
    }
    if ($order) {
        $sql .= " ORDER BY $order";
    }
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

function db_get_one($table, $where = null, $order = null) {
    $data = db_get($table, $where, $order, 1);
    return $data ? $data[0] : null;
}

function db_insert($table, $data) {
    $sql = "INSERT INTO $table SET ";
    $sql .= implode(', ', array_map(function($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_update($table, $data, $where) {
    $sql = "UPDATE $table SET ";
    $sql .= implode(', ', array_map(function($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    $sql .= " WHERE $where";
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_delete($table, $where) {
    $sql = "DELETE FROM $table WHERE $where";
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_list_penjualan()
{
    $sql = "
        SELECT 
            a.*,
            COALESCE(SUM(b.total), 0) AS total, 
            GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang
        FROM
            penjualan a 
            LEFT JOIN penjualan_barang b 
                ON b.no_transaksi = a.no_transaksi 
            LEFT JOIN barang 
                ON barang.kode_brg = b.kode_brg
        GROUP BY a.no_transaksi  
    ";

    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $data;
}

function db_list_pembelian()
{
    $sql = "
        SELECT 
            a.*,
            COALESCE(SUM(b.total), 0) AS total, 
            GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang
        FROM
            pembelian a
            LEFT JOIN pembelian_barang b
                ON b.no_faktur = a.no_faktur 
            LEFT JOIN barang 
                ON barang.kode_brg = b.kode_brg 
        GROUP BY a.no_faktur 
    ";

    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $data;
}