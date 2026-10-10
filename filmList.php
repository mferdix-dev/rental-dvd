<?php
require_once "machine.php";

$pageTitle = "Daftar Koleksi Film";
$message = null;
$messageType = "info";

// Handle Tambah Film
if (isset($_POST["submit"])) {
    $result = tambahFilm($_POST);
    if ($result > 0) {
        $message = "Film berhasil ditambahkan ke database!";
        $messageType = "success";
    } elseif ($result === -1) {
        $message = "Gagal mengunggah gambar. Pastikan format file (JPG, PNG, WEBP) dan ukuran maksimal 2MB.";
        $messageType = "danger";
    } else {
        $message = "Gagal menambahkan film. Pastikan judul terisi.";
        $messageType = "warning";
    }
}

// Handle Pencarian
$keyword = $_GET["search"] ?? $_POST["search"] ?? "";
if ($keyword !== "") {
    $results = searchFilms($keyword);
} else {
    $results = getAllFilms();
}

$genres = ["Drama", "Comedy", "Horror", "Action", "Animation", "Documenter", "Fantasy", "Science Fiction (Sci - Fi)", "Thriller", "Musical"];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Film List - Rental DVD Lightning</title>
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
                                <h4 class="title">Pencarian Film</h4>
                            </div>
                            <div class="content">
                                <form action="filmList.php" method="get">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <input name="search" type="text" class="form-control" placeholder="Cari berdasarkan judul, genre, sutradara, atau tahun..." value="<?= e($keyword) ?>" autocomplete="off">
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
                                            <small class="text-muted">Menampilkan hasil untuk: <strong><?= e($keyword) ?></strong> | <a href="filmList.php">Reset Pencarian</a></small>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Input / Upload Film Function -->
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Tambah Koleksi Film Baru</h4>
                            </div>
                            <div class="content">
                                <form action="filmList.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Judul Film <span class="text-danger">*</span></label>
                                                <input name="judul" type="text" required class="form-control" placeholder="Masukkan Judul Film..." autocomplete="off">
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
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Rating (1 - 5)</label>
                                                <select name="rating" class="form-control">
                                                    <?php for ($r = 1; $r <= 5; $r++): ?>
                                                        <option value="<?= $r ?>"><?= $r ?> Bintang</option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Tahun Rilis</label>
                                                <input name="tahun" type="number" min="1900" max="2099" class="form-control" placeholder="Contoh: 2024" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Sutradara</label>
                                                <input name="sutradara" type="text" class="form-control" placeholder="Nama Sutradara..." autocomplete="off">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Poster Cover Film (JPG, PNG, WEBP - Max 2MB)</label>
                                                <input type="file" name="file" class="form-control" accept="image/jpeg,image/png,image/webp">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" name="submit" class="btn btn-info btn-fill pull-right">
                                        <i class="fa fa-plus"></i> Simpan Film
                                    </button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Film Grid -->
                <div class="row">
                    <div class="col-md-12">
                        <h4 style="margin: 20px 0 15px 0;">Daftar Film (<?= count($results) ?> Total)</h4>
                    </div>

                    <?php if (empty($results)): ?>
                        <div class="col-md-12">
                            <div class="alert alert-warning text-center" style="padding: 30px;">
                                <i class="pe-7s-info" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                                Tidak ada data film yang ditemukan.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($results as $result): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card" style="margin-bottom: 25px; overflow: hidden; border-radius: 6px;">
                                    <div style="height: 320px; background: #222; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                        <?php if (!empty($result['image']) && file_exists(__DIR__ . '/assets/img/dvd-cover/' . $result['image'])): ?>
                                            <img src="assets/img/dvd-cover/<?= e($result['image']) ?>" alt="<?= e($result['judul']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="text-center text-muted" style="color: #888;">
                                                <i class="pe-7s-film" style="font-size: 48px; display: block; margin-bottom: 8px;"></i>
                                                <small>No Cover Image</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="content" style="padding: 15px;">
                                        <h4 class="title" style="font-size: 18px; margin-top: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= e($result['judul']) ?>">
                                            <?= e($result["judul"]) ?>
                                        </h4>
                                        <div style="margin-bottom: 12px; font-size: 13px; color: #555;">
                                            <div><strong>Genre:</strong> <span class="label label-default"><?= e($result["genre"]) ?></span></div>
                                            <div style="margin-top: 4px;"><strong>Rating:</strong> ⭐ <?= e($result["rating"]) ?>/5</div>
                                            <div style="margin-top: 4px;"><strong>Tahun:</strong> <?= e($result["tahun"]) ?></div>
                                            <div style="margin-top: 4px;"><strong>Sutradara:</strong> <?= e($result["sutradara"]) ?></div>
                                        </div>
                                        <hr style="margin: 10px 0;">
                                        <div class="text-right">
                                            <a href="updateFilmList.php?id=<?= (int)$result['id'] ?>" class="btn btn-primary btn-fill btn-sm">
                                                <i class="pe-7s-pen"></i> Edit
                                            </a>
                                            <a href="hapusFilm.php?id=<?= (int)$result['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus film ini? Data tidak dapat dipulihkan.');" class="btn btn-danger btn-fill btn-sm">
                                                <i class="pe-7s-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
