<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header('Location: index.php');
}

if (isset($_POST['submit'])) {
    $kode_brg = $_POST['kode_brg'];
    $nama_brg = $_POST['nama_brg'];
    $ukuran = $_POST['ukuran'];
    $harga = $_POST['harga'];
    $stok_ambang = $_POST['stok_ambang'];
    $kode_rak = $_POST['kode_rak'];

    try {
        $data_insert = [
            'kode_brg' => $kode_brg,
            'nama_brg' => $nama_brg,
            'ukuran' => $ukuran,
            'harga' => $harga,
            'stok_ambang' => $stok_ambang,
            'kode_rak' => $kode_rak
        ];
        // upload_gambar
        if ($_FILES['gambar']['error'] == 0) {
            $nama = $_FILES['gambar']['name'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
            if (file_exists('../assets/img/' . $nama)) {
                throw new Exception("File dengan nama $nama sudah ada!");
            }

            move_uploaded_file($file_tmp, '../assets/img/' . $nama);
            if (file_exists('../assets/img/' . $nama)) {
                $gambar = $nama;
                if (!empty($gambar)) {
                    $data_insert['gambar'] = $gambar;
                }
            }
        }

        $barang = db_get_one('barang', "kode_brg='$kode_brg'");
        if ($barang) {
            throw new Exception('Kode barang sudah digunakan');
        }
        $result = db_insert('barang', $data_insert);
        if ($result) {
            session_flash('message', 'Data berhasil ditambahkan');
            header('Location: index.php');
            exit;
        } else {
            session_flash('error', 'Data gagal ditambahkan');
        }
    } catch (Exception $e) {
        unlink('../assets/img/' . $nama);
        session_flash('error', $e->getMessage());
    }
}

$title = "Tambah Barang";

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
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php elseif (isset($message)) : ?>
                <div class="alert alert-info" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kode_brg">Kode Barang</label>
                    <input type="text" name="kode_brg" id="kode_brg" class="form-control" placeholder="Kode Barang" value="<?= $kode_brg ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_brg">Nama Barang</label>
                    <input type="text" name="nama_brg" id="nama_brg" class="form-control" placeholder="Nama Barang" value="<?= $nama_brg ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="ukuran">Ukuran</label>
                    <input type="text" name="ukuran" id="ukuran" class="form-control" placeholder="Ukuran" value="<?= $ukuran ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <div class="input-group">
                        <span class="input-group-addon">Rp</span>
                        <input type="text" name="harga" id="harga" class="form-control" value="<?= $harga ?? '' ?>" placeholder="Harga">
                        <span class="input-group-addon">,00</span>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" min="0" onkeypress="input_number(event)" placeholder="Stok" value="<?= $stok ?? '' ?>" required>
                </div> -->
                <div class="form-group">
                    <label for="stok_ambang">Stok Ambang</label>
                    <input type="number" name="stok_ambang" id="stok_ambang" class="form-control" min="0" onkeypress="input_number(event)" placeholder="Stok Ambang" value="<?= $stok_ambang ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="kode_rak">Rak</label>
                    <select name="kode_rak" id="kode_rak" class="form-control" required>
                        <option value="">Pilih Rak</option>
                        <?php foreach ($rak as $r) : ?>
                            <option value="<?= $r['kode_rak'] ?>" <?= isset($kode_rak) && $r['kode_rak'] == $kode_rak ? 'selected' : '' ?>><?= $r['kode_rak'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    <div class="row">
                        <div class="col-md-4">
                            <?php if (isset($barang['gambar']) && !is_null($barang['gambar'])) : ?>
                                <img style="margin-bottom: 5px;" src="../assets/img/<?= $barang['gambar'] ?>" alt="gambar" width="350">
                            <?php endif; ?>
                            <input type="file" name="gambar" id="gambar" class="form-control" placeholder="Stok Ambang" value="<?= $gambar ?>" required>
                        </div>
                    </div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#harga').mask('000.000.000.000', {
            reverse: true
        });
        $('form').on('submit', function(e) {
            $('#harga').unmask();
        });
    });
</script>
<?php include '../layout/footer.php'; ?>