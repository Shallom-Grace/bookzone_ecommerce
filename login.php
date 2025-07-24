<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
    if ($password === $user['password']) {  
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_id'] = $user['id']; 
        //echo "login in successful";
        header("Location: index.php");
        exit;
    } else {
        $error = "Incorrect password.";
    }
} else {
    $error = "User not found.";
}
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login - BookZone</title>
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
      background-color: #27ae60;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
    }
    .form-container button:hover {
      background-color: #219150;
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
      <a href="index.html">Home</a>
      <a href="register.php">Register</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Login</h2>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error): ?>
      <p class="error"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
  </div>
</body>
</html>
