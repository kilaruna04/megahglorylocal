<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Halaman Utama - Sistem Penjualan & Service</title>
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
    .main-card {
      max-width: 380px;   /* lebih kecil dari sebelumnya */
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
      max-width: 120px;   /* perkecil logo */
      margin-bottom: 15px;
    }
    h2 {
      font-size: 20px;
      margin-bottom: 8px;
    }
    p {
      font-size: 14px;
      margin-bottom: 20px;
    }
    .btn-choice {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      font-weight: bold;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-choice:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }
    .btn-admin {
      background: linear-gradient(to right, #00b09b, #96c93d);
      color: white;
      border: none;
    }
    .btn-teknisi {
      background: linear-gradient(to right, #36d1dc, #5b86e5);
      color: white;
      border: none;
    }
  </style>
</head>
<body>
  <div class="main-card">
    <img src="assets/img/mgtransparant2048.png" alt="Logo MG" class="logo">
    <h2 class="fw-bold">✨ Sistem Penjualan & Service</h2>
    <p>CV Megah Glory</p>
    <div class="d-grid gap-3">
      <a href="login.php?role=admin" class="btn btn-choice btn-admin">👨‍💼 Masuk sebagai Admin</a>
      <a href="login.php?role=teknisi" class="btn btn-choice btn-teknisi">🛠️ Masuk sebagai Teknisi</a>
    </div>
  </div>
</body>
</html>
