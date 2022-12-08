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
    $kode_sup = $_POST['kode_sup'];
    $nama_sup = $_POST['nama_sup'];
    $alamat_sup = $_POST['alamat_sup'];
    $telp_sup = $_POST['telp_sup'];

    try {
        $supplier = db_get_one('supplier', "kode_sup='$kode_sup'");
        if ($supplier) {
            throw new Exception('Kode supplier sudah digunakan');
        }
        $result = db_insert('supplier', [
            'kode_sup' => $kode_sup,
            'nama_sup' => $nama_sup,
            'alamat_sup' => $alamat_sup,
            'telp_sup' => $telp_sup,
        ]);
        if ($result) {
            session_flash('message', 'Data berhasil ditambahkan');
            header('Location: index.php');
            exit;
        } else {
            throw new Exception('Data gagal ditambahkan');
        }
    } catch (Exception $e) {
        session_flash('error', $e->getMessage());
    }

}

$title = "Tambah Supplier";

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
            <h1><?=$title?></h1>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php elseif (isset($message)) : ?>
                <div class="alert alert-info" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <form action="" method="post">
                <div class="form-group">
                    <label for="kode_sup">Kode Supplier</label>
                    <input type="text" name="kode_sup" id="kode_sup" class="form-control" placeholder="Kode Supplier" value="<?= $kode_sup ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_sup">Nama Supplier</label>
                    <input type="text" name="nama_sup" id="nama_sup" class="form-control" placeholder="Nama Supplier" value="<?= $nama_sup ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="alamat_sup">Alamat Supplier</label>
                    <textarea name="alamat_sup" id="alamat_sup" class="form-control" placeholder="Alamat" required><?= $alamat_sup ?? '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="telp_sup">Telp Supplier</label>
                    <input type="text" name="telp_sup" id="telp_sup" class="form-control" placeholder="Telp" value="<?= $telp_sup ?? '' ?>" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>