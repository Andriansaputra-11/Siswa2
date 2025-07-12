<?php 
include 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  extract($_POST);

  // Handle checkbox alumni agar tidak error saat tidak dicentang
  $alumni = isset($alumni) ? 1 : 0;

  // Simpan data termasuk status_ortu
  $conn->query("INSERT INTO siswa (
    nama, nik, asal_sekolah, penghasilan_ortu, tanggungan_ortu, status_ortu, status_rumah, is_alumni
  ) VALUES (
    '$nama', '$nik', '$asal', $penghasilan, $tanggungan, '$status_ortu', '$status', $alumni
  )");

  echo "✅ Pendaftaran berhasil!";
}
?>

<!-- FORM -->
<form method="post">
  <h2>Form Pendaftaran Siswa Tidak Mampu</h2>

  <input name="nama" placeholder="Nama Lengkap"><br>
  <input name="nik" placeholder="NIK"><br>
  <input name="asal" placeholder="Asal Sekolah"><br>
  <input name="penghasilan" placeholder="Penghasilan Orang Tua" type="number"><br>
  <input name="tanggungan" placeholder="Jumlah Tanggungan" type="number"><br>

  <label>Status Orang Tua:</label><br>
  <select name="status_ortu" required>
    <option value="">-- Pilih --</option>
    <option value="Lengkap">Lengkap</option>
    <option value="Yatim">Yatim</option>
    <option value="Yatim Piatu">Yatim Piatu</option>
  </select><br><br>

  <label>Status Rumah:</label><br>
  <select name="status">
    <option value="Sewa">Sewa</option>
    <option value="Milik Sendiri">Milik Sendiri</option>
  </select><br><br>

  Alumni? <input type="checkbox" name="alumni" value="1"><br><br>

  <button type="submit">Daftar</button>
</form>
