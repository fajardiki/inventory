<?php

// $stok_tipis = db_get('barang', 'stok <= stok_ambang');
$sql = mysqli_query($GLOBALS['koneksi'] ,"
    SELECT 
        barang.kode_brg,
        barang.gambar,
        barang.nama_brg,
        COALESCE(SUM(a.jumlah), 0) AS stok,
        barang.stok_ambang, 
        barang.kode_rak 
    FROM
        stokbarang a 
        RIGHT JOIN barang 
            ON barang.kode_brg = a.kode_brg 
    WHERE 1 
    GROUP BY a.kode_brg 
    HAVING stok <= barang.stok_ambang 
    ORDER BY barang.nama_brg 
");
$stok_tipis = mysqli_fetch_all($sql, MYSQLI_ASSOC);
?>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            buttons: [
                'pdf'
            ]
        });
    });
    
    <?php foreach ($stok_tipis as $barang) { ?>
        Toastify({
            text: "Stok <?= $barang['nama_brg'] ?> tinggal <?= $barang['stok'] ?>",
            close: true,
            destination: "<?= BASE_URL . '/barang' ?>",
            gravity: "top",
            position: "right",
            backgroundColor: "red",
            color: "white",
            stopOnFocus: true,
        }).showToast();
    <?php } ?>
</script>
</body>

</html>