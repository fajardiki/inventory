<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}

$title = "Barang";

$barang = db_get('barang');
$rak = db_get('rak');
$message = session_flash('message');
$error = session_flash('error');
?>

<!-- mulai halaman -->
<?php include '../layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php include '../layout/menu.php'; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h1><?= $title ?></h1>
            <?php if (session_is_admin()) { ?>
                <p><a href="tambah.php" class="btn btn-primary">Tambah</a></p>
            <?php } ?>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php elseif (isset($message)) : ?>
                <div class="alert alert-info" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <!-- loop modalDetail rak -->
            <?php foreach ($rak as $key => $value) : ?>
                <div class="modal fade" id="modalDetail<?= $value['kode_rak'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetail<?= $value['kode_rak'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalDetail<?= $value['kode_rak'] ?>">Detail Rak</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <tr>
                                        <th>Kode Rak</th>
                                        <td><?= $value['kode_rak'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Kapasitas</th>
                                        <td><?= $value['kapasitas'] ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Gambar</th>
                        <th>Nama Barang</th>
                        <th>Ukuran</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Stok Ambang</th>
                        <th>Rak</th>
                        <?php if (session_is_admin()) { ?>
                            <th>Aksi</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $key => $value) : ?>
                        <tr <?= $value['stok'] <= $value['stok_ambang'] ? 'class="danger"' : '' ?>>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['kode_brg'] ?></td>
                            <td>
                                <?php if (!is_null($value['gambar'])) : ?>
                                    <img style="margin-bottom: 5px;" src="../assets/img/<?= $value['gambar'] ?>" alt="gambar" width="150">
                                <?php endif; ?>
                            </td>
                            <td><?= $value['nama_brg'] ?></td>
                            <td><?= $value['ukuran'] ?></td>
                            <td>Rp<?= number_format($value['harga'], 2, ',', '.') ?></td>
                            <td><?= $value['stok'] ?></td>
                            <td><?= $value['stok_ambang'] ?></td>
                            <td><a href="#" onclick="tampil('<?= $value['kode_rak'] ?>')"><?= $value['kode_rak'] ?></a></td>
                            <?php if (session_is_admin()) { ?>
                                <td>
                                    <a href="edit.php?kode_brg=<?= $value['kode_brg'] ?>" class="btn btn-warning">Edit</a>
                                    <button onclick="hapus('<?= $value['kode_brg'] ?>')" class="btn btn-danger">Hapus</button>
                                    <!-- modal -->
                                    <div class="modal fade" id="modalHapus<?= $value['kode_brg'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalHapusLabel">Hapus Data</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah anda yakin ingin menghapus data ini?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                                                    <a href="hapus.php?kode_brg=<?= $value['kode_brg'] ?>" class="btn btn-primary">Ya</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function tampil(kode_rak) {
        $(`#modalDetail${kode_rak}`).modal('show');
    }

    function hapus(kode_brg) {
        $(`#modalHapus${kode_brg}`).modal('show');
    }
</script>
<?php include '../layout/footer.php'; ?>