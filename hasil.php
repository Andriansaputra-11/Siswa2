<?php
include 'inc/db.php';
session_start();

function getKategori($nilai) {
  if ($nilai >= 0.80) return "Layak Diterima";
  elseif ($nilai >= 0.65) return "Layak Dipertimbangkan";
  elseif ($nilai >= 0.50) return "Kurang Layak";
  else return "Tidak Layak";
}

$jenjang = isset($_GET['jenjang']) ? strtoupper($_GET['jenjang']) : 'ALL';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Seleksi - Yayasan Baitul Jihad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 20px;
    }
    .page-title {
      text-align: center;
      margin-bottom: 20px;
    }
    .page-title h1 {
      margin: 0;
      font-size: 28px;
      color: #2c3e50;
    }
    .page-title p {
      margin: 5px 0 0;
      font-size: 16px;
      color: #7f8c8d;
    }

    .filters {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 10px;
    }
    .filters a {
      padding: 8px 15px;
      background: #3498db;
      color: white;
      border-radius: 20px;
      text-decoration: none;
      font-size: 13px;
    }
    .filters a.active {
      background: #2c3e50;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    th {
      background-color: #2c3e50;
      color: white;
    }

    tr.layak_diterima { background-color: #d0f5d8; }
    tr.layak_dipertimbangkan { background-color: #fff9c4; }
    tr.kurang_layak { background-color: #ffe0b2; }
    tr.tidak_layak { background-color: #ffcdd2; }

    .back-btn {
      display: block;
      margin: 20px auto 0;
      padding: 10px 20px;
      background: #34495e;
      color: white;
      text-align: center;
      text-decoration: none;
      border-radius: 5px;
      width: fit-content;
    }

    .summary {
      margin-top: 40px;
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .card-summary {
      background: white;
      padding: 20px;
      border-left: 6px solid #ccc;
      border-radius: 8px;
      text-align: center;
      width: 200px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card-summary h3 {
      margin: 0;
      font-size: 15px;
      color: #333;
    }
    .card-summary .value {
      font-size: 28px;
      font-weight: bold;
      color: #2c3e50;
      margin-top: 10px;
    }

    .diterima { border-color: #27ae60; }
    .dipertimbangkan { border-color: #f1c40f; }
    .kurang { border-color: #e67e22; }
    .tidak { border-color: #e74c3c; }
  </style>
</head>
<body>

<div class="page-title">
  <h1><i class="fas fa-user-graduate"></i> Hasil Seleksi <?= htmlspecialchars($jenjang) ?></h1>
  <p>Yayasan Baitul Jihad</p>
</div>

<div class="filters">
  <a href="?jenjang=ALL" class="<?= $jenjang == 'ALL' ? 'active' : '' ?>">Semua Jenjang</a>
  <a href="?jenjang=SD" class="<?= $jenjang == 'SD' ? 'active' : '' ?>">SD</a>
  <a href="?jenjang=SMP" class="<?= $jenjang == 'SMP' ? 'active' : '' ?>">SMP</a>
</div>

<?php
$where = "h.nilai IS NOT NULL";
if ($jenjang != 'ALL') $where .= " AND s.jenjang = '$jenjang'";

$result = $conn->query("
  SELECT s.id, s.nama, s.is_alumni, s.jenjang, h.nilai, h.metode
  FROM hasil_seleksi h
  JOIN siswa s ON h.id_siswa = s.id
  WHERE $where
  ORDER BY h.nilai DESC
");

$data_tertinggi = [];
while ($r = $result->fetch_assoc()) {
  $id = $r['id'];
  if (!isset($data_tertinggi[$id]) || $r['nilai'] > $data_tertinggi[$id]['nilai']) {
    $data_tertinggi[$id] = $r;
  }
}

usort($data_tertinggi, function ($a, $b) {
  return $b['nilai'] <=> $a['nilai'];
});

$rank = 1;
$count_diterima = $count_dipertimbangkan = $count_kurang = $count_tidak = 0;
?>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <?php if ($jenjang == 'ALL'): ?><th>Jenjang</th><?php endif; ?>
      <th>Alumni</th>
      <th>Nilai</th>
      <th>Kategori</th>
      <th>Metode</th>
      <th>Detail</th>
    </tr>
  </thead>
  <tbody>
<?php foreach ($data_tertinggi as $r):
  $nilai = $r['nilai'];
  $kategori = getKategori($nilai);
  $class = strtolower(str_replace(' ', '_', $kategori));

  if ($kategori == "Layak Diterima") $count_diterima++;
  elseif ($kategori == "Layak Dipertimbangkan") $count_dipertimbangkan++;
  elseif ($kategori == "Kurang Layak") $count_kurang++;
  else $count_tidak++;
?>
  <tr class="<?= $class ?>">
    <td><?= $rank++ ?></td>
    <td><?= htmlspecialchars($r['nama']) ?></td>
    <?php if ($jenjang == 'ALL'): ?><td><?= $r['jenjang'] ?></td><?php endif; ?>
    <td><?= $r['is_alumni'] ? 'Ya' : 'Tidak' ?></td>
    <td><?= number_format($r['nilai'], 4) ?></td>
    <td><?= $kategori ?></td>
    <td><?= $r['metode'] ?></td>
    <td><a href="detail_siswa.php?id=<?= $r['id'] ?>" title="Lihat Detail"><i class="fas fa-eye"></i></a></td>
  </tr>
<?php endforeach; ?>
  </tbody>
</table>

<div class="summary">
  <div class="card-summary diterima">
    <h3>Layak Diterima</h3>
    <div class="value"><?= $count_diterima ?></div>
  </div>
  <div class="card-summary dipertimbangkan">
    <h3>Dipertimbangkan</h3>
    <div class="value"><?= $count_dipertimbangkan ?></div>
  </div>
  <div class="card-summary kurang">
    <h3>Kurang Layak</h3>
    <div class="value"><?= $count_kurang ?></div>
  </div>
  <div class="card-summary tidak">
    <h3>Tidak Layak</h3>
    <div class="value"><?= $count_tidak ?></div>
  </div>
</div>

<a href="dashboard_admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>

</body>
</html>
