<?php

require_once 'init.php';

$title = 'Menu';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
?>

<!-- mulai halaman -->
<?php include 'layout/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Menu</h1>
            <p>Selamat datang, <?= session_get_username() ?></p>

            <?php include './layout/menu.php'; ?>

        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>