<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}

// Jika bukan admin, arahkan ke halaman index
if (!session_is_admin()) {
    header("Location: index.php");
}

// Jika tidak ada parameter no_faktur, arahkan ke halaman index
if (!isset($_GET['no_faktur'])) {
    header("Location: index.php");
}

$no_faktur = $_GET['no_faktur'];
$pembelian = db_get_one('pembelian', "no_faktur = '$no_faktur'");
if (!$pembelian) {
    header("Location: index.php");
}

if (isset($_POST['tambah'])) {
    $kode_brg = $_POST['kode_brg'];
    $kode_sup = $_POST['kode_sup'];
    $jumlah = $_POST['jumlah'];
    $barang = db_get_one('barang', "kode_brg='$kode_brg'");
    $total = $jumlah * $barang['harga'];
    $data = [
        'no_faktur' => $no_faktur,
        'kode_brg' => $kode_brg,
        'kode_sup' => $kode_sup,
        'jumlah' => $jumlah,
        'total' => $total
    ];
    $result = db_insert('pembelian_barang', $data);
    // insert jumlah ke table pembelian
    $jumlah_beli_barang = db_get('pembelian_barang', "no_faktur='$no_faktur'");
    $sum_total = array_sum(array_column($jumlah_beli_barang, 'total'));
    $result3 = db_update('pembelian', [
        'total' => $sum_total
    ], "no_faktur='$no_faktur'");

    if ($result && $result3) {
        $insert_stok = db_insert_stok_barang([
            "kode_barang" => $kode_brg, 
            "no_faktur" => $no_faktur, 
            "no_transaksi" => null, 
            "id_detail" => $result, 
            "jumlah" => $jumlah
        ]);
        session_flash('message', 'Data berhasil ditambahkan');
    } else {
        session_flash('error', 'Data gagal ditambahkan');
    }
}

if (isset($_POST['edit'])) {
    $id_detail = $_POST['id_detail'];
    $old_detail = db_get_one('pembelian_barang', "id_detail=$id_detail");
    $kode_brg = $_POST['kode_brg'];
    $kode_sup = $_POST['kode_sup'];
    $jumlah = $_POST['jumlah'];
    $barang = db_get_one('barang', "kode_brg='$kode_brg'");
    $total = $jumlah * $barang['harga'];
    $data = [
        'kode_brg' => $kode_brg,
        'kode_sup' => $kode_sup,
        'jumlah' => $jumlah,
        'total' => $total
    ];
    $result = db_update('pembelian_barang', $data, "id_detail=$id_detail");
    // insert jumlah ke table pembelian
    $jumlah_beli_barang = db_get('pembelian_barang', "no_faktur='$no_faktur'");
    $sum_total = array_sum(array_column($jumlah_beli_barang, 'total'));
    $result3 = db_update('pembelian', [
        'total' => $sum_total
    ], "no_faktur='$no_faktur'");

    if ($result && $result3) {
        $insert_stok = db_insert_stok_barang([
            "kode_barang" => $kode_brg, 
            "no_faktur" => $no_faktur, 
            "no_transaksi" => null, 
            "id_detail" => $id_detail, 
            "jumlah" => $jumlah
        ]);
        session_flash('message', 'Data berhasil diubah');
    } else {
        session_flash('error', 'Data gagal diubah');
    }
}

if (isset($_POST['hapus'])) {
    $id_detail = $_POST['id_detail'];
    $old_detail = db_get_one('pembelian_barang', "id_detail=$id_detail");
    $barang = db_get_one('barang', "kode_brg='" . $old_detail['kode_brg'] . "'");
    $result = db_delete('pembelian_barang', "id_detail=$id_detail");
    
    // insert jumlah ke table pembelian
    $jumlah_beli_barang = db_get('pembelian_barang', "no_faktur='$no_faktur'");
    $sum_total = array_sum(array_column($jumlah_beli_barang, 'total'));
    $result3 = db_update('pembelian', [
        'total' => $sum_total
    ], "no_faktur='$no_faktur'");
    if ($result && $result3) {
        db_delete("stokbarang", "no_faktur = '$no_faktur' AND id_detail = $id_detail");
        session_flash('message', 'Data berhasil dihapus');
    } else {
        session_flash('error', 'Data gagal dihapus');
    }
}

