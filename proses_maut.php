<?php
include 'inc/db.php';

// Ambil jenjang dari parameter URL
$jenjang = isset($_GET['jenjang']) ? strtoupper($_GET['jenjang']) : 'ALL';

// Ambil bobot kriteria
$bobot = [
  'penghasilan_ortu' => 0.20,
  'tanggungan_ortu'  => 0.15,
  'status_rumah'     => 0.15,
  'is_alumni'        => 0.15,
  'motor'            => 0.10,
  'jarak_km'         => 0.15,
  'status_ortu'      => 0.10
];

// Ambil data siswa sesuai jenjang
$data = [];
$seen = [];
$whereJenjang = ($jenjang != 'ALL') ? "WHERE jenjang = '$jenjang'" : '';
$res = $conn->query("SELECT * FROM siswa $whereJenjang");
while ($row = $res->fetch_assoc()) {
  if (!isset($seen[$row['nik']])) {
    $data[] = $row;
    $seen[$row['nik']] = true;
  }
}

// Hitung nilai maksimal
$max = ['penghasilan_ortu'=>0, 'tanggungan_ortu'=>0, 'motor'=>0, 'jarak_km'=>0];
foreach ($data as $d) {
  $max['penghasilan_ortu'] = max($max['penghasilan_ortu'], $d['penghasilan_ortu']);
  $max['tanggungan_ortu']  = max($max['tanggungan_ortu'], $d['tanggungan_ortu']);
  $max['motor']            = max($max['motor'], $d['motor']);
  $max['jarak_km']         = max($max['jarak_km'], $d['jarak_km']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Perhitungan MAUT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: #f4f6f8;
      font-family: 'Segoe UI', sans-serif;
      color: #333;
    }
    .container { max-width: 1100px; margin: 40px auto; }
    .table { background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    thead.table-dark { background-color: #2c3e50; color: #fff; }
    .table th, .table td { vertical-align: middle; text-align: center; }
    .badge-soft { padding: 5px 10px; border-radius: 20px; font-size: 13px; font-weight: 500; }
    .badge-soft-success { background-color: #e9f9ec; color: #2e7d32; }
    .badge-soft-warning { background-color: #fff7e6; color: #9c6f00; }
    .badge-soft-secondary { background-color: #f0f0f0; color: #666; }
    .badge-soft-danger { background-color: #fcebea; color: #c0392b; }
    .badge-alumni { background-color: #dceeff; color: #1b94e4; border-radius: 20px; padding: 5px 10px; font-size: 13px; font-weight: bold; }
    .highlight { background-color: #f5fff9 !important; }
    .chart-container { margin: 50px auto; }
    .back-btn { display: block; width: fit-content; margin: 30px auto 0; padding: 10px 20px; background: #34495e; color: white; text-decoration: none; border-radius: 5px; }
    .filters { text-align: center; margin-bottom: 20px; }
    .filters a {
      display: inline-block; margin: 5px; padding: 7px 14px; background: #3498db;
      color: white; text-decoration: none; border-radius: 20px; font-size: 13px; transition: 0.2s;
    }
    .filters a:hover, .filters a.active { background: #2c3e50; }
    
    .explanation {
      font-size: 15px;
      background: #eef2f3;
      border-left: 4px solid #007BFF;
      padding: 15px;
      border-radius: 6px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
<div class="container">
  <h2 class="text-center mb-4">📊 Hasil Perhitungan & Proses Metode MAUT</h2>
  <div class="filters">
    <a href="?jenjang=ALL" class="<?= $jenjang == 'ALL' ? 'active' : '' ?>">Semua Jenjang</a>
    <a href="?jenjang=SD" class="<?= $jenjang == 'SD' ? 'active' : '' ?>">SD</a>
    <a href="?jenjang=SMP" class="<?= $jenjang == 'SMP' ? 'active' : '' ?>">SMP</a>
  </div>

  <div class="table-responsive mb-5">
    <table class="table table-bordered table-striped align-middle table-sm">
      <thead class="table-dark">
        <tr>
          <th>No.</th>
          <th>Nama</th>
          <th>Nilai</th>
          <th>Kategori</th>
          <th>Alumni</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $seen_nama = [];
        $no = 1;
        $ranking = $conn->query("SELECT hs.*, s.nama, s.is_alumni FROM hasil_seleksi hs JOIN siswa s ON s.id=hs.id_siswa WHERE metode='MAUT' AND nilai > 0" . ($jenjang != 'ALL' ? " AND s.jenjang='$jenjang'" : "") . " ORDER BY nilai DESC");
        while ($r = $ranking->fetch_assoc()):
          if (isset($seen_nama[$r['nama']])) continue;
          $seen_nama[$r['nama']] = true;

          $kategori = ($r['nilai'] >= 0.80) ? 'Layak Diterima' :
                      (($r['nilai'] >= 0.65) ? 'Layak Dipertimbangkan' :
                      (($r['nilai'] >= 0.50) ? 'Kurang Layak' : 'Tidak Layak'));
        ?>
        <tr class="<?= $no <= 15 ? 'highlight' : '' ?>">
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($r['nama']) ?></td>
          <td><?= number_format($r['nilai'], 4) ?></td>
          <td>
            <span class="badge-soft 
              <?= $kategori == 'Layak Diterima' ? 'badge-soft-success' :
                ($kategori == 'Layak Dipertimbangkan' ? 'badge-soft-warning' :
                ($kategori == 'Kurang Layak' ? 'badge-soft-secondary' : 'badge-soft-danger')) ?>">
              <?= $kategori ?>
            </span>
          </td>
          <td>
            <?= $r['is_alumni'] ? '<span class="badge-alumni">Alumni</span>' : '<span class="badge-soft badge-soft-secondary">Non-Alumni</span>' ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Tabel Perhitungan Detail -->
  <h4 class="mb-3">📊 Proses Perhitungan Detail MAUT:</h4>
  <div class="table-responsive mb-5">
    <table class="table table-bordered table-sm">
      <thead class="table-light">
        <tr>
          <th>Nama</th>
          <th>Penghasilan (<?= $bobot['penghasilan_ortu'] ?>)</th>
          <th>Tanggungan (<?= $bobot['tanggungan_ortu'] ?>)</th>
          <th>Status Rumah (<?= $bobot['status_rumah'] ?>)</th>
          <th>Alumni (<?= $bobot['is_alumni'] ?>)</th>
          <th>Motor (<?= $bobot['motor'] ?>)</th>
          <th>Jarak (<?= $bobot['jarak_km'] ?>)</th>
          <th>Status Ortu (<?= $bobot['status_ortu'] ?>)</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $chart_nama = [];
        $chart_nilai = [];
        foreach ($data as $d):
          $penghasilan = 1 - ($d['penghasilan_ortu'] / ($max['penghasilan_ortu'] ?: 1));
          $tanggungan  = $d['tanggungan_ortu'] / ($max['tanggungan_ortu'] ?: 1);
          $rumah       = ($d['status_rumah'] == 'Sewa') ? 1 : 0.5;
          $alumni      = $d['is_alumni'] ? 1 : 0;
          $motor       = 1 - ($d['motor'] / ($max['motor'] ?: 1));
          $jarak       = 1 - ($d['jarak_km'] / ($max['jarak_km'] ?: 1));
          $status_ortu = match ($d['status_ortu']) {
            'Yatim Piatu' => 1,
            'Yatim'       => 0.5,
            default       => 0
          };

          $total = round(
            ($penghasilan * $bobot['penghasilan_ortu']) +
            ($tanggungan  * $bobot['tanggungan_ortu']) +
            ($rumah       * $bobot['status_rumah']) +
            ($alumni      * $bobot['is_alumni']) +
            ($motor       * $bobot['motor']) +
            ($jarak       * $bobot['jarak_km']) +
            ($status_ortu * $bobot['status_ortu']),
            4
          );
          if ($total == 0) continue;
          $chart_nama[] = $d['nama'];
          $chart_nilai[] = $total;
        ?>
        <tr>
          <td><?= htmlspecialchars($d['nama']) ?></td>
          <td><?= round($penghasilan, 4) ?></td>
          <td><?= round($tanggungan, 4) ?></td>
          <td><?= $rumah ?></td>
          <td><?= $alumni ?></td>
          <td><?= round($motor, 4) ?></td>
          <td><?= round($jarak, 4) ?></td>
          <td><?= $status_ortu ?></td>
          <td><strong><?= $total ?></strong></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Grafik -->
<div class="chart-container">
  <h5 class="text-center mb-3">📈 Grafik Nilai MAUT</h5>
  <canvas id="chartNilai"></canvas>
</div>

<!-- Penjelasan dipindah ke bawah -->
<div class="explanation mt-5">
  <strong>Penjelasan:</strong> Metode <strong>MAUT (Multi Attribute Utility Theory)</strong> adalah metode pengambilan keputusan multikriteria yang digunakan untuk menentukan pilihan terbaik berdasarkan sejumlah atribut (kriteria). Dalam konteks ini, MAUT digunakan untuk membantu memilih siswa yang paling layak menerima bantuan pendidikan dengan mempertimbangkan faktor-faktor seperti penghasilan orang tua, tanggungan, status rumah, kepemilikan motor, jarak ke sekolah, status alumni, dan status orang tua (yatim/piatu). Setiap kriteria diberikan bobot sesuai tingkat kepentingannya, kemudian dihitung skor akhir untuk menentukan kelayakan siswa.
</div>
<a href="dashboard_admin.php" class="back-btn">⬅️ Kembali ke Dashboard</a>
</div>

<script>
const ctx = document.getElementById('chartNilai').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($chart_nama) ?>,
    datasets: [{
      label: 'Nilai MAUT',
      data: <?= json_encode($chart_nilai) ?>,
      backgroundColor: '#36A2EB'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, title: { display: true, text: 'Nilai MAUT' } }
    }
  }
});
</script>
</body>
</html>
