<?php
declare(strict_types=1);

require_once "machine.php";

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id > 0) {
    hapusBooking($id);
}

header("Location: tableBooking.php");
exit;
