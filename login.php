<?php
session_start();
require_once __DIR__ . "/config.php";

$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: Admin/index.php");
            } elseif ($user['role'] == 'teknisi') {
                header("Location: Teknisi/index.php");
            } else {
                $error = "Role tidak dikenali!";
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - Sistem Penjualan & Service</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(-45deg, #1E1E2F, #2b5876, #4e4376, #1E1E2F);
      background-size: 400% 400%;
      animation: gradientMove 10s ease infinite;
    }
    @keyframes gradientMove {
      0% {background-position: 0% 50%;}
      50% {background-position: 100% 50%;}
      100% {background-position: 0% 50%;}
    }
    .login-card {
      max-width: 360px;
      width: 100%;
      border-radius: 16px;
      padding: 25px 20px;
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 25px rgba(0,0,0,0.5);
      text-align: center;
      color: white;
      animation: fadeIn 1s ease;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    .logo {
      max-width: 100px;
      margin-bottom: 15px;
    }
    h3 {
      font-size: 20px;
      margin-bottom: 8px;
    }
    p {
      font-size: 14px;
      margin-bottom: 15px;
    }
    .btn-custom {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      font-weight: bold;
      background: linear-gradient(to right, #36d1dc, #5b86e5);
      border: none;
      color: white;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-custom:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    a.back-link {
      color: #fff;
      text-decoration: none;
      font-size: 14px;
    }
    a.back-link:hover {
      text-decoration: underline;
    }
    .alert {
      font-size: 14px;
      padding: 8px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <img src="assets/img/mgtransparant2048.png" alt="Logo MG" class="logo">

    <div class="mb-3">
      <h3 class="fw-bold">🔑 Login</h3>
      <?php if ($role == "admin") { ?>
        <p>Masuk sebagai <strong>Admin</strong></p>
      <?php } elseif ($role == "teknisi") { ?>
        <p>Masuk sebagai <strong>Teknisi</strong></p>
      <?php } else { ?>
        <p>Sistem Penjualan & Service</p>
      <?php } ?>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3 text-start">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
      </div>
      <div class="mb-3 text-start">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-custom">Login</button>
      </div>
    </form>

    <hr style="border-color: rgba(255,255,255,0.3);">
    <div class="text-center">
      <a href="index.php" class="back-link">⬅ Kembali ke Halaman Utama</a>
    </div>
  </div>
</body>
</html>
