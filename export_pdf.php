<?php
require_once __DIR__ . '/vendor/autoload.php';
include 'inc/db.php';
$mpdf = new \Mpdf\Mpdf();
$html = "<h2>Hasil Seleksi</h2><table border='1'><tr><th>Nama</th><th>Nilai</th><th>Metode</th></tr>";
$res = $conn->query("SELECT s.nama, h.nilai, h.metode FROM hasil_seleksi h JOIN siswa s ON h.id_siswa=s.id");
while($r = $res->fetch_assoc()) {
  $html .= "<tr><td>{$r['nama']}</td><td>{$r['nilai']}</td><td>{$r['metode']}</td></tr>";
}
$html .= "</table>";
$mpdf->WriteHTML($html);
$mpdf->Output("hasil_seleksi.pdf", "D"); ?>