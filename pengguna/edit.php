<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header("Location: $BASE_URL");
}

if (!isset($_GET['username'])) {
    header('Location: index.php');
}
$username = $_GET['username'];
$pengguna = db_get_one('pengguna', "username='$username'");
if (!$pengguna) {
    header('Location: index.php');
}

if (isset($_POST['submit'])) {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    try {
        if ($new_username != $username) {
            $pengguna = db_get_one('pengguna', "username='$new_username'");
            if ($pengguna) {
                throw new Exception("Username '$new_username' sudah digunakan");
            }
        }
        $result = db_update('pengguna', [
            'username' => $new_username,
            'password' => password_hash($new_password, PASSWORD_BCRYPT),
        ], "username = '$username'");
        if ($result) {
            session_flash('message', 'Data berhasil diubah');
            header('Location: index.php');
            exit;
        } else {
            throw new Exception('Data gagal diubah');
        }
    } catch (Exception $e) {
        session_flash('error', $e->getMessage());
        header("Location: edit.php?username=$username");
        exit;
    }
}

$title = 'Edit Pengguna';

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
            <form action="edit.php?username=<?=$pengguna['username']?>" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?=$pengguna['username']?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>