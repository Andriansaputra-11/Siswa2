<?php 
session_start();
include 'inc/db.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama             = $_POST['nama'];
  $nik              = $_POST['nik'];
  $asal_sekolah     = $_POST['asal_sekolah'];
  $jenjang          = $_POST['jenjang'];
  $status_ortu      = $_POST['status_ortu'];
  $penghasilan_ortu = $_POST['penghasilan_ortu'];
  $tanggungan_ortu  = $_POST['tanggungan_ortu'];
  $status_rumah     = $_POST['status_rumah'];
  $is_alumni        = isset($_POST['is_alumni']) ? 1 : 0;
  $ac               = isset($_POST['ac']) ? 1 : 0;
  $tv               = isset($_POST['tv']) ? 1 : 0;
  $kulkas           = isset($_POST['kulkas']) ? 1 : 0;
  $motor            = $_POST['motor'];
  $jarak_km         = $_POST['jarak_km'];

  $sql = "INSERT INTO siswa (
    nama, nik, asal_sekolah, jenjang, status_ortu, penghasilan_ortu, tanggungan_ortu,
    status_rumah, is_alumni, ac, tv, kulkas, motor, jarak_km
  ) VALUES (
    '$nama', '$nik', '$asal_sekolah', '$jenjang', '$status_ortu', '$penghasilan_ortu',
    '$tanggungan_ortu', '$status_rumah', '$is_alumni', '$ac', '$tv', '$kulkas', '$motor', '$jarak_km'
  )";

  if ($conn->query($sql)) {
    $id_siswa = $conn->insert_id;
    $conn->query("INSERT INTO hasil_seleksi (id_siswa, metode, nilai, ranking) VALUES ($id_siswa, 'MAUT', 0, 0)");
    echo "<script>alert('Data siswa berhasil ditambahkan'); window.location.href='hasil.php';</script>";
  } else {
    echo "Gagal menyimpan data: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Data Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }
    .sidebar {
      width: 220px;
      height: 100vh;
      background-color: #2c3e50;
      color: white;
      position: fixed;
      padding: 20px;
    }
    .sidebar h2 {
      font-size: 18px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    .sidebar img {
      margin-right: 10px;
    }
    .sidebar ul {
      list-style: none;
      padding: 0;
    }
    .sidebar ul li {
      margin: 25px 0;
    }
    .sidebar ul li a {
      color: white;
      text-decoration: none;
      font-size: 14px;
      display: flex;
      align-items: center;
    }
    .sidebar ul li a i {
      margin-right: 10px;
    }
    .sidebar ul li a:hover {
      text-decoration: underline;
    }
    .main {
      margin-left: 240px;
      padding: 30px;
    }
    .form-container {
      h2 { text-align: center; color:rgb(14, 13, 13); margin-bottom: 30px; }
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: auto;
    }
    h2 { text-align: center; color:rgb(255, 255, 255); margin-bottom: 30px; }
    label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
    input[type="text"], input[type="number"], select {
      width: 100%;
      padding: 10px 14px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    .checkbox-group { margin-bottom: 20px; }
    .checkbox-group label { margin-right: 15px; font-weight: normal; }
    .submit-btn {
      width: 100%;
      padding: 14px;
      background-color: #2980b9;
      border: none;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
    }
    .submit-btn:hover {
      background-color: #1c6fa5;
    }
    @media screen and (max-width: 768px) {
      .main { margin-left: 0; padding: 20px; }
      .sidebar { width: 100%; position: relative; height: auto; }
    }
  </style>
</head>
<body>
  <div class="sidebar">
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
    <div class="form-container">
      <h2>📋 Tambah Data Siswa</h2>
      <form method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" required>

        <label>NIK</label>
        <input type="text" name="nik" required>

        <label>Asal Sekolah</label>
        <input type="text" name="asal_sekolah" required>

        <label>Jenjang Pendaftaran</label>
        <select name="jenjang" required>
          <option value="">-- Pilih Jenjang --</option>
          <option value="SD">SD</option>
          <option value="SMP">SMP</option>
        </select>

        <label>Status Orang Tua</label>
        <select name="status_ortu" required>
          <option value="">-- Pilih --</option>
          <option value="Lengkap">Lengkap</option>
          <option value="Yatim">Yatim</option>
          <option value="Yatim Piatu">Yatim Piatu</option>
        </select>

        <label>Penghasilan Orang Tua</label>
        <input type="number" name="penghasilan_ortu" required>

        <label>Jumlah Tanggungan</label>
        <input type="number" name="tanggungan_ortu" required>

        <label>Status Rumah</label>
        <select name="status_rumah" required>
          <option value="">-- Pilih --</option>
          <option value="Milik Sendiri">Milik Sendiri</option>
          <option value="Sewa">Sewa</option>
          <option value="Menumpang">Menumpang</option>
        </select>

        <label>Status Alumni</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="is_alumni" value="1"> Alumni Baitul Jihad</label>
        </div>

        <label>Fasilitas Rumah</label>
        <div class="checkbox-group">
          <label><input type="checkbox" name="ac" value="1"> AC</label>
          <label><input type="checkbox" name="tv" value="1"> TV</label>
          <label><input type="checkbox" name="kulkas" value="1"> Kulkas</label>
        </div>

        <label>Jumlah Motor</label>
        <input type="number" name="motor" min="0" required>

        <label>Jarak ke Sekolah (km)</label>
        <input type="number" name="jarak_km" step="0.1" min="0" required>

        <button type="submit" class="submit-btn">Simpan Data</button>
      </form>
    </div>
  </div>
</body>
</html>