$pembelian_barang = db_get('pembelian_barang', "no_faktur = '$no_faktur'");

$semua_barang = db_get('barang');
$semua_supplier = db_get('supplier');

$title = "Pembelian Barang";

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
            <h4>No Faktur: <?= $no_faktur ?></h4>
            <h4>Tanggal: <?= $pembelian['tgl_transaksi'] ?></h4>
            <?php if (session_is_admin()) { ?>
                <p><a href="#" onclick="tambah()" class="btn btn-primary">Tambah</a></p>
                <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTambahLabel">Tambah Data</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="kode_brg">Barang</label>
                                        <select name="kode_brg" class="form-control">
                                            <?php foreach ($semua_barang as $barang) { ?>
                                                <option value="<?= $barang['kode_brg'] ?>"><?= $barang['nama_brg'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="kode_sup">Supplier</label>
                                        <select name="kode_sup" class="form-control">
                                            <?php foreach ($semua_supplier as $supplier) { ?>
                                                <option value="<?= $supplier['kode_sup'] ?>"><?= $supplier['nama_sup'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="jumlah">Jumlah</label>
                                        <input type="number" class="form-control" name="jumlah" min="0" onkeypress="input_number(event)">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                    <input type="hidden" name="no_faktur" value="<?= $no_faktur ?>">
                                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kode Supplier</th>
                        <th>Nama Supplier</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pembelian_barang as $key => $value) {
                        $barang = db_get_one('barang', "kode_brg='" . $value['kode_brg'] . "'");
                        $supplier = db_get_one('supplier', "kode_sup='" . $value['kode_sup'] . "'"); ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['kode_brg'] ?></td>
                            <td><?= $barang['nama_brg'] ?></td>
                            <td><?= $value['kode_sup'] ?></td>
                            <td><?= $supplier['nama_sup'] ?></td>
                            <td>Rp<?= number_format($barang['harga'], 2, ',', '.') ?></td>
                            <td><?= $value['jumlah'] ?></td>
                            <td>Rp<?= number_format($value['total'], 2, ',', '.') ?></td>
                            <td>
                                <button onclick="edit('<?= $value['id_detail'] ?>')" class="btn btn-warning">Edit</button>
                                <div class="modal fade" id="modalEdit<?= $value['id_detail'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalEditLabel">Edit Data</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="kode_brg">Barang</label>
                                                        <select name="kode_brg" class="form-control">
                                                            <?php foreach ($semua_barang as $barang) { ?>
                                                                <option value="<?= $barang['kode_brg'] ?>" <?= $value['kode_brg'] == $barang['kode_brg'] ? 'selected' : '' ?>><?= $barang['nama_brg'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="kode_sup">Supplier</label>
                                                        <select name="kode_sup" class="form-control">
                                                            <?php foreach ($semua_supplier as $supplier) { ?>
                                                                <option value="<?= $supplier['kode_sup'] ?>" <?= $value['kode_sup'] == $supplier['kode_sup'] ? 'selected' : '' ?>><?= $supplier['nama_sup'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="jumlah">Jumlah</label>
                                                        <input type="number" class="form-control" name="jumlah" value="<?= $value['jumlah'] ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <input type="hidden" name="id_detail" value="<?= $value['id_detail'] ?>">
                                                    <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <button onclick="hapus('<?= $value['id_detail'] ?>')" class="btn btn-danger">Hapus</button>
                                <!-- modal -->
                                <div class="modal fade" id="modalHapus<?= $value['id_detail'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                                                <form method="POST" style="display:inline;">
                                                    <input type="hidden" name="id_detail" value="<?= $value['id_detail'] ?>">
                                                    <button type="submit" name="hapus" class="btn btn-primary">Ya</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        <?php } ?>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function tambah() {
        $(`#modalTambah`).modal('show');
    }

    function edit(id_detail) {
        $(`#modalEdit${id_detail}`).modal('show');
    }

    function hapus(id_detail) {
        $(`#modalHapus${id_detail}`).modal('show');
    }
</script>
<?php include '../layout/footer.php'; ?>