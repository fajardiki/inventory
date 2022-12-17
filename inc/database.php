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

function db_insert_detail($table, $data)
{
    $sql = "INSERT INTO $table SET ";
    $sql .= implode(', ', array_map(function ($key) use ($data) {
        return "$key = '$data[$key]'";
    }, array_keys($data)));
    mysqli_query($GLOBALS['koneksi'], $sql);
    return mysqli_insert_id($GLOBALS['koneksi']);
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

function db_insert_stok_barang($data)
{
    $kode_barang = $data['kode_barang'];
    $no_faktur = $data['no_faktur'];
    $no_transaksi = $data['no_transaksi'];
    $id_detail = $data['id_detail'];
    $jumlah = $data['jumlah'];

    if (!is_null($no_faktur)) {
        $delete_data = mysqli_query($GLOBALS['koneksi'], "DELETE FROM stokbarang WHERE no_faktur = '$no_faktur' AND id_detail = $id_detail");
    } elseif (!is_null($no_transaksi)) {
        $delete_data = mysqli_query($GLOBALS['koneksi'], "DELETE FROM stokbarang WHERE no_transaksi = '$no_transaksi' AND id_detail = $id_detail");
    }

    $sql = "INSERT INTO stokbarang (kode_brg, no_faktur, no_transaksi, id_detail, jumlah) VALUE ('$kode_barang', '$no_faktur', '$no_transaksi', $id_detail, $jumlah)";

    return mysqli_query($GLOBALS['koneksi'], $sql);
}

function db_list_penjualan($jenis_laporan = null, $kode_barang = null, $nama_barang = null)
{
    $where = null;
    $groupBy = null;

    if ($jenis_laporan == "harian") {
        $groupBy = "GROUP BY harian";
    } elseif ($jenis_laporan == "mingguan") {
        $groupBy = "GROUP BY mingguan";
    } elseif ($jenis_laporan == "bulanan") {
        $groupBy = "GROUP BY bulanan";
    } elseif ($jenis_laporan == "tahunan") {
        $groupBy = "GROUP BY tahunan";
    } else {
        $groupBy = "GROUP BY a.no_transaksi";
    }

    if ($kode_barang) {
        $where .= "AND b.kode_brg LIKE '%$kode_barang%' ";
    }

    if ($nama_barang) {
        $nama_barang = strtolower($nama_barang);
        $where .= "AND LOWER(barang.nama_brg) LIKE '%$nama_barang%' ";
    }

    $sql = "
        SELECT 
            a.*,
            COALESCE(SUM(b.total), 0) AS total, 
            GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang,
            a.tgl_transaksi AS harian, 
            WEEK(a.tgl_transaksi) AS mingguan, 
            DATE_FORMAT(a.tgl_transaksi, '%Y-%m') AS bulanan, 
            YEAR(a.tgl_transaksi) AS tahunan
        FROM
            penjualan a 
            LEFT JOIN penjualan_barang b 
                ON b.no_transaksi = a.no_transaksi 
            LEFT JOIN barang 
                ON barang.kode_brg = b.kode_brg
        WHERE 1
            $where 
        $groupBy 
    ";

    // $sql = "
    //     SELECT 
    //         a.*,
    //         COALESCE(SUM(b.total), 0) AS total, 
    //         GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang
    //     FROM
    //         penjualan a 
    //         LEFT JOIN penjualan_barang b 
    //             ON b.no_transaksi = a.no_transaksi 
    //         LEFT JOIN barang 
    //             ON barang.kode_brg = b.kode_brg
    //     GROUP BY a.no_transaksi  
    // ";

    $result = mysqli_query($GLOBALS['koneksi'], $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $data;
}

function db_list_pembelian($jenis_laporan = null, $kode_barang = null, $nama_barang = null, $kode_supplier = null, $nama_supplier = null)
{
    $where = null;
    $groupBy = null;

    if ($jenis_laporan == "harian") {
        $groupBy = "GROUP BY harian";
    } elseif ($jenis_laporan == "mingguan") {
        $groupBy = "GROUP BY mingguan";
    } elseif ($jenis_laporan == "bulanan") {
        $groupBy = "GROUP BY bulanan";
    } elseif ($jenis_laporan == "tahunan") {
        $groupBy = "GROUP BY tahunan";
    } else {
        $groupBy = "GROUP BY a.no_faktur";
    }

    if ($kode_barang) {
        $where .= "AND b.kode_brg LIKE '%$kode_barang%' ";
    }

    if ($nama_barang) {
        $nama_barang = strtolower($nama_barang);
        $where .= "AND LOWER(barang.nama_brg) LIKE '%$nama_barang%' ";
    }

    if ($kode_supplier) {
        $where .= "AND b.kode_sup LIKE '%$kode_supplier%' ";
    }

    if ($nama_supplier) {
        $nama_supplier = strtolower($nama_supplier);
        $where .= "AND supplier.nama_sup LIKE '%$nama_supplier%' ";
    }

    $sql = "
    SELECT 
        a.*,
        GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang,
        COALESCE(SUM(b.total), 0) AS total,
        a.tgl_transaksi AS harian, 
        WEEK(a.tgl_transaksi) AS mingguan, 
        DATE_FORMAT(a.tgl_transaksi, '%Y-%m') AS bulanan, 
        YEAR(a.tgl_transaksi) AS tahunan
    FROM
        pembelian a 
        LEFT JOIN pembelian_barang b 
            ON b.no_faktur = a.no_faktur 
        LEFT JOIN barang 
            ON barang.kode_brg = b.kode_brg 
        LEFT JOIN supplier 
            ON supplier.kode_sup = b.kode_sup 
    WHERE 1 
        $where
    $groupBy
    ";

    // $sql = "
    //     SELECT 
    //         a.*,
    //         COALESCE(SUM(b.total), 0) AS total, 
    //         GROUP_CONCAT(barang.nama_brg SEPARATOR ', ') AS barang
    //     FROM
    //         pembelian a
    //         LEFT JOIN pembelian_barang b
    //             ON b.no_faktur = a.no_faktur 
    //         LEFT JOIN barang 
    //             ON barang.kode_brg = b.kode_brg 
    //         LEFT JOIN supplier 
    //             ON supplier.kode_sup = b.kode_sup
    //     WHERE 1 
    //         $where 
    //     GROUP BY a.no_faktur 
    // ";

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
