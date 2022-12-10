<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?: 'Page' ?> | Sistem Inventory</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/site.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= BASE_URL ?>/assets/css/toastify.min.css">
    <script src="<?= BASE_URL ?>/assets/js/jquery-1.10.1.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/jquery-mask.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/helper.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/jquery.dataTables.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/chart.js"></script>
    <script type="text/javascript" src="<?= BASE_URL ?>/assets/js/toastify-js"></script>
</head>

<body>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-4">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?= BASE_URL ?>">Sistem Inventory</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-4">
                <?php if (session_is_login()) { ?>
                    <a class="btn btn-warning navbar-btn" href="<?= BASE_URL . '/logout.php' ?>">Sign out</a>
                <?php } else { ?>
                    <a class="btn btn-success navbar-btn" href="<?= BASE_URL . '/login.php' ?>">Sign in</a>
                <?php } ?>
            </div>
        </div>
    </nav>