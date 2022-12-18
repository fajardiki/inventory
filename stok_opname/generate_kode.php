<?php
require '../inc/koneksi.php';

$table = $_GET['table'];
$prefix_kode = $_GET['prefix_kode'];
$tanggal = date('Y-m');

$sql = "
    SELECT 
        COUNT(a.tgl_transaksi) AS jumlah 
    FROM
        $table a 
    WHERE 1 
        AND DATE_FORMAT(a.tgl_transaksi, '%Y-%m') = '$tanggal' 
";
$res = mysqli_query($GLOBALS['koneksi'], $sql);
$data = mysqli_fetch_assoc($res);

$kode = $prefix_kode . date('y/m') . '/' . str_pad($data['jumlah'] + 1, 3, '0', STR_PAD_LEFT);
echo json_encode([
    "kode" => $kode
]);
