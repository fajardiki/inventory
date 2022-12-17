<?php
require '../inc/koneksi.php';

$kode_brg = $_GET['kode_brg'];

$sql = "
    SELECT 
        barang.kode_brg,
        barang.gambar,
        barang.nama_brg,
        COALESCE(SUM(a.jumlah), 0) AS jumlah,
        barang.stok_ambang, 
        barang.kode_rak 
    FROM
        stokbarang a 
        RIGHT JOIN barang 
            ON barang.kode_brg = a.kode_brg 
    WHERE 1 
        AND barang.kode_brg = '$kode_brg'
    GROUP BY a.kode_brg 
    ORDER BY barang.nama_brg 
";
$res = mysqli_query($GLOBALS['koneksi'], $sql);
$data = mysqli_fetch_assoc($res);
echo json_encode($data);
