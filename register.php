<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['error'] = "Email already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();
        $_SESSION['user'] = ['name' => $name, 'email' => $email];
        header("Location: index.php"); 
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register - BookZone</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .form-container {
      max-width: 400px;
      margin: 50px auto;
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }
    .form-container h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }
    .form-container input[type="text"],
    .form-container input[type="email"],
    .form-container input[type="password"] {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    .form-container button {
      width: 100%;
      background-color: #2980b9;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
    .form-container button:hover {
      background-color: #2471a3;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .form-container p {
      text-align: center;
    }
  </style>
</head>
<body>
  <header>
    <h1>📚 BookZone</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Register</h2>
    <?php if (isset($_SESSION['error'])): ?>
      <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
  </div>
</body>
</html>
