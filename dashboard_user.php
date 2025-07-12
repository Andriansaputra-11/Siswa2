<?php 
session_start(); 
include 'inc/db.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard User - Yayasan Baitul Jihad</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f8f9fa;
    }
    .sidebar {
      position: fixed;
      width: 220px;
      height: 100vh;
      background-color: #1e2b39;
      color: white;
      display: flex;
      flex-direction: column;
      padding: 20px;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
      transition: transform 0.3s ease-in-out;
    }
    .sidebar.collapsed {
      transform: translateX(-100%);
    }
    .toggle-btn {
      display: none;
      background: #1e2b39;
      color: white;
      padding: 10px;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1000;
      border: none;
      border-radius: 4px;
    }
    .logo-section {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
    }
    .logo {
      height: 60px;
      margin-right: 15px;
    }
    .logo-section h2 {
      font-size: 18px;
      font-weight: bold;
      margin: 0;
      color: #f1f1f1;
    }
    .menu {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    .menu li {
      margin-bottom: 20px;
    }
    .menu li a {
      display: flex;
      align-items: center;
      color: #cfd8dc;
      text-decoration: none;
      font-size: 15px;
      transition: 0.3s;
      padding: 10px;
      border-radius: 6px;
    }
    .menu li a i {
      margin-right: 10px;
      width: 18px;
    }
    .menu li a:hover {
      background-color: #34495e;
      color: white;
    }
    .main {
      margin-left: 240px;
      padding: 30px; 
    }
    .main h1 {
      font-size: 24px;
      margin-bottom: 10px;
      color: #2c3e50;
    }
    .carousel-container {
      overflow: hidden;
      width: 100%;
      background: #fff;
      margin: 20px 0;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .carousel-track {
      display: flex;
    }
    .carousel-slide {
      display: flex;
      animation: scrollLeft 50s linear infinite;
    }
    .carousel-slide img {
      width: 330px;
      height: 240px;
      object-fit: cover;
      margin-right: 10px;
      border-radius: 8px;
    }
    @keyframes scrollLeft {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
    .container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 20px;
    }
    .card {
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
      flex: 1;
      min-width: 300px;
    }
    .footer {
      text-align: center;
      font-size: 14px;
      color: #888;
      padding: 20px 0;
    }
    .user-info {
      text-align: right;
      margin-bottom: 10px;
      color: #555;
      font-size: 14px;
    }
    @media screen and (max-width: 768px) {
      .main {
        margin-left: 0;
        padding: 20px;
      }
      .toggle-btn {
        display: block;
      }
    }
  </style>
</head>
<body>

  <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="logo-section">
      <img src="assets/img/logo.png" alt="Logo" class="logo">
      <h2>BAITUL JIHAD</h2>
    </div>
    <ul class="menu">
      <li><a href="dashboard_user.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="hasil.php"><i class="fas fa-chart-line"></i> Hasil Seleksi</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main">
    <div class="user-info">👤 Anda login sebagai: <strong><?= htmlspecialchars($username) ?></strong></div>

    <h1>Selamat datang Calon Siswa/i di Sistem Informasi Yayasan Baitul Jihad</h1>

    <div class="carousel-container">
      <div class="carousel-track">
        <div class="carousel-slide">
          <img src="assets/img/andi.jpg" alt="Banner 1">
          <img src="assets/img/andi.jpg" alt="Banner 2">
          <img src="assets/img/andi.jpg" alt="Banner 3">
          <img src="assets/img/andi.jpg" alt="Banner 4">
          <img src="assets/img/andi.jpg" alt="Banner 1 Duplicate">
          <img src="assets/img/andi.jpg" alt="Banner 2 Duplicate">
        </div>
      </div>
    </div>

    <div class="container">
      <div class="card">
        <h1>Selamat Datang Calon Siswa</h1>
        <p>Anda dapat melihat hasil seleksi Anda pada menu "Hasil Seleksi".</p>
      </div>
      <div class="card">
        <h1>Informasi</h1>
        <p>Silakan cek secara berkala untuk melihat apakah Anda lolos seleksi penerimaan berdasarkan kriteria sosial-ekonomi dan alumni.</p>
      </div>
    </div>

    <div class="card">
      <h2>Tentang Yayasan Baitul Jihad</h2>
      <p>
        Yayasan Baitul Jihad merupakan lembaga sosial dan pendidikan yang berfokus pada pemberdayaan masyarakat melalui pendidikan berkualitas dan bantuan sosial. Kami berkomitmen memberikan akses pendidikan bagi siswa/siswi dari keluarga tidak mampu agar memiliki masa depan yang lebih baik. Sistem ini dibangun untuk membantu proses seleksi penerimaan siswa secara objektif dan transparan.
      </p>
    </div>

    <div class="footer">
      &copy; <?= date('Y') ?> Yayasan Baitul Jihad. All rights reserved.
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('collapsed');
    }
  </script>
</body>
</html>
