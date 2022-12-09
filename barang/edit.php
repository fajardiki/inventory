<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header('Location: index.php');
}

if (!isset($_GET['kode_brg'])) {
    header('Location: index.php');
}
$kode_brg = $_GET['kode_brg'];
$barang = db_get_one('barang', "kode_brg='$kode_brg'");
if (!$barang) {
    header('Location: index.php');
}

if (isset($_POST['submit'])) {
    $new_kode_brg = $_POST['kode_brg'];
    $new_nama_brg = $_POST['nama_brg'];
    $new_ukuran = $_POST['ukuran'];
    $new_harga = $_POST['harga'];
    $new_stok = $_POST['stok'];
    $new_stok_ambang = $_POST['stok_ambang'];
    $new_kode_rak = $_POST['kode_rak'];
    $new_gambar = null;

    try {
        // upload_gambar
        if ($_FILES['gambar']) {
            $nama = $_FILES['gambar']['name'];
            $file_tmp = $_FILES['gambar']['tmp_name'];
            if (file_exists('../assets/img/' . $nama)) {
                throw new Exception("File dengan nama $nama sudah ada!");
            }

            move_uploaded_file($file_tmp, '../assets/img/' . $nama);
            if (file_exists('../assets/img/' . $nama)) {
                $new_gambar = $nama;
            }
        }

        if ($new_kode_brg != $kode_brg) {
            $barang = db_get_one('barang', "kode_brg='$new_kode_brg'");
            if ($barang) {
                throw new Exception("Kode barang '$new_kode_brg' sudah digunakan");
            }
        }
        $result = db_update('barang', [
            'kode_brg' => $new_kode_brg,
            'nama_brg' => $new_nama_brg,
            'ukuran' => $new_ukuran,
            'harga' => $new_harga,
            'stok' => $new_stok,
            'stok_ambang' => $new_stok_ambang,
            'kode_rak' => $new_kode_rak,
            'gambar' => $new_gambar
        ], "kode_brg = '$kode_brg'");
        if ($result) {
            session_flash('message', 'Data berhasil diubah');
            header('Location: index.php');
            exit;
        } else {
            unlink('../assets/img/' . $nama);
            throw new Exception('Data gagal diubah');
        }
    } catch (Exception $e) {
        session_flash('error', $e->getMessage());
        header("Location: edit.php?kode_brg=$kode_brg");
        exit;
    }
}

$title = "Edit Barang";

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
            <form action="edit.php?kode_brg=<?= $barang['kode_brg'] ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kode_brg">Kode Barang</label>
                    <input type="text" name="kode_brg" id="kode_brg" class="form-control" placeholder="Kode Barang" value="<?= $barang['kode_brg'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_brg">Nama Barang</label>
                    <input type="text" name="nama_brg" id="nama_brg" class="form-control" placeholder="Nama Barang" value="<?= $barang['nama_brg'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="ukuran">Ukuran</label>
                    <input type="text" name="ukuran" id="ukuran" class="form-control" placeholder="Ukuran" value="<?= $barang['ukuran'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <div class="input-group">
                        <span class="input-group-addon">Rp</span>
                        <input type="text" name="harga" id="harga" class="form-control" value="<?= $barang['harga'] ?>" placeholder="Harga">
                        <span class="input-group-addon">,00</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="stok">Stok</label>
                    <input type="number" name="stok" id="stok" class="form-control" min="0" onkeypress="input_number(event)" placeholder="Stok" value="<?= $barang['stok'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="stok_ambang">Stok Ambang</label>
                    <input type="number" name="stok_ambang" id="stok_ambang" class="form-control" min="0" onkeypress="input_number(event)" placeholder="Stok Ambang" value="<?= $barang['stok_ambang'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="kode_rak">Rak</label>
                    <select name="kode_rak" id="kode_rak" class="form-control" required>
                        <option value="">Pilih Rak</option>
                        <?php foreach ($rak as $r) : ?>
                            <option value="<?= $r['kode_rak'] ?>" <?= isset($kode_rak) && $r['kode_rak'] == $barang['kode_rak'] ? 'selected' : '' ?>><?= $r['kode_rak'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    <div class="row">
                        <div class="col-md-4">
                            <?php if (!is_null($barang['gambar'])) : ?>
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