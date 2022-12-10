<?php

require_once 'init.php';

$title = 'Menu';

$chart = db_chart();
// echo $chart['jumlah_penjualan'];

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
    <div class="row">
        <canvas style="margin-top: 25px; width: 300px !important; height: 100px !important;" id="myChart"></canvas>
    </div>
</div>
<script>
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($chart['bulan']) ?>,
            datasets: [{
                    label: 'Pembelian',
                    data: <?php echo json_encode($chart['jumlah_pembelian']) ?>,
                    borderWidth: 1
                },
                {
                    label: 'Penjualan',
                    data: <?php echo json_encode($chart['jumlah_penjualan']) ?>,
                    borderWidth: 1
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: "Penjualan Pembelian Penjualan " + <?= date('Y'); ?>
                }
            }
        }
    });
</script>

<?php include 'layout/footer.php'; ?>