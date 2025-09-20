<?php
include "../../config.php";
session_start();

$id = $_GET['id'];
$conn->query("DELETE FROM kategori_produk WHERE id='$id'");
header("Location: kategori.php?msg=deleted");
?>
