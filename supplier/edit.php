<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header('Location: index.php');
}

if (!isset($_GET['kode_sup'])) {
    header('Location: index.php');
}
$kode_sup = $_GET['kode_sup'];
$supplier = db_get_one('supplier', "kode_sup='$kode_sup'");
if (!$supplier) {
    header('Location: index.php');
}

if (isset($_POST['submit'])) {
    $new_kode_sup = $_POST['kode_sup'];
    $new_alamat_sup = $_POST['alamat_sup'];
    $new_telp_sup = $_POST['telp_sup'];

    try {
        if ($new_kode_sup != $kode_sup) {
            $supplier = db_get_one('supplier', "kode_sup='$new_kode_sup'");
            if ($supplier) {
                throw new Exception("Kode supplier '$new_kode_sup' sudah digunakan");
            }
        }
        $result = db_update('supplier', [
            'kode_sup' => $new_kode_sup,
            'alamat_sup' => $new_alamat_sup,
            'telp_sup' => $new_telp_sup,
        ], "kode_sup = '$kode_sup'");
        if ($result) {
            session_flash('message', 'Data berhasil diubah');
            header('Location: index.php');
            exit;
        } else {
            throw new Exception('Data gagal diubah');
        }
    } catch (Exception $e) {
        session_flash('error', $e->getMessage());
        header("Location: edit.php?kode_sup=$kode_sup");
        exit;
    }
}

$title = "Edit Supplier";

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
            <form action="edit.php?kode_sup=<?=$supplier['kode_sup']?>" method="post">
                <div class="form-group">
                    <label for="kode_sup">Kode Supplier</label>
                    <input type="text" name="kode_sup" id="kode_sup" class="form-control" placeholder="Kode Rak" value="<?=$supplier['kode_sup']?>" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat Supplier</label>
                    <textarea name="alamat_sup" id="alamat_sup" class="form-control" placeholder="Alamat" required><?=$supplier['alamat_sup']?></textarea>
                </div>
                <div class="form-group">
                    <label for="telp_sup">Telp Supplier</label>
                    <input type="text" name="telp_sup" id="telp_sup" class="form-control" placeholder="Kode Rak" value="<?=$supplier['telp_sup']?>" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>