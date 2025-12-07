<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MoneyMate - Selamat Datang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      height: 100vh;
      margin: 0;
      background: linear-gradient(135deg, #fbbd9f, #cde7b0);
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .welcome-box {
      text-align: center;
    }
    .welcome-box img {
      width: 330px;
      height: auto;
      margin-bottom: -32px;
    }
    .welcome-box h1 {
      font-size: 26px;
      font-weight: bold;
      color: #444;
      margin-bottom: 90px;
    }
    .btn-start {
      background-color: #5c8a63;
      color: white;
      border: none;
      padding: 10px 30px;
      font-size: 14px;
      border-radius: 20px;
    }
    .btn-start:hover {
      background-color: #6c7d6b;
    }

    @media (max-width: 576px) {
      .welcome-box img {
        width: 220px;
        margin-bottom: -10px;
      }
      .welcome-box h1 {
        font-size: 24px;
      }
      .btn-start {
        padding: 8px 24px;
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <div class="welcome-box">
    <img src="logo.png" alt="Logo MoneyMate">
    <h1>MoneyMate</h1>
    <a href="login.php" class="btn btn-start">Mulai</a>
  </div>
</body>
</html>
