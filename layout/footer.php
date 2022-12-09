<?php

$stok_tipis = db_get('barang', 'stok <= stok_ambang');
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