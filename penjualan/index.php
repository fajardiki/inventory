<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}

$title = "Penjualan";

// $penjualan = db_list_penjualan();
// $penjualan = db_get('penjualan');
$message = session_flash('message');
$error = session_flash('error');

if (isset($_POST['penjualan'])) {
    if (empty($_POST['no_transaksi']) && empty($_POST['tgl_transaksi'])) {
        session_flash('error', 'Data gagal ditambahkan, No Transaksi dan Tanggal kosong');
    } else {
        $data = [
            'no_transaksi' => $_POST['no_transaksi'],
            'tgl_transaksi' => $_POST['tgl_transaksi'],
            'username' => session_get_username()
        ];

        $result = db_insert('penjualan', $data);
        if ($result) {
            session_flash('message', 'Data berhasil ditambahkan');
        } else {
            throw new Exception('Data gagal ditambahkan');
        }
    }
    header('Location: index.php');
} elseif (isset($_POST['filter'])) {
    $penjualan = db_list_penjualan($_POST['jenis_laporan'], $_POST['kode_barang'], $_POST['nama_barang']);
} else {
    $penjualan = db_list_penjualan();
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
                                    <label for="kode_barang">Jenis Laporan</label>
                                    <select class="form-control" name="jenis_laporan" id="jenis_laporan">
                                        <option selected></option>
                                        <option value="harian">Harian</option>
                                        <option value="mingguan">Mingguan</option>
                                        <option value="bulanan">Bulanan</option>
                                        <option value="tahunan">Tahunan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="kode_barang">Kode Barang</label>
                                    <input type="text" class="form-control" id="kode_barang" name="kode_barang" placeholder="Kode Barang" value="<?= isset($_POST['kode_barang']) ? $_POST['kode_barang'] : null ?>">
                                </div>
                                <div class="form-group">
                                    <label for="nama_barang">Nama Barang</label>
                                    <input type="text" class="form-control" id="nama_barang" name="nama_barang" placeholder="Nama Barang" value="<?= isset($_POST['nama_barang']) ? $_POST['nama_barang'] : null ?>">
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
            <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambah" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambah">Tambah Penjualan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="no_transaksi">No Transaksi</label>
                                    <input type="text" class="form-control" id="no_transaksi" name="no_transaksi" placeholder="No Transaksi">
                                </div>
                                <div class="form-group">
                                    <label for="tgl_transaksi">Tanggal</label>
                                    <input type="date" class="form-control" min="<?= $today; ?>" id="tgl_transaksi" name="tgl_transaksi" placeholder="Tanggal">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="penjualan" class="btn btn-info" value="Simpan">
                                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
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
            <?php foreach ($penjualan as $key => $value) : ?>
                <div class="modal fade" id="modalDetail<?= $value['no_transaksi'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalDetail<?= $value['no_transaksi'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalDetail<?= $value['no_transaksi'] ?>">Detail Penjualan</h5>
                                <?php $penjualan_barang = db_get('penjualan_barang', "no_transaksi='" . $value['no_transaksi'] . "'"); ?>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <table class="table">
                                    <tr>
                                        <th>No Transaksi</th>
                                        <td><?= $value['no_transaksi'] ?></td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Penjualan</th>
                                        <td><?= $value['tgl_transaksi'] ?></td>
                                    </tr>
                                    <h4>Barang</h4>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                    <?php foreach ($penjualan_barang as $key => $value) {
                                        $barang = db_get_one('barang', "kode_brg = '" . $value['kode_brg'] . "'");; ?>
                                        <tr>
                                            <td><?= $value['kode_brg'] ?></td>
                                            <td><?= $barang['nama_brg'] ?></td>
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
                        <th style="width: 30px;">No</th>
                        <?php if (empty($_POST['jenis_laporan'])) : ?>
                            <th>No Transaksi</th>
                        <?php else : ?>
                            <th><?= ucfirst($_POST['jenis_laporan']) ?></th>
                        <?php endif; ?>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($penjualan as $key => $value) : ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <?php if (empty($_POST['jenis_laporan'])) : ?>
                                <td><?= $value['no_transaksi'] ?></td>
                            <?php else : ?>
                                <td><?= $value[$_POST['jenis_laporan']] ?></td>
                            <?php endif; ?>
                            <td><?= $value['tgl_transaksi'] ?></td>
                            <td><?= number_format($value['total'], 0, ',', '.') ?></td>
                            <td><a href="#" class="btn btn-info" onclick="tampil('<?= $value['no_transaksi'] ?>')">Lihat</a>
                                <?php if (session_is_admin()) { ?>
                                    <a href="edit.php?no_transaksi=<?= $value['no_transaksi'] ?>" class="btn btn-warning">Edit</a>
                                    <button onclick="hapus('<?= $value['no_transaksi'] ?>')" class="btn btn-danger">Hapus</button>
                                    <!-- modal -->
                                    <div class="modal fade" id="modalHapus<?= $value['no_transaksi'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                                                    <a href="hapus.php?no_transaksi=<?= $value['no_transaksi'] ?>" class="btn btn-primary">Ya</a>
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
                <h1>Laporan Penjualan</h1>
                <table class="table-print" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <?php if (empty($_POST['jenis_laporan'])) : ?>
                                <th style="width: 130px;">No Transaksi</th>
                            <?php else : ?>
                                <th style="width: 130px;"><?= ucfirst($_POST['jenis_laporan']) ?></th>
                            <?php endif; ?>
                            <th style="width: 100px;">Tanggal</th>
                            <th>Barang</th>
                            <th style="width: 120px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penjualan as $key => $value) : ?>
                            <tr>
                                <td style="text-align: center;"><?= $key + 1 ?>. </td>
                                <?php if (empty($_POST['jenis_laporan'])) : ?>
                                    <td><?= $value['no_transaksi'] ?></td>
                                <?php else : ?>
                                    <td><?= $value[$_POST['jenis_laporan']] ?></td>
                                <?php endif; ?>
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
    function tampil(no_transaksi) {
        $(`#modalDetail${no_transaksi}`).modal('show');
    }

    function filter() {
        $(`#modalFilter`).modal('show');
    }

    function tambah() {
        $.ajax({
            url: '../stok_opname/generate_kode.php',
            dataType: 'json',
            data: {
                'table': "penjualan",
                'prefix_kode': "FKT/"
            },
            success: function(response) {
                console.log(response);
                $('#no_transaksi').val(response.kode);
            }
        })
        $(`#modalTambah`).modal('show');
    }

    function hapus(no_transaksi) {
        $(`#modalHapus${no_transaksi}`).modal('show');
    }
</script>
<?php include '../layout/footer.php'; ?>