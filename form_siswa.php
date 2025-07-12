<?php
include 'inc/db.php';

$edit = false;
$siswa = [
  'nama' => '', 'nik' => '', 'asal_sekolah' => '',
  'penghasilan_ortu' => '', 'tanggungan_ortu' => '',
  'status_rumah' => '', 'status_ortu' => '',
  'is_alumni' => 0, 'punya_ac' => 0, 'punya_tv' => 0, 'punya_kulkas' => 0,
  'jumlah_motor' => '', 'jarak_kesekolah' => ''
];

if (isset($_GET['id'])) {
  $edit = true;
  $id = $_GET['id'];
  $query = $conn->query("SELECT * FROM siswa WHERE id = $id");
  if ($query->num_rows > 0) {
    $siswa = $query->fetch_assoc();
  } else {
    echo "Data tidak ditemukan";
    exit;
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama             = $_POST['nama'];
  $nik              = $_POST['nik'];
  $asal_sekolah     = $_POST['asal_sekolah'];
  $penghasilan_ortu = $_POST['penghasilan_ortu'];
  $tanggungan_ortu  = $_POST['tanggungan_ortu'];
  $status_rumah     = $_POST['status_rumah'];
  $status_ortu      = $_POST['status_ortu'];
  $is_alumni        = isset($_POST['is_alumni']) ? 1 : 0;
  $punya_ac         = isset($_POST['punya_ac']) ? 1 : 0;
  $punya_tv         = isset($_POST['punya_tv']) ? 1 : 0;
  $punya_kulkas     = isset($_POST['punya_kulkas']) ? 1 : 0;
  $jumlah_motor     = $_POST['jumlah_motor'];
  $jarak_kesekolah  = $_POST['jarak_kesekolah'];

  if ($edit) {
    $sql = "UPDATE siswa SET
      nama='$nama', nik='$nik', asal_sekolah='$asal_sekolah',
      penghasilan_ortu='$penghasilan_ortu', tanggungan_ortu='$tanggungan_ortu',
      status_rumah='$status_rumah', status_ortu='$status_ortu',
      is_alumni='$is_alumni', punya_ac='$punya_ac', punya_tv='$punya_tv',
      punya_kulkas='$punya_kulkas', jumlah_motor='$jumlah_motor',
      jarak_kesekolah='$jarak_kesekolah'
      WHERE id=$id";
  } else {
    $sql = "INSERT INTO siswa (nama, nik, asal_sekolah, penghasilan_ortu, tanggungan_ortu, status_rumah, status_ortu, is_alumni, punya_ac, punya_tv, punya_kulkas, jumlah_motor, jarak_kesekolah)
      VALUES ('$nama', '$nik', '$asal_sekolah', '$penghasilan_ortu', '$tanggungan_ortu', '$status_rumah', '$status_ortu', '$is_alumni', '$punya_ac', '$punya_tv', '$punya_kulkas', '$jumlah_motor', '$jarak_kesekolah')";
  }

  if ($conn->query($sql)) {
    echo "<script>alert('Data berhasil disimpan'); window.location.href='siswa.php';</script>";
  } else {
    echo "Gagal menyimpan data: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $edit ? 'Edit' : 'Tambah' ?> Data Siswa</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; padding: 0; margin: 0; }
    .form-container {
      max-width: 700px; margin: 40px auto; background: white;
      padding: 30px; border-radius: 12px; box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; color: #333; margin-bottom: 20px; }
    label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
    input[type="text"], input[type="number"], select {
      width: 100%; padding: 10px; border: 1px solid #ccc;
      border-radius: 6px; margin-top: 5px;
    }
    .checkbox-group { margin-top: 10px; }
    .checkbox-group label { font-weight: normal; margin-right: 15px; }
    .btn {
      margin-top: 25px; padding: 12px; border: none;
      border-radius: 6px; font-size: 16px; cursor: pointer;
    }
    .submit-btn { background-color: #2e86de; color: white; width: 100%; }
    .submit-btn:hover { background-color: #1e5fa1; }
    .back-btn {
      background: #ccc; color: #333;
      text-decoration: none; display: inline-block;
      padding: 10px 16px; border-radius: 6px; margin-top: 15px;
    }
  </style>
  <script>
    function validateForm() {
      const requiredFields = ['nama', 'nik', 'asal_sekolah', 'penghasilan_ortu', 'tanggungan_ortu', 'status_ortu', 'jumlah_motor', 'jarak_kesekolah'];
      for (let field of requiredFields) {
        let value = document.forms["formSiswa"][field].value;
        if (value == "") {
          alert("Kolom " + field.replace("_", " ") + " wajib diisi!");
          return false;
        }
      }
      return true;
    }
  </script>
</head>
<body>

  <div class="form-container">
    <h2><?= $edit ? 'Edit' : 'Tambah' ?> Data Siswa</h2>
    <form name="formSiswa" method="POST" onsubmit="return validateForm();">
      <label>Nama Lengkap</label>
      <input type="text" name="nama" value="<?= $siswa['nama'] ?>">

      <label>NIK</label>
      <input type="text" name="nik" value="<?= $siswa['nik'] ?>">

      <label>Asal Sekolah</label>
      <input type="text" name="asal_sekolah" value="<?= $siswa['asal_sekolah'] ?>">

      <label>Penghasilan Orang Tua (Rp)</label>
      <input type="number" name="penghasilan_ortu" value="<?= $siswa['penghasilan_ortu'] ?>">

      <label>Jumlah Tanggungan Orang Tua</label>
      <input type="number" name="tanggungan_ortu" value="<?= $siswa['tanggungan_ortu'] ?>">

      <label>Status Orang Tua</label>
      <select name="status_ortu">
        <option <?= $siswa['status_ortu'] == 'Lengkap' ? 'selected' : '' ?> value="Lengkap">Lengkap</option>
        <option <?= $siswa['status_ortu'] == 'Yatim' ? 'selected' : '' ?> value="Yatim">Yatim</option>
        <option <?= $siswa['status_ortu'] == 'Yatim Piatu' ? 'selected' : '' ?> value="Yatim Piatu">Yatim Piatu</option>
      </select>

      <label>Status Rumah</label>
      <select name="status_rumah">
        <option <?= $siswa['status_rumah'] == 'Milik Sendiri' ? 'selected' : '' ?> value="Milik Sendiri">Milik Sendiri</option>
        <option <?= $siswa['status_rumah'] == 'Sewa' ? 'selected' : '' ?> value="Sewa">Sewa</option>
        <option <?= $siswa['status_rumah'] == 'Menumpang' ? 'selected' : '' ?> value="Menumpang">Menumpang</option>
      </select>

      <label>Status Alumni</label>
      <div class="checkbox-group">
        <input type="checkbox" name="is_alumni" value="1" <?= $siswa['is_alumni'] ? 'checked' : '' ?>> <label>Alumni Baitul Jihad</label>
      </div>

      <label>Fasilitas Rumah</label>
      <div class="checkbox-group">
        <input type="checkbox" name="punya_ac" value="1" <?= $siswa['punya_ac'] ? 'checked' : '' ?>> <label>AC</label>
        <input type="checkbox" name="punya_tv" value="1" <?= $siswa['punya_tv'] ? 'checked' : '' ?>> <label>TV</label>
        <input type="checkbox" name="punya_kulkas" value="1" <?= $siswa['punya_kulkas'] ? 'checked' : '' ?>> <label>Kulkas</label>
      </div>

      <label>Jumlah Motor</label>
      <input type="number" name="jumlah_motor" min="0" value="<?= $siswa['jumlah_motor'] ?>">

      <label>Jarak ke Sekolah (km)</label>
      <input type="number" step="0.1" name="jarak_kesekolah" value="<?= $siswa['jarak_kesekolah'] ?>">

      <button class="btn submit-btn" type="submit"><?= $edit ? 'Update' : 'Simpan' ?> Data</button>
    </form>

    <a href="siswa.php" class="back-btn">← Kembali ke Data Siswa</a>
  </div>

</body>
</html>
