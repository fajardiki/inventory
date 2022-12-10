<?php

function db_get($table, $where = null, $order = null, $limit = null)
{
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

function db_get_one($table, $where = null, $order = null)
{
    $data = db_get($table, $where, $order, 1);
    return $data ? $data[0] : null;
}

function db_insert($table, $data)
{
    $sql = "INSERT INTO $table SET ";
    $sql .= implode(', ', array_map(function ($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_update($table, $data, $where)
{
    $sql = "UPDATE $table SET ";
    $sql .= implode(', ', array_map(function ($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    $sql .= " WHERE $where";
    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_delete($table, $where)
{
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

function db_chart()
{
    $tahun = date('Y');
    $sql = "
    SELECT 
        COALESCE(SUM(tbl.jumlah_pembelian), 0) AS jumlah_pembelian,
        COALESCE(SUM(tbl.jumlah_penjualan), 0) AS jumlah_penjualan,
        tbl.bulan,
        tbl.tanggal 
    FROM
        (SELECT 
            a.no_faktur AS nomor,
            COUNT(a.no_faktur) AS jumlah_pembelian,
            NULL AS jumlah_penjualan,
            SUM(b.total) AS total,
            MONTHNAME(a.tgl_transaksi) AS bulan,
            YEAR(a.tgl_transaksi) AS tahun,
            DATE_FORMAT(a.tgl_transaksi, '%y-%m') AS tanggal 
        FROM
            pembelian a 
            LEFT JOIN pembelian_barang b 
            ON b.no_faktur = a.no_faktur 
        WHERE 1 
        GROUP BY tanggal 
        UNION
        ALL 
        SELECT 
            a.no_transaksi AS nomor,
            NULL AS jumlah_pembelian,
            COUNT(a.no_transaksi) AS jumlah_penjualan,
            SUM(b.total) AS total,
            MONTHNAME(a.tgl_transaksi) AS bulan,
            YEAR(a.tgl_transaksi) AS tahun,
            DATE_FORMAT(a.tgl_transaksi, '%y-%m') AS tanggal 
        FROM
            penjualan a 
            LEFT JOIN penjualan_barang b 
            ON b.no_transaksi = a.tgl_transaksi 
        WHERE 1 
        GROUP BY tanggal) tbl 
    WHERE 1 
        AND tbl.tahun = '$tahun' 
    GROUP BY tbl.bulan 
    ORDER BY tbl.tanggal 
    ";

    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // return $data;

    $response = [];
    foreach ($data as $key => $value) {
        $response['jumlah_pembelian'][] = intval($value['jumlah_pembelian']);
        $response['jumlah_penjualan'][] = intval($value['jumlah_penjualan']);
        $response['bulan'][] = $value['bulan'];
    }

    return $response;
}
