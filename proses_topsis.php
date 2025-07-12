<?php
include 'inc/db.php';

// Filter jenjang dari URL
$jenjang = isset($_GET['jenjang']) ? strtoupper($_GET['jenjang']) : 'ALL';

// Ambil data siswa sesuai jenjang
$where = ($jenjang != 'ALL') ? "WHERE jenjang = '$jenjang'" : "";
$data = [];
$res = $conn->query("SELECT * FROM siswa $where");
while ($row = $res->fetch_assoc()) $data[] = $row;

// Bobot
$bobot = [
  'penghasilan_ortu'=>0.25,
  'tanggungan_ortu'=>0.15,
  'status_rumah'=>0.20,
  'is_alumni'=>0.15,
  'status_ortu'=>0.25
];

// Matriks awal
$matriks = [];
foreach ($data as $d) {
  $penghasilan = 1 - ($d['penghasilan_ortu'] / 2000000);
  $tanggungan = $d['tanggungan_ortu'] / 5;
  $rumah = ($d['status_rumah'] == 'Sewa') ? 1 : 0.5;
  $alumni = $d['is_alumni'] ? 1 : 0;
  $status_ortu = match ($d['status_ortu']) {
    'Yatim Piatu' => 1,
    'Yatim'       => 0.5,
    default       => 0
  };
  $matriks[] = [
    'id' => $d['id'], 'nama' => $d['nama'],
    'penghasilan_ortu' => $penghasilan,
    'tanggungan_ortu' => $tanggungan,
    'status_rumah' => $rumah,
    'is_alumni' => $alumni,
    'status_ortu' => $status_ortu
  ];
}

// Normalisasi
$div = [];
foreach ($bobot as $k => $v) {
  $sum = 0;
  foreach ($matriks as $m) $sum += pow($m[$k], 2);
  $div[$k] = sqrt($sum);
}
foreach ($matriks as &$m) {
  foreach ($bobot as $k => $v) {
    $m["norm_$k"] = $m[$k] / $div[$k];
    $m["bobot_$k"] = $m["norm_$k"] * $v;
  }
}

// Ideal
$ideal_pos = $ideal_neg = [];
foreach ($bobot as $k => $v) {
  $arr = array_column($matriks, "bobot_$k");
  $ideal_pos[$k] = max($arr);
  $ideal_neg[$k] = min($arr);
}

