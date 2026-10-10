<?php
require_once "machine.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$film = getFilmById($id);

if (!$film) {
    echo "<script>alert('Film tidak ditemukan!'); window.location.href='filmList.php';</script>";
    exit;
}

$pageTitle = "Update Film: " . $film['judul'];
$message = null;
$messageType = "info";

if (isset($_POST["submit"])) {
    $result = updateFilmList($_POST);
    if ($result > 0) {
        echo "<script>alert('Film Berhasil Diperbarui!'); window.location.href='filmList.php';</script>";
        exit;
    } elseif ($result === -1) {
        $message = "Gagal mengunggah cover baru. Pastikan format file (JPG, PNG, WEBP) dan ukuran maksimal 2MB.";
        $messageType = "danger";
    } else {
        $message = "Gagal memperbarui film. Pastikan judul film tidak kosong.";
        $messageType = "warning";
    }
    // Refresh film data
    $film = getFilmById($id) ?? $film;
}

$genres = ["Drama", "Comedy", "Horror", "Action", "Animation", "Documenter", "Fantasy", "Science Fiction (Sci - Fi)", "Thriller", "Musical"];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Update Film - <?= e($film['judul']) ?></title>
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
                                <h4 class="title">Edit Informasi Film</h4>
                                <p class="category">Perbarui rincian metadata dan poster film</p>
                            </div>
                            <div class="content">
                                <form action="updateFilmList.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?= (int)$film['id'] ?>">
                                    <input type="hidden" name="gambarLama" value="<?= e($film['image']) ?>">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Judul Film <span class="text-danger">*</span></label>
                                                <input name="judul" type="text" required class="form-control" placeholder="Masukkan Judul Film..." value="<?= e($film['judul']) ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Genre Film</label>
                                                <select class="form-control" name="genre">
                                                    <?php foreach ($genres as $g): ?>
                                                        <option value="<?= e($g) ?>" <?= ($film['genre'] === $g) ? 'selected' : '' ?>><?= e($g) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Rating (1 - 5)</label>
                                                <select name="rating" class="form-control">
                                                    <?php for ($r = 1; $r <= 5; $r++): ?>
                                                        <option value="<?= $r ?>" <?= ((string)$film['rating'] === (string)$r) ? 'selected' : '' ?>><?= $r ?> Bintang</option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tahun Rilis</label>
                                                <input name="tahun" type="number" min="1900" max="2099" class="form-control" placeholder="Tahun Rilis..." value="<?= e($film['tahun']) ?>" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sutradara</label>
                                                <input name="sutradara" type="text" class="form-control" placeholder="Nama Sutradara..." value="<?= e($film['sutradara']) ?>" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Poster Cover Saat Ini</label>
                                                <div style="margin-bottom: 10px;">
                                                    <?php if (!empty($film['image']) && file_exists(__DIR__ . '/assets/img/dvd-cover/' . $film['image'])): ?>
                                                        <img src="assets/img/dvd-cover/<?= e($film['image']) ?>" alt="<?= e($film['judul']) ?>" style="max-height: 180px; border-radius: 4px; border: 1px solid #ddd; padding: 4px;">
                                                    <?php else: ?>
                                                        <span class="text-muted">Belum ada cover.</span>
                                                    <?php endif; ?>
                                                </div>
                                                <label>Ganti Poster Cover (Opsional - Kosongkan jika tidak diganti)</label>
                                                <input type="file" name="file" class="form-control" accept="image/jpeg,image/png,image/webp">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <a href="filmList.php" class="btn btn-default btn-fill">Batal</a>
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
