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
    $kode_rak = $_POST['kode_rak'];
    $kapasitas = $_POST['kapasitas'];

    try {
        $rak = db_get_one('rak', "kode_rak='$kode_rak'");
        if ($rak) {
            throw new Exception('Kode rak sudah digunakan');
        } else {
            $result = db_insert('rak', [
                'kode_rak' => $kode_rak,
                'kapasitas' => $kapasitas,
            ]);
            if ($result) {
                session_flash('message', 'Data berhasil ditambahkan');
                header('Location: index.php');
                exit;
            } else {
                throw new Exception('Data gagal ditambahkan');
            }
        }
    } catch (Exception $e) {
        session_flash('error', "Gagal: " . $e->getMessage());
    }

}

$title = "Tambah Rak";

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
                    <label for="kode_rak">Kode Rak</label>
                    <input type="text" name="kode_rak" id="kode_rak" class="form-control" placeholder="Kode Rak" value="<?= $kode_rak ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="kapasitas">Kapasitas</label>
                    <input type="number" name="kapasitas" id="kapasitas" class="form-control" min="0" onkeypress="input_number(event)" placeholder="Kapasitas" value="<?= $kapasitas ?? '' ?>" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>