// Hitung preferensi
$conn->query("DELETE FROM hasil_seleksi WHERE metode='TOPSIS' AND id_siswa IN (SELECT id FROM siswa $where)");
foreach ($matriks as &$m) {
  $d_pos = $d_neg = 0;
  foreach ($bobot as $k => $v) {
    $d_pos += pow($m["bobot_$k"] - $ideal_pos[$k], 2);
    $d_neg += pow($m["bobot_$k"] - $ideal_neg[$k], 2);
  }
  $m['d_pos'] = sqrt($d_pos);
  $m['d_neg'] = sqrt($d_neg);
  $m['preferensi'] = round($m['d_neg'] / ($m['d_pos'] + $m['d_neg']), 4);
  $conn->query("INSERT INTO hasil_seleksi (id_siswa, metode, nilai) 
                VALUES ({$m['id']}, 'TOPSIS', {$m['preferensi']})");
}

// Ambil hasil akhir
$hasil = $conn->query("
  SELECT hs.id_siswa, s.nama, s.is_alumni, s.jenjang, hs.nilai
  FROM hasil_seleksi hs
  JOIN siswa s ON s.id = hs.id_siswa
  WHERE hs.metode='TOPSIS' AND hs.nilai > 0 " . ($jenjang != 'ALL' ? "AND s.jenjang = '$jenjang'" : "") . "
  ORDER BY hs.nilai DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil TOPSIS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .container { max-width: 1200px; margin: 40px auto; }
    h2, h4, h5 { text-align: center; margin: 30px 0; }
    .filters { text-align: center; margin-bottom: 30px; }
    .filters a {
      padding: 8px 15px; margin: 0 5px;
      background: #3498db; color: white;
      border-radius: 20px; text-decoration: none;
      font-size: 14px;
    }
    .filters a.active { background: #2c3e50; }
    .section-title {
      font-size: 18px; font-weight: bold; background: #dfe6e9;
      padding: 10px 20px; border-left: 5px solid #2980b9;
      margin-top: 40px; margin-bottom: 20px;
    }
    .table thead { background-color: #2c3e50; color: white; }
    .badge-soft-success { background: #e9f9ec; color: #2e7d32; }
    .badge-soft-warning { background: #fff7e6; color: #9c6f00; }
    .badge-soft-secondary { background: #f0f0f0; color: #666; }
    .badge-soft-danger { background: #fcebea; color: #c0392b; }
    .badge-alumni {
      background-color: #dceeff;
      color: #2874a6;
      border-radius: 20px;
      padding: 5px 10px;
      font-size: 13px;
    }
    .explanation {
      font-size: 15px;
      background: #eef2f3;
      border-left: 4px solid #007BFF;
      padding: 15px;
      border-radius: 6px;
      margin-top: 30px;
    }
    .back-btn { 
      display: block; 
      width: fit-content; 
      margin: 30px auto 0; 
      padding: 10px 20px; 
      background: #34495e; 
      color: white; 
      text-decoration: none; 
      border-radius: 5px; }
  </style>
</head>
<body>
<div class="container">
  <h2>📊 Hasil Perhitungan & Proses Metode TOPSIS</h2>

  <!-- Filter Jenjang -->
  <div class="filters">
    <a href="?jenjang=ALL" class="<?= $jenjang == 'ALL' ? 'active' : '' ?>">Semua Jenjang</a>
    <a href="?jenjang=SD" class="<?= $jenjang == 'SD' ? 'active' : '' ?>">SD</a>
    <a href="?jenjang=SMP" class="<?= $jenjang == 'SMP' ? 'active' : '' ?>">SMP</a>
  </div>

  <!-- Hasil Akhir -->
  <div class="section-title">🏁 Hasil Akhir Seleksi</div>
  <table class="table table-bordered text-center align-middle">
    <thead>
      <tr>
        <th>No</th><th>Nama</th><th>Nilai</th><th>Kategori</th><th>Alumni</th>
      </tr>
    </thead>
    <tbody>
      <?php $no = 1; while ($r = $hasil->fetch_assoc()):
        if ($r['nilai'] >= 0.80) { $kategori = 'Layak Diterima'; $badge = 'badge-soft-success'; }
        elseif ($r['nilai'] >= 0.65) { $kategori = 'Layak Dipertimbangkan'; $badge = 'badge-soft-warning'; }
        elseif ($r['nilai'] >= 0.50) { $kategori = 'Kurang Layak'; $badge = 'badge-soft-secondary'; }
        else { $kategori = 'Tidak Layak'; $badge = 'badge-soft-danger'; }
      ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($r['nama']) ?></td>
        <td><?= number_format($r['nilai'], 4) ?></td>
        <td><span class="badge <?= $badge ?>"><?= $kategori ?></span></td>
        <td><?= $r['is_alumni'] ? '<span class="badge-alumni">Alumni</span>' : '<span class="badge badge-soft-secondary">Non-Alumni</span>' ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <!-- Proses Perhitungan -->
  <div class="section-title">🔧 Proses Perhitungan TOPSIS</div>
  <table class="table table-bordered text-center align-middle table-sm">
    <thead class="table-light">
      <tr>
        <th>Nama</th>
        <?php foreach ($bobot as $k => $v): ?>
          <th><?= ucwords(str_replace('_', ' ', $k)) ?></th>
        <?php endforeach; ?>
        <th>D⁺</th><th>D⁻</th><th>Preferensi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($matriks as $m): ?>
        <tr>
          <td><?= htmlspecialchars($m['nama']) ?></td>
          <?php foreach ($bobot as $k => $v): ?>
            <td><?= round($m["bobot_$k"], 4) ?></td>
          <?php endforeach; ?>
          <td><?= round($m['d_pos'], 4) ?></td>
          <td><?= round($m['d_neg'], 4) ?></td>
          <td><strong><?= $m['preferensi'] ?></strong></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Grafik -->
  <div class="section-title">📊 Grafik Nilai Preferensi</div>
  <canvas id="chartTopsis"></canvas>

  <!-- Penjelasan -->
  <div class="explanation">
    <strong>Penjelasan:</strong> Metode <strong>TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)</strong> adalah metode pengambilan keputusan multikriteria yang mempertimbangkan jarak alternatif terhadap solusi ideal positif (terbaik) dan negatif (terburuk). Semakin dekat alternatif ke solusi ideal positif dan semakin jauh dari solusi negatif, maka semakin tinggi nilainya. Dalam konteks ini, siswa yang memiliki kondisi sosial-ekonomi yang lebih layak akan mendapatkan nilai preferensi yang lebih tinggi.
  </div>

  <a href="dashboard_admin.php" class="back-btn">⬅ Kembali ke Dashboard</a>
</div>

<script>
const ctx = document.getElementById('chartTopsis').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($matriks, 'nama')) ?>,
    datasets: [{
      label: 'Nilai Preferensi',
      data: <?= json_encode(array_column($matriks, 'preferensi')) ?>,
      backgroundColor: '#2980b9'
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => `Preferensi: ${ctx.raw}`
        }
      }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>
</body>
</html>
