<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<div class="sidebar" data-color="purple" data-image="assets/img/sidebar-5.jpg">
    <div class="sidebar-wrapper">
        <div class="logo">
            <a href="index.php" class="simple-text">
                Rental DVD
            </a>
        </div>

        <ul class="nav">
            <li class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">
                <a href="index.php">
                    <i class="pe-7s-graph"></i>
                    <p>Dashboard</p>
                </a>
            </li>
            <li class="<?= in_array($currentPage, ['tableBooking.php', 'updateBookingList.php'], true) ? 'active' : '' ?>">
                <a href="tableBooking.php">
                    <i class="pe-7s-note2"></i>
                    <p>Booking List</p>
                </a>
            </li>
            <li class="<?= in_array($currentPage, ['filmList.php', 'updateFilmList.php'], true) ? 'active' : '' ?>">
                <a href="filmList.php">
                    <i class="pe-7s-film"></i>
                    <p>Film List</p>
                </a>
            </li>
        </ul>
    </div>
</div>
