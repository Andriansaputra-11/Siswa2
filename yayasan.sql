CREATE DATABASE yayasan_baituljihad;
USE yayasan_baituljihad;

-- Hapus tabel jika sudah ada
DROP TABLE IF EXISTS hasil_seleksi;
DROP TABLE IF EXISTS siswa;

-- Buat tabel siswa
CREATE TABLE siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  nik VARCHAR(20),
  asal_sekolah VARCHAR(100),
  penghasilan_ortu INT,
  tanggungan_ortu INT,
  status_ortu VARCHAR(50),
  status_rumah VARCHAR(50),
  is_alumni TINYINT(1)
);

-- Buat tabel hasil seleksi
CREATE TABLE hasil_seleksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_siswa INT,
  metode VARCHAR(10),
  nilai DECIMAL(5,2),
  ranking INT
);

-- Masukkan data awal siswa
INSERT INTO siswa (nama, nik, asal_sekolah, penghasilan_ortu, tanggungan_ortu, status_ortu, status_rumah, is_alumni) VALUES
('Ahmad Fauzi', '3210011223344556', 'SMPN 1 Cileungsi', 1000000, 4, 'Yatim', 'Sewa', 1),
('Siti Aminah', '3210044556677889', 'SMPN 2 Cibinong', 1500000, 3, 'Lengkap', 'Milik Sendiri', 0),
('Budi Setiawan', '3210067788990011', 'SMP IT Nurul Iman', 800000, 5, 'Yatim Piatu', 'Sewa', 1);
