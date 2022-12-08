<?php

require_once '../init.php';

// Jika belum login, arahkan ke halaman login
if (!session_is_login()) {
    header("Location: $BASE_URL/login.php");
}
if (!session_is_admin()) {
    header("Location: $BASE_URL");
}

$title = "Pengguna";

$pengguna = db_get('pengguna');
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
            <h1>Pengguna</h1>
            <p><a href="tambah.php" class="btn btn-primary">Tambah</a></p>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            <?php elseif (isset($message)) : ?>
                <div class="alert alert-info" role="alert">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pengguna as $key => $value) : ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $value['username'] ?></td>
                            <td><?= $value['jenis'] ?></td>
                            <td>
                                <a href="edit.php?username=<?= $value['username'] ?>" class="btn btn-warning">Edit</a>
                                <?php if ($value['username'] != session_get_username()) {?>
                                <button onclick="hapus('<?=$value['username']?>')" class="btn btn-danger">Hapus</button>
                                <?php } ?>
                            </td>
                            <!-- modal -->
                            <div class="modal fade" id="modalHapus<?=$value['username']?>" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
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
                                            <a href="hapus.php?username=<?=$value['username']?>" class="btn btn-primary">Ya</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function hapus(username) {
        $(`#modalHapus${username}`).modal('show');
    }
</script>
<?php include '../layout/footer.php'; ?>