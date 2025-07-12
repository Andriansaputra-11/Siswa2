-- Hapus tabel jika sudah ada
DROP TABLE IF EXISTS siswa;

-- Buat ulang tabel siswa dengan kolom jenjang
CREATE TABLE siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100),
  nik VARCHAR(20),
  asal_sekolah VARCHAR(100),
  penghasilan_ortu INT,
  tanggungan_ortu INT,
  status_ortu VARCHAR(50),
  status_rumah VARCHAR(50),
  is_alumni TINYINT(1),
  ac TINYINT(1) DEFAULT 0,
  tv TINYINT(1) DEFAULT 0,
  kulkas TINYINT(1) DEFAULT 0,
  motor INT DEFAULT 0,
  jarak_km FLOAT DEFAULT 0,
  jenjang VARCHAR(20) NOT NULL
);

-- Contoh data siswa SD
INSERT INTO siswa (nama, nik, asal_sekolah, penghasilan_ortu, tanggungan_ortu, status_ortu, status_rumah, is_alumni, ac, tv, kulkas, motor, jarak_km, jenjang)
VALUES 
('Ahmad SD', '1111111111', 'SDIT A', 800000, 4, 'Yatim', 'Sewa', 1, 1, 1, 1, 1, 2.5, 'SD'),
('Fatimah SD', '1111111112', 'SDIT B', 900000, 3, 'Lengkap', 'Milik', 0, 0, 1, 0, 2, 1.5, 'SD');

-- Contoh data siswa SMP
INSERT INTO siswa (nama, nik, asal_sekolah, penghasilan_ortu, tanggungan_ortu, status_ortu, status_rumah, is_alumni, ac, tv, kulkas, motor, jarak_km, jenjang)
VALUES 
('Rizky SMP', '2222222221', 'SMP IT A', 950000, 4, 'Yatim Piatu', 'Sewa', 1, 1, 1, 1, 1, 2.2, 'SMP'),
('Sari SMP', '2222222222', 'SMP IT B', 1200000, 2, 'Lengkap', 'Milik', 0, 1, 0, 0, 1, 3.0, 'SMP');
