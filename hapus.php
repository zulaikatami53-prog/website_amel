<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("location:login.php");
    exit();
}
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    mysqli_query($koneksi, "DELETE FROM penjualan WHERE id='$id'");
}

header("location:index.php");
exit();
?>
