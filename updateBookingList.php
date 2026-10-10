<?php
require_once "machine.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$booking = getBookingById($id);

if (!$booking) {
    echo "<script>alert('Data booking tidak ditemukan!'); window.location.href='tableBooking.php';</script>";
    exit;
}

$pageTitle = "Update Data Booking: " . $booking['judul'];
$message = null;
$messageType = "info";

if (isset($_POST["submit"])) {
    $result = updateBooking($_POST);
    if ($result > 0) {
        echo "<script>alert('Data Booking Berhasil Diperbarui!'); window.location.href='tableBooking.php';</script>";
        exit;
    } else {
        $message = "Gagal memperbarui data booking. Pastikan judul film dan email terisi.";
        $messageType = "warning";
    }
    // Refresh data
    $booking = getBookingById($id) ?? $booking;
}

$filmList = getAllFilms();
$genres = ["Drama", "Comedy", "Horror", "Action", "Animation", "Documenter", "Fantasy", "Science Fiction (Sci - Fi)", "Thriller", "Musical"];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Update Booking - <?= e($booking['judul']) ?></title>
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
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Edit Data Booking Penyewa</h4>
                                <p class="category">Perbarui data kontak penyewa dan judul film yang disewa</p>
                            </div>
                            <div class="content">
                                <form action="updateBookingList.php?id=<?= $id ?>" method="post">
                                    <input type="hidden" name="id" value="<?= (int)$booking['id'] ?>">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Judul Film yang Disewa <span class="text-danger">*</span></label>
                                                <select class="form-control" name="judul" required>
                                                    <option value="<?= e($booking['judul']) ?>" selected><?= e($booking['judul']) ?> (Saat ini)</option>
                                                    <?php foreach ($filmList as $f): ?>
                                                        <?php if ($f['judul'] !== $booking['judul']): ?>
                                                            <option value="<?= e($f['judul']) ?>"><?= e($f['judul']) ?> (<?= e($f['genre']) ?>)</option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Genre Film</label>
                                                <select class="form-control" name="genre">
                                                    <?php foreach ($genres as $g): ?>
                                                        <option value="<?= e($g) ?>" <?= ($booking['genre'] === $g) ? 'selected' : '' ?>><?= e($g) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email Penyewa <span class="text-danger">*</span></label>
                                                <input name="email" type="email" required class="form-control" placeholder="Email Penyewa..." value="<?= e($booking['email']) ?>" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                                                <input name="nomor" type="tel" required class="form-control" placeholder="Nomor Telepon..." value="<?= e($booking['nomor']) ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <a href="tableBooking.php" class="btn btn-default btn-fill">Batal</a>
                                        <button type="submit" name="submit" class="btn btn-info btn-fill">
                                            <i class="fa fa-save"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                    <div class="clearfix"></div>
                                </form>
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
