<?php
require_once "machine.php";

$pageTitle = "Daftar Booking Penyewa";
$message = null;
$messageType = "info";

// Handle Tambah Booking
if (isset($_POST["submit"])) {
    $result = tambahBooking($_POST);
    if ($result > 0) {
        $message = "Data booking penyewa berhasil ditambahkan!";
        $messageType = "success";
    } else {
        $message = "Gagal menambahkan data booking. Pastikan judul film dan email penyewa terisi.";
        $messageType = "warning";
    }
}

// Handle Pencarian
$keyword = $_GET["search"] ?? $_POST["search"] ?? "";
if ($keyword !== "") {
    $results = searchBookings($keyword);
} else {
    $results = getAllBookings();
}

// Ambil daftar film untuk opsi dropdown
$filmList = getAllFilms();
$genres = ["Drama", "Comedy", "Horror", "Action", "Animation", "Documenter", "Fantasy", "Science Fiction (Sci - Fi)", "Thriller", "Musical"];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Booking List - Rental DVD Lightning</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/animate.min.css" rel="stylesheet"/>
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <link href="assets/css/demo.css" rel="stylesheet" />

    <!-- Fonts and icons -->
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
                <?php if ($message): ?>
                    <div class="alert alert-<?= $messageType ?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?= e($message) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Search Function -->
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Pencarian Data Booking</h4>
                            </div>
                            <div class="content">
                                <form action="tableBooking.php" method="get">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="Cari berdasarkan judul, genre, email, atau nomor telepon..." value="<?= e($keyword) ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" class="btn btn-info btn-fill btn-block">
                                                <i class="fa fa-search"></i> Cari
                                            </button>
                                        </div>
                                    </div>
                                    <?php if ($keyword !== ''): ?>
                                        <div style="margin-top: 5px;">
                                            <small class="text-muted">Menampilkan hasil untuk: <strong><?= e($keyword) ?></strong> | <a href="tableBooking.php">Reset Pencarian</a></small>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Input / Tambah Penyewa Function -->
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Tambah Data Penyewa Baru</h4>
                            </div>
                            <div class="content">
                                <form action="tableBooking.php" method="post">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pilih Judul Film <span class="text-danger">*</span></label>
                                                <select class="form-control" name="judul" required>
                                                    <option value="" disabled selected>-- Pilih Film dari Koleksi --</option>
                                                    <?php foreach ($filmList as $film): ?>
                                                        <option value="<?= e($film['judul']) ?>"><?= e($film['judul']) ?> (<?= e($film['genre']) ?>)</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Genre Film</label>
                                                <select class="form-control" name="genre">
                                                    <?php foreach ($genres as $g): ?>
                                                        <option value="<?= e($g) ?>"><?= e($g) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email Penyewa <span class="text-danger">*</span></label>
                                                <input name="email" type="email" required class="form-control" placeholder="Contoh: pelanggan@gmail.com" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                                <input name="nomor" type="tel" required class="form-control" placeholder="Contoh: 081234567890" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-info btn-fill pull-right">
                                        <i class="fa fa-user-plus"></i> Tambahkan Penyewa
                                    </button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Booking -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Daftar Booking Aktif (<?= count($results) ?> Data)</h4>
                                <p class="category">Daftar pengguna yang sedang menyewa DVD</p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <th style="width: 50px;">No.</th>
                                        <th>Judul Film</th>
                                        <th>Genre</th>
                                        <th>Email Penyewa</th>
                                        <th>Nomor WhatsApp</th>
                                        <th style="width: 140px;" class="text-center">Aksi</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($results)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted" style="padding: 30px;">
                                                    <i class="pe-7s-info" style="font-size: 28px; display: block; margin-bottom: 8px;"></i>
                                                    Tidak ada data penyewaan ditemukan.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php $i = 1; ?>
                                            <?php foreach ($results as $result): ?>
                                                <tr>
                                                    <td><?= $i++; ?></td>
                                                    <td><strong><?= e($result["judul"]); ?></strong></td>
                                                    <td><span class="label label-info"><?= e($result["genre"]); ?></span></td>
                                                    <td><?= e($result["email"]); ?></td>
                                                    <td>
                                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', (string)$result['nomor']) ?>" target="_blank" class="text-success" title="Chat WhatsApp">
                                                            <i class="fa fa-phone"></i> <?= e($result["nomor"]); ?>
                                                        </a>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="updateBookingList.php?id=<?= (int)$result['id']; ?>" class="btn btn-primary btn-fill btn-sm" title="Edit Data Booking">
                                                            <i class="pe-7s-pen"></i>
                                                        </a>
                                                        <a href="hapusBooking.php?id=<?= (int)$result['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data booking ini?');" class="btn btn-danger btn-fill btn-sm" title="Hapus Data Booking">
                                                            <i class="pe-7s-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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
