<?php 
session_start(); 
include 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $u = $_POST['username']; 
  $p = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1");
  $stmt->bind_param("ss", $u, $p);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $_SESSION['login'] = true;
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    if ($data['role'] == 'admin') {
      header("Location: dashboard_admin.php");
    } else if ($data['role'] == 'user') {
      header("Location: dashboard_user.php");
    } else {
      $error = "Role tidak dikenali!";
    }
    exit();
  } else {
    $error = "❌ Username atau Password salah!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Yayasan Baitul Jihad</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff);
      background-size: 400% 400%;
      animation: gradientMove 15s ease infinite;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-box {
      background: white;
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      width: 100%;
      max-width: 400px;
      position: relative;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #2c3e50;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 40px 12px 15px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
    }

    .input-group i {
      position: absolute;
      top: 12px;
      right: 15px;
      color: #aaa;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #3498db;
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #2c80b4;
    }

    .error {
      text-align: center;
      color: #e74c3c;
      font-size: 14px;
      margin-top: 15px;
    }

    .branding {
      text-align: center;
      margin-bottom: 30px;
    }

    .branding img {
      height: 70px;
      margin-bottom: 10px;
    }

    .branding span {
      display: block;
      font-size: 20px;
      font-weight: bold;
      color: #2c3e50;
    }

    @media(max-width: 480px) {
      .login-box {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="branding">
      <img src="assets/img/logo.png" alt="Logo">
      <span>Yayasan Baitul Jihad</span>
    </div>
    <h2>Login Admin / User</h2>
    <form method="post" autocomplete="off">
      <div class="input-group">
        <input type="text" name="username" placeholder="Username" required>
        <i class="fas fa-user"></i>
      </div>
      <div class="input-group">
        <input type="password" name="password" placeholder="Password" required>
        <i class="fas fa-lock"></i>
      </div>
      <button type="submit">Masuk</button>
      <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
