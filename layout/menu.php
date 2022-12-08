<a href="<?= BASE_URL . '/rak/index.php' ?>" class="btn btn-primary">Rak</a>
<a href="<?= BASE_URL . '/supplier/index.php' ?>" class="btn btn-primary">Supplier</a>
<a href="<?= BASE_URL . '/barang/index.php' ?>" class="btn btn-primary">Barang</a>
<a href="<?= BASE_URL . '/pembelian/index.php' ?>" class="btn btn-primary">Pembelian</a>
<a href="<?= BASE_URL . '/penjualan/index.php' ?>" class="btn btn-primary">Penjualan</a>
<?php if (session_is_admin()) { ?>
<a href="<?= BASE_URL . '/pengguna/index.php' ?>" class="btn btn-primary">Pengguna</a>
<?php } ?>