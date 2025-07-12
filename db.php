<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "yayasan_baituljihad";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>