<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include 'inc/db.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->fromArray(['Nama', 'Nilai', 'Metode'], NULL, 'A1');
$res = $conn->query("SELECT s.nama, h.nilai, h.metode FROM hasil_seleksi h JOIN siswa s ON h.id_siswa=s.id");
$row = 2;
while($r = $res->fetch_assoc()) {
  $sheet->setCellValue("A$row", $r['nama']);
  $sheet->setCellValue("B$row", $r['nilai']);
  $sheet->setCellValue("C$row", $r['metode']);
  $row++;
}
$writer = new Xlsx($spreadsheet);
$writer->save("hasil_seleksi.xlsx");
header("Content-Disposition: attachment; filename=hasil_seleksi.xlsx");
readfile("hasil_seleksi.xlsx"); ?>