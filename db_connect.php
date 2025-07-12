<?php
$servername = "localhost";  // Sesuaikan dengan nama server Anda
$username = "root";         // Sesuaikan dengan username MySQL Anda
$password = "";             // Sesuaikan dengan password MySQL Anda
$dbname = "yayasan";         // Sesuaikan dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
