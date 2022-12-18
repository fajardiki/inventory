<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}

$title = "Sok Opname";
$stokopname = db_get('stokopname');
$semua_barang = db_get('barang');

$message = session_flash('message');
$error = session_flash('error');

if (isset($_POST['simpan'])) {
    if (empty($_POST['kode'])) {
        session_flash('error', 'Data gagal ditambahkan, Kode Stok Opname tidak boleh kosong');
    } elseif (empty($_POST['kode_brg'])) {
        session_flash('error', 'Data gagal ditambahkan, Barang tidak boleh kosong');
    } elseif (empty($_POST['jumlah_stok_opname'])) {
        session_flash('error', 'Data gagal ditambahkan, Stok Opname tidak boleh kosong');
    } else {
        $data = [
            'kode' => $_POST['kode'],
            'kode_brg' => $_POST['kode_brg'],
            'jumlah_stok' => $_POST['jumlah_stok'],
            'jumlah_stok_opname' => $_POST['jumlah_stok_opname'],
            'jumlah_selisih' => $_POST['jumlah_selisih'],
        ];

        $result = db_insert_detail('stokopname', $data);
        if ($result) {
            $insert_stok = db_insert_stok_barang([
                "kode_barang" => $_POST['kode_brg'],
                "no_faktur" => null,
                "no_transaksi" => null,
                "nomorstokopname" => $result,
                "id_detail" => 0,
                "jumlah" => $_POST['jumlah_selisih']
            ]);
            // echo json_encode($insert_stok);
            // exit;
            session_flash('message', 'Data berhasil ditambahkan');
        } else {
            session_flash('error', 'Data gagal ditambahkan');
            // throw new Exception('Data gagal ditambahkan');
        }
    }
    header('Location: index.php');
}

if (isset($_POST['hapus'])) {
    $nomor = $_POST['nomor'];
    $result = db_delete('stokopname', "nomor=$nomor");
    if ($result) {
        db_delete("stokbarang", "nomorstokopname = '$nomor'");
        session_flash('message', 'Data berhasil dihapus');
        header('Location: index.php');
    } else {
        session_flash('error', 'Data gagal dihapus');
    }
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
            <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambah" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambah">Tambah Stok Opname</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="kode">Kode Stok Opname</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode Stok Opname" required>
                                </div>
                                <div class="form-group">
                                    <label for="kode_brg">Barang</label>
                                    <select name="kode_brg" class="form-control" onchange="onChangeBarang(this)" required>
                                        <option value="" selected></option>
                                        <?php foreach ($semua_barang as $barang) { ?>
                                            <option value="<?= $barang['kode_brg'] ?>"><?= $barang['nama_brg'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_stok">Stok Gudang</label>
                                    <input type="number" min="0" class="form-control" id="jumlah_stok" name="jumlah_stok" placeholder="Stok Gudang" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_stok_opname">Stok Opname</label>
                                    <input type="number" min="0" class="form-control" id="jumlah_stok_opname" name="jumlah_stok_opname" placeholder="Stok Opname" onkeyup="calculateStokSelisih(this)" onkeypress="input_number(event)" required>
                                </div>
                                <div class="form-group">
                                    <label for="jumlah_selisih">Stok Selisih</label>
                                    <input type="number" min="0" class="form-control" id="jumlah_selisih" name="jumlah_selisih" placeholder="Stok Selisih" readonly>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" name="simpan" class="btn btn-info" value="Simpan">
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
            <table class="table" id="table">
                <thead>
                    <tr>
                        <th style="width: 30px;">No</th>
                        <th>Kode</th>
                        <th>Kode Barang</th>
                        <th>Stok Opname</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stokopname as $key => $value) : ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['kode'] ?></td>
                            <td><?= $value['kode_brg'] ?></td>
                            <td><?= number_format($value['jumlah_stok_opname'], 0, ',', '.') ?></td>
                            <td>
                                <?php if (session_is_admin()) { ?>
                                    <button onclick="hapus('<?= $value['nomor'] ?>')" class="btn btn-danger">Hapus</button>
                                    <!-- modal -->
                                    <div class="modal fade" id="modalHapus<?= $value['nomor'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                                                        <input type="hidden" name="nomor" value="<?= $value['nomor'] ?>">
                                                        <button type="submit" name="hapus" class="btn btn-primary">Ya</button>
                                                    </form>
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
        $(`#modalTambah`).modal('show');
    }

    function hapus(nomor) {
        $(`#modalHapus${nomor}`).modal('show');
    }

    function onChangeBarang(items) {
        console.log(items.value);
        $.ajax({
            url: 'stok.php',
            dataType: 'json',
            data: {
                'kode_brg': items.value
            },
            success: function(response) {
                $('#jumlah_stok').val(response.jumlah);
                // console.log(response.jumlah);
            }
        })
    }

    function calculateStokSelisih(items) {
        console.log(items.value);
        let jml_gudang = $('#jumlah_stok').val();
        let jml_stok_opname = items.value;
        let jml_selisih = jml_stok_opname - jml_gudang;
        $('#jumlah_selisih').val(jml_selisih);
    }
</script>
<?php include '../layout/footer.php'; ?>