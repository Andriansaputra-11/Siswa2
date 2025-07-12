CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(100) NOT NULL,
  role ENUM('admin', 'user') NOT NULL
);

-- Contoh data awal
INSERT INTO users (username, password, role) VALUES
('admin', 'admin123', 'admin'),
('siswa1', 'siswa123', 'user');
