<?php 
session_start(); 
include 'inc/db.php';
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];

$total_siswa = $conn->query("SELECT COUNT(*) AS total FROM siswa")->fetch_assoc()['total'];
$total_alumni = $conn->query("SELECT COUNT(*) AS total FROM siswa WHERE is_alumni = 1")->fetch_assoc()['total'];
$total_hasil = $conn->query("SELECT COUNT(DISTINCT id_siswa) AS total FROM hasil_seleksi")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin - Yayasan Baitul Jihad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f0f2f5; }
    .toggle-btn { display: none; background: #2c3e50; color: white; padding: 10px; position: fixed; top: 10px; left: 10px; z-index: 1000; border: none; border-radius: 4px; }
    .sidebar { width: 220px; height: 100vh; background-color: #2c3e50; color: white; position: fixed; padding: 20px; transition: transform 0.3s ease-in-out; }
    .sidebar.collapsed { transform: translateX(-100%); }
    .sidebar h2 { font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; }
    .sidebar img { margin-right: 10px; }
    .sidebar ul { list-style: none; padding: 0; }
    .sidebar ul li { margin: 25px 0; }
    .sidebar ul li a { color: white; text-decoration: none; font-size: 14px; display: flex; align-items: center; }
    .sidebar ul li a i { margin-right: 10px; }
    .sidebar ul li a:hover { text-decoration: underline; }
    .main { margin-left: 240px; padding: 30px; }
    .main h1 { font-size: 24px; margin-bottom: 10px; }
    .container { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px; }
    .card-summary { flex: 1 1 250px; background: #fff; border-left: 6px solid #2196f3; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border-radius: 10px; text-align: center; }
    .card-summary h3 { margin: 0; font-size: 16px; color: #444; }
    .card-summary .value { font-size: 30px; font-weight: bold; color: #2c3e50; margin-top: 8px; }
    .card-summary i { font-size: 26px; margin-bottom: 10px; color: #2196f3; }
    .footer { text-align: center; font-size: 14px; color: #888; padding: 30px 0; }
    .user-info { text-align: right; margin-bottom: 10px; color: #555; font-size: 14px; }
    .chart-container { max-width: 600px; margin: 0 auto; }
    canvas { max-width: 100% !important; height: auto !important; }
    @media screen and (max-width: 768px) {
      .main { margin-left: 0; padding: 20px; }
      .sidebar { position: absolute; width: 100%; height: auto; }
      .toggle-btn { display: block; }
    }
  </style>
</head>
<body>
  <button class="toggle-btn" onclick="toggleSidebar()">☰ Menu</button>
  <div class="sidebar" id="sidebar">
    <h2><img src="assets/img/logo.png" alt="Logo" style="height: 50px;"> ADMIN</h2>
    <ul>
      <li><a href="dashboard_admin.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="tambah_siswa.php"><i class="fas fa-user-plus"></i> Data Siswa</a></li>
      <li><a href="proses_maut.php"><i class="fas fa-calculator"></i> Proses MAUT</a></li>
      <li><a href="proses_topsis.php"><i class="fas fa-sliders-h"></i> Proses TOPSIS</a></li>
      <li><a href="hasil.php"><i class="fas fa-poll"></i> Hasil Seleksi</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="user-info">👤 Login sebagai: <strong><?= htmlspecialchars($username) ?></strong></div>
    <h1>Dashboard Admin Penerimaan Siswa Tidak Mampu</h1>
    <p>Selamat datang di sistem informasi Yayasan Baitul Jihad.</p>

    <div class="container">
      <div class="card-summary">
        <i class="fas fa-users"></i>
        <h3>Total Siswa Terdaftar</h3>
        <div class="value"><?= $total_siswa ?></div>
      </div>
      <div class="card-summary">
        <i class="fas fa-user-graduate"></i>
        <h3>Total Alumni</h3>
        <div class="value"><?= $total_alumni ?></div>
      </div>
      <div class="card-summary">
        <i class="fas fa-times-circle"></i>
        <h3>Total Hasil Seleksi</h3>
        <div class="value"><?= $total_hasil ?></div>
      </div>
    </div>

    <div class="card">
      <h2>Diagram Batang Jumlah Data</h2>
      <div class="chart-container">
        <canvas id="barChart"></canvas>
      </div>
    </div>

    <div class="card">
      <h2>Diagram Pie Alumni vs Non-Alumni</h2>
      <div class="chart-container">
        <canvas id="pieChart"></canvas>
      </div>
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

    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
      type: 'bar',
      data: {
        labels: ['Siswa', 'Alumni', 'Hasil Seleksi'],
        datasets: [{
          label: 'Jumlah',
          data: [<?= $total_siswa ?>, <?= $total_alumni ?>, <?= $total_hasil ?>],
          backgroundColor: ['#3498db', '#2ecc71', '#f1c40f'],
          borderColor: ['#2980b9', '#27ae60', '#f39c12'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } }
        }
      }
    });

    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: ['Non-Alumni', 'Alumni'],
        datasets: [{
          data: [<?= $total_siswa - $total_alumni ?>, <?= $total_alumni ?>],
          backgroundColor: ['#36A2EB', '#FF6384'],
          hoverOffset: 4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: function(context) {
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const value = context.raw;
                const percent = ((value / total) * 100).toFixed(1);
                return `${context.label}: ${value} siswa (${percent}%)`;
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
