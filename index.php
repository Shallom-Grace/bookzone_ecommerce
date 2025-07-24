<?php
session_start();
?>
...
<p style="text-align: center; margin-top: 10px;">
  Welcome <?= isset($_SESSION['email']) ? $_SESSION['email'] : 'Guest' ?>
</p>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>BookZone - Online Bookstore</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h1>📚 BookZone</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="#categories">Categories</a>
      <a href="#contact">Contact</a>
      <a href="login.php" style="color: #f1c40f;">Login</a>
      <a href="logout.php" style="color: #f1c40f;">Logout</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-content">
      <h2>Welcome to BookZone</h2>
      <p>Find your next great read among our curated collection of books.</p>
      <a href="#books" class="btn">Browse Books</a>
    </div>
  </section>

  <section id="categories" class="categories">
    <h2>Popular Categories</h2>
    <div class="category-grid">
      <div class="category-card">Fiction</div>
      <div class="category-card">Self Help</div>
      <div class="category-card">Business</div>
      <div class="category-card">Academic</div>
      <div class="category-card">Children</div>
      <div class="category-card">Romance</div>
    </div>
  </section>

  <main>
    <h2>Available Books</h2>
    <div id="books" class="book-grid"></div>
  </main>

  <footer id="contact">
    <p>📞 Contact us at: <a href="mailto:support@bookzone.com">support@bookzone.com</a></p>
    <p>📍 Nairobi, Kenya</p>
    <p>© 2025 BookZone. All rights reserved.</p>
  </footer>

  <button id="chat-icon">💬</button>

  <div id="chat-popup" class="hidden">
    <div id="chat-header">Ask a Question</div>
    <div id="chat-history"></div>
    <div id="chat-controls">
      <input type="text" id="chatInput" placeholder="Type your message..." />
      <button onclick="sendChat()">Send</button>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
