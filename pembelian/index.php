<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}

$title = "Pembelian";

$message = session_flash('message');
$error = session_flash('error');

$post_data = $_POST;

if (isset($post_data['pembelian'])) {
    $data = [
        'no_faktur' => $post_data['no_faktur'],
        'tgl_transaksi' => $post_data['tgl_transaksi'],
        'username' => session_get_username()
    ];
    $result = db_insert('pembelian', $data);
    if ($result) {
        session_flash('message', 'Data berhasil ditambahkan');
    } else {
        throw new Exception('Data gagal ditambahkan');
    }
    header('Location: index.php');
} elseif (isset($post_data['filter'])) {
    $pembelian = db_list_pembelian($post_data['kode_barang'], $post_data['nama_barang'], $post_data['kode_supplier'], $post_data['nama_supplier']);
} else {
    $pembelian = db_list_pembelian();
}

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
                <p><a href="#" class="btn btn-primary" onclick="tambah()">Tambah</a></p>
            <?php } ?>
            <?php if (!session_is_admin()) { ?>
                <div class="pull-right" style="margin-bottom: 20px;">
                    <a href="#" class="btn btn-warning" onclick="print()">Report</a>
                    <button class="btn btn-default" onclick="filter()"><i class="fa fa-search"></i></button>
                </div>
            <?php } ?>

            <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambah" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambah">Tambah Pembelian</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="no_faktur">No Faktur</label>
                                    <input type="text" class="form-control" id="no_faktur" name="no_faktur" placeholder="No Faktur">
                                </div>
                                <div class="form-group">
                                    <label for="tgl_transaksi">Tanggal</label>
                                    <input type="date" min="<?= $today; ?>" class="form-control" id="tgl_transaksi" name="tgl_transaksi" placeholder="Tanggal">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="pembelian" class="btn btn-info" value="Simpan">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalFilter" tabindex="-1" role="dialog" aria-labelledby="modalFilter" aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalFilter">Filter</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" placeholder="Kode Barang">
                                </div>
                                <div class="form-group">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Nama Barang" value="<?= $post_data['nama_barang'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="kode_supplier">Kode Supplier</label>
                                    <input type="text" class="form-control" id="kode_supplier" name="kode_supplier" placeholder="Kode Supplier">
                                </div>
                                <div class="form-group">
                                    <label for="nama_supplier">Supplier</label>
                                    <input type="text" class="form-control" id="nama_supplier" name="nama_supplier" placeholder="Nama Supplier">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="filter" id="filter" class="btn btn-warning" value="Cari">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
            <?php foreach ($pembelian as $key => $value) : ?>
                <div class="modal fade" id="modalDetail<?= $value['no_faktur'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetail<?= $value['no_faktur'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalDetail<?= $value['no_faktur'] ?>">Detail Pembelian</h5>
                                <?php $pembelian_barang = db_get('pembelian_barang', "no_faktur='" . $value['no_faktur'] . "'"); ?>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <tr>
                                        <th>No Faktur</th>
                                        <td><?= $value['no_faktur'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Pembelian</th>
                                        <td><?= $value['tgl_transaksi'] ?></td>
                                    </tr>
                                    <h4>Barang</h4>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Supplier</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                    <?php foreach ($pembelian_barang as $key => $value) {
                                        $barang = db_get_one('barang', "kode_brg = '" . $value['kode_brg'] . "'");
                                        $supplier = db_get_one('supplier', "kode_sup = '" . $value['kode_sup'] . "'"); ?>
                                        <tr>
                                            <td><?= $value['kode_brg'] ?></td>
                                            <td><?= $barang['nama_brg'] ?></td>
                                            <td><?= $supplier['nama_sup'] ?></td>
                                            <td><?= $value['jumlah'] ?></td>
                                            <td>Rp<?= number_format($barang['harga'], 0, ',', '.') ?></td>
                                            <td>Rp<?= number_format($value['total'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php } ?>
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
                        <th>No Faktur</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pembelian as $key => $value) : ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['no_faktur'] ?></td>
                            <td><?= $value['tgl_transaksi'] ?></td>
                            <td>Rp<?= number_format($value['total'], 0, ',', '.') ?></td>
                            <td><a href="#" class="btn btn-info" onclick="tampil('<?= $value['no_faktur'] ?>')">Lihat</a>
                                <?php if (session_is_admin()) { ?>
                                    <a href="edit.php?no_faktur=<?= $value['no_faktur'] ?>" class="btn btn-warning">Edit</a>
                                    <button onclick="hapus('<?= $value['no_faktur'] ?>')" class="btn btn-danger">Hapus</button>
                                    <!-- modal -->
                                    <div class="modal fade" id="modalHapus<?= $value['no_faktur'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                                                    <a href="hapus.php?no_faktur=<?= $value['no_faktur'] ?>" class="btn btn-primary">Ya</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- untuk cetak -->
            <div id="print-area" hidden>
                <h1>Laporan Pembelian</h1>
                <table class="table-print" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 130px;">No Faktur</th>
                            <th style="width: 100px;">Tanggal</th>
                            <th>Barang</th>
                            <th style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembelian as $key => $value) : ?>
                            <tr>
                                <td style="text-align: center;"><?= $key + 1 ?>. </td>
                                <td><?= $value['no_faktur'] ?></td>
                                <td><?= date_format(date_create($value['tgl_transaksi']), 'd-m-Y') ?></td>
                                <td><?= $value['barang'] ?></td>
                                <td>Rp <?= number_format($value['total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    function tampil(no_faktur) {
        $(`#modalDetail${no_faktur}`).modal('show');
    }

    function filter() {
        $(`#modalFilter`).modal('show');
    }

    function tambah() {
        $(`#modalTambah`).modal('show');
    }

    function hapus(no_faktur) {
        $(`#modalHapus${no_faktur}`).modal('show');
    }
</script>
<?php include '../layout/footer.php'; ?>