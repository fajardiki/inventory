<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header("Location: $BASE_URL");
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $jenis = $_POST['jenis'];

    try {
        $pengguna = db_get_one('pengguna', "username='$username'");
        if ($pengguna) {
            throw new Exception('Username sudah digunakan');
        } else {
            $result = db_insert('pengguna', [
                'username' => $username,
                'jenis' => $jenis,
                'password' => password_hash($password, PASSWORD_BCRYPT)
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

$title = "Tambah Pengguna";

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
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?= $username ?? '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="jenis">Jenis</label>
                    <select name="jenis" id="jenis" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="pemilik">Pemilik</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="<?= $password ?? '' ?>" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>