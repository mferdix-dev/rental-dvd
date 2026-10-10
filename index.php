<?php
require_once "machine.php";

$pageTitle = "Dashboard Overview";
$stats = getDashboardStats();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Dashboard - Rental DVD Lightning</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <link href="assets/css/demo.css" rel="stylesheet" />

    <!-- Fonts and icons (HTTPS) -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
</head>
<body>

<div class="wrapper">
    <?php require_once 'assets/html/sidebar.php'; ?>

    <div class="main-panel">
        <?php require_once 'assets/html/navbar.php'; ?>

        <div class="content">
            <div class="container-fluid">
                <!-- Stat Cards -->
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="icon-big icon-warning text-center" style="font-size: 48px; color: #ff9800;">
                                            <i class="pe-7s-film"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="numbers">
                                            <p style="margin: 0; color: #888;">Total Koleksi Film</p>
                                            <h3 style="margin: 5px 0 0 0; font-weight: bold;"><?= $stats['total_films'] ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr style="margin: 10px 0;">
                                    <div class="stats">
                                        <a href="filmList.php"><i class="fa fa-arrow-circle-right"></i> Lihat & Kelola Film</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <div class="icon-big icon-success text-center" style="font-size: 48px; color: #4caf50;">
                                            <i class="pe-7s-note2"></i>
                                        </div>
                                    </div>
                                    <div class="col-xs-8">
                                        <div class="numbers">
                                            <p style="margin: 0; color: #888;">Total Penyewa Aktif</p>
                                            <h3 style="margin: 5px 0 0 0; font-weight: bold;"><?= $stats['total_bookings'] ?></h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="footer">
                                    <hr style="margin: 10px 0;">
                                    <div class="stats">
                                        <a href="tableBooking.php"><i class="fa fa-arrow-circle-right"></i> Lihat & Kelola Booking</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Bookings Table & Quick Action -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Penyewaan Terbaru</h4>
                                <p class="category">5 transaksi booking terakhir yang tercatat</p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <th>Judul Film</th>
                                        <th>Genre</th>
                                        <th>Email Penyewa</th>
                                        <th>Kontak</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($stats['latest_bookings'])): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted" style="padding: 20px;">Belum ada data penyewaan saat ini.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($stats['latest_bookings'] as $booking): ?>
                                                <tr>
                                                    <td><strong><?= e($booking['judul']) ?></strong></td>
                                                    <td><span class="label label-info"><?= e($booking['genre']) ?></span></td>
                                                    <td><?= e($booking['email']) ?></td>
                                                    <td><?= e($booking['nomor']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Film Terbaru</h4>
                                <p class="category">Daftar judul yang baru ditambahkan</p>
                            </div>
                            <div class="content">
                                <?php if (empty($stats['latest_films'])): ?>
                                    <p class="text-muted text-center" style="padding: 20px 0;">Belum ada data film.</p>
                                <?php else: ?>
                                    <ul class="list-unstyled" style="margin-bottom: 0;">
                                        <?php foreach ($stats['latest_films'] as $film): ?>
                                            <li style="display: flex; align-items: center; margin-bottom: 15px;">
                                                <div style="width: 50px; height: 65px; margin-right: 12px; flex-shrink: 0; background: #eee; border-radius: 4px; overflow: hidden;">
                                                    <?php if (!empty($film['image']) && file_exists(__DIR__ . '/assets/img/dvd-cover/' . $film['image'])): ?>
                                                        <img src="assets/img/dvd-cover/<?= e($film['image']) ?>" alt="<?= e($film['judul']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #aaa;">
                                                            <i class="pe-7s-film" style="font-size: 24px;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <h5 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600;"><?= e($film['judul']) ?></h5>
                                                    <small class="text-muted"><?= e($film['genre']) ?> (<?= e($film['tahun']) ?>) &bull; ⭐ <?= e($film['rating']) ?>/5</small>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <hr style="margin: 10px 0;">
                                <div class="text-center">
                                    <a href="filmList.php" class="btn btn-primary btn-sm btn-fill">Lihat Semua Film</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer">
            <div class="container-fluid">
                <p class="copyright pull-right">
                    &copy; <?= date('Y') ?> Rental DVD Lightning. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</div>

<!-- Core JS Files -->
<script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap-notify.js"></script>
<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

</body>
</html>
