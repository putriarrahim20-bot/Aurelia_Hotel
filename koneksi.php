<?php
$server        = "localhost";
$user          = "root";
$pass          = "";
$nama_database = "dbhotel";

date_default_timezone_set('Asia/Jakarta');

mysqli_report(MYSQLI_REPORT_OFF);
$db = @mysqli_connect($server, $user, $pass, $nama_database);

if (!$db) {
    // Fallback to 127.0.0.1 in case localhost socket mapping differs
    $db = @mysqli_connect("127.0.0.1", $user, $pass, $nama_database);
}

if (!$db) {
    die("Gagal terhubung dengan database: " . mysqli_connect_error());
}
mysqli_set_charset($db, "utf8mb4");
