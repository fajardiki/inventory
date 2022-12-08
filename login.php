<?php

require_once 'init.php';

$title = 'Login';

// Jika sudah login, arahkan ke halaman index (menu)
if (session_is_login()) {
    header("Location: $BASE_URL/index.php");
}

// Jika ada data yang dikirimkan, cek apakah data tersebut benar
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = db_get_one('pengguna', "username = '$username'");
    
    if ($user != null) {
        // Jika data ditemukan, cek apakah password benar
        if (password_verify($password, $user['password'])) {
            // Jika benar, buat sesi dan arahkan ke halaman index (menu)
            session_set_user($username, $user['jenis']);
            header('Location: index.php');
        } else {
            // Jika salah, tampilkan pesan error
            $error = 'Password salah';
        }
    } else {
        // Jika data tidak ditemukan, tampilkan pesan error
        $error = 'Username tidak ditemukan';
    }
}
?>

<!-- mulai halaman -->
<?php include 'layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    <!-- Jika ada error, maka tampilkan -->
                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>