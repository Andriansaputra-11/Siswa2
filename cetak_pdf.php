<?php
require('vendor/autoload.php'); // pastikan mPDF sudah terinstall
include 'inc/db.php';

$mpdf = new \Mpdf\Mpdf();
$html = '<h2>Hasil Seleksi Calon Siswa</h2>
<table border="1" cellpadding="10" cellspacing="0" width="100%">
<tr><th>Ranking</th><th>Nama</th><th>Nilai</th><th>Status</th><th>Metode</th></tr>';

$query = $conn->query("SELECT s.nama, h.nilai, h.metode FROM hasil_seleksi h JOIN siswa s ON h.id_siswa = s.id ORDER BY h.nilai DESC");
$rank = 1;
$kuota = 10;
while ($r = $query->fetch_assoc()) {
  $status = ($rank <= $kuota) ? "DITERIMA" : "TIDAK DITERIMA";
  $html .= "<tr>
    <td>{$rank}</td>
    <td>{$r['nama']}</td>
    <td>{$r['nilai']}</td>
    <td>{$status}</td>
    <td>{$r['metode']}</td>
  </tr>";
  $rank++;
}
$html .= '</table>';
$mpdf->WriteHTML($html);
$mpdf->Output('hasil-seleksi.pdf', 'I');
?>
