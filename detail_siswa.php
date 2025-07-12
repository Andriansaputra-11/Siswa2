<?php 
include 'inc/db.php';
session_start();

if (!isset($_GET['id'])) {
  echo "ID siswa tidak ditemukan.";
  exit();
}

$id_siswa = (int) $_GET['id'];

$siswa = $conn->query("SELECT * FROM siswa WHERE id = $id_siswa")->fetch_assoc();
$hasil = $conn->query("SELECT metode, nilai, ranking FROM hasil_seleksi WHERE id_siswa = $id_siswa ORDER BY nilai DESC LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Siswa - Yayasan Baitul Jihad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; padding: 0; background: linear-gradient(135deg, #dff1ff, #ffffff); min-height: 100vh; }
    .profile-container { max-width: 850px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); padding: 40px; position: relative; }
    .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
    .profile-header img { width: 90px; height: 90px; border-radius: 50%; margin-right: 25px; border: 3px solid #3498db; }
    .profile-header h2 { margin: 0; color: #2c3e50; }
    .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px 40px; }
    .profile-grid .field { margin-bottom: 15px; }
    .field label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
    .field span { color: #333; }
    .back-btn { margin-top: 30px; text-align: center; }
    .back-btn a { background-color: #3498db; color: white; padding: 12px 25px; border-radius: 6px; text-decoration: none; font-weight: bold; transition: background 0.3s; }
    .back-btn a:hover { background-color: #2980b9; }
    @media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } .profile-header { flex-direction: column; align-items: center; text-align: center; } .profile-header img { margin-bottom: 15px; } }
  </style>
</head>
<body>
<div class="profile-container">
  <div class="profile-header">
    <img src="https://ui-avatars.com/api/?name=<?= urlencode($siswa['nama']) ?>&background=3498db&color=fff" alt="Avatar">
    <h2><?= htmlspecialchars($siswa['nama']) ?></h2>
  </div>
  <div class="profile-grid">
    <div class="field"><label>NIK</label><span><?= $siswa['nik'] ?></span></div>
    <div class="field"><label>Jenjang</label><span><?= strtoupper($siswa['jenjang']) ?></span></div>
    <div class="field"><label>Asal Sekolah</label><span><?= $siswa['asal_sekolah'] ?></span></div>
    <div class="field"><label>Status Orang Tua</label><span><?= $siswa['status_ortu'] ?></span></div>
    <div class="field"><label>Penghasilan Orang Tua</label><span>Rp <?= number_format($siswa['penghasilan_ortu'], 0, ',', '.') ?></span></div>
    <div class="field"><label>Tanggungan Orang Tua</label><span><?= $siswa['tanggungan_ortu'] ?> orang</span></div>
    <div class="field"><label>Status Rumah</label><span><?= $siswa['status_rumah'] ?></span></div>
    <div class="field"><label>Alumni Sekolah Ini</label><span><?= $siswa['is_alumni'] ? 'Ya' : 'Tidak' ?></span></div>
    <div class="field"><label>Fasilitas Rumah</label><span><?= $siswa['ac'] ? 'AC, ' : '' ?><?= $siswa['tv'] ? 'TV, ' : '' ?><?= $siswa['kulkas'] ? 'Kulkas' : '' ?><?= (!$siswa['ac'] && !$siswa['tv'] && !$siswa['kulkas']) ? 'Tidak Ada' : '' ?></span></div>
    <div class="field"><label>Jumlah Motor</label><span><?= $siswa['motor'] ?> unit</span></div>
    <div class="field"><label>Jarak ke Sekolah</label><span><?= $siswa['jarak_km'] ?> km</span></div>
    <div class="field"><label>Nilai Akhir</label><span><?= $hasil ? number_format($hasil['nilai'], 4) : '-' ?></span></div>
    <div class="field"><label>Metode</label><span><?= $hasil ? $hasil['metode'] : '-' ?></span></div>
    <div class="field"><label>Ranking</label><span><?= $hasil && $hasil['ranking'] ? $hasil['ranking'] : '-' ?></span></div>
  </div>

<?php if ($hasil): ?>
  <?php
    $kategori = 'Tidak Layak';
    if ($hasil['nilai'] >= 0.80) $kategori = 'Layak Diterima';
    elseif ($hasil['nilai'] >= 0.65) $kategori = 'Layak Dipertimbangkan';
    elseif ($hasil['nilai'] >= 0.50) $kategori = 'Kurang Layak';

    $penghasilan = $siswa['penghasilan_ortu'];
    $tanggungan = $siswa['tanggungan_ortu'];
    $status_rumah = strtolower($siswa['status_rumah']) === 'kontrak' ? 1 : 0;
    $alumni = $siswa['is_alumni'];

    $bobot = [
      'penghasilan_ortu' => 0.3,
      'tanggungan_ortu'  => 0.2,
      'status_rumah'     => 0.3,
      'is_alumni'        => 0.2
    ];

    $nilai_penghasilan = 1 - min($penghasilan / 2000000, 1);
    $nilai_tanggungan  = min($tanggungan / 5, 1);
    $nilai_status_rumah = $status_rumah;
    $nilai_alumni = $alumni;

    $nilai_akhir_maut = 
      ($nilai_penghasilan * $bobot['penghasilan_ortu']) +
      ($nilai_tanggungan * $bobot['tanggungan_ortu']) +
      ($nilai_status_rumah * $bobot['status_rumah']) +
      ($nilai_alumni * $bobot['is_alumni']);
  ?>
  <div style="margin-top: 30px; padding: 25px; background: #f9f9f9; border-left: 5px solid #3498db; border-radius: 8px;">
    <h3>🧮 Penjelasan Hasil Seleksi</h3>
    <p>Metode <strong><?= $hasil['metode'] ?></strong>, Nilai akhir: <strong><?= number_format($hasil['nilai'], 4) ?></strong> — <strong><?= $kategori ?></strong></p>
    <?php if ($hasil['metode'] === 'MAUT'): ?>
      <h4>Rincian Perhitungan MAUT:</h4>
      <table style="width:100%; border-collapse:collapse;">
        <tr style="background:#e3f2fd;"><th>Kriteria</th><th>Nilai</th><th>Bobot</th><th>Nilai x Bobot</th></tr>
        <tr><td>Penghasilan</td><td><?= number_format($nilai_penghasilan, 4) ?></td><td>0.3</td><td><?= number_format($nilai_penghasilan * 0.3, 4) ?></td></tr>
        <tr><td>Tanggungan</td><td><?= number_format($nilai_tanggungan, 4) ?></td><td>0.2</td><td><?= number_format($nilai_tanggungan * 0.2, 4) ?></td></tr>
        <tr><td>Status Rumah</td><td><?= $nilai_status_rumah ?></td><td>0.3</td><td><?= number_format($nilai_status_rumah * 0.3, 4) ?></td></tr>
        <tr><td>Alumni</td><td><?= $nilai_alumni ?></td><td>0.2</td><td><?= number_format($nilai_alumni * 0.2, 4) ?></td></tr>
        <tr style="font-weight:bold;"><td colspan="3">Total</td><td><?= number_format($nilai_akhir_maut, 4) ?></td></tr>
      </table>
    <?php elseif ($hasil['metode'] === 'TOPSIS'): ?>
      <h4>Penjelasan dan Rincian Perhitungan TOPSIS:</h4>
      <ul>
        <li>Setiap kriteria dinormalisasi dan dikalikan bobot.</li>
        <li>Dihitung jarak terhadap solusi ideal positif dan negatif.</li>
        <li>Preferensi dihitung: <code>nilai = D⁻ / (D⁺ + D⁻)</code>.</li>
        <li>Semakin mendekati 1, semakin layak diterima.</li>
      </ul>
    <?php endif; ?>
    <p style="font-style: italic; color: #555;">* Penjelasan disederhanakan agar mudah dimengerti semua kalangan.</p>
  </div>
<?php endif; ?>

<div class="back-btn">
  <a href="hasil.php">⬅ Kembali ke Hasil Seleksi</a>
</div>
</div>
</body>
</html>
