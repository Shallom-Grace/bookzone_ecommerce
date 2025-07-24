<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT books.id, books.title, books.image_url, books.price, cart.quantity
        FROM cart
        JOIN books ON cart.book_id = books.id
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="cartcss.css">
</head>
<body>
  <header>
    <h1>🛒 BookZone Cart</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

  <main class="cart-section">
    <h2>🧾 Items in Your Cart</h2>
    <div class="cart-grid">
      <?php if (count($cart_items) === 0): ?>
        <p class="empty-cart-msg">Your cart is empty. Browse books and add some!</p>
      <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
          <div class="cart-item">
            <div class="item-info">
              <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="cart-image">
              <div>
                <h4><?= htmlspecialchars($item['title']) ?></h4>
                <p>Price: KSh <?= number_format($item['price'], 2) ?></p>
                <p>
                  Quantity:
                  <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">−</button>
                  <span id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                  <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                </p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="cart-summary">
      <p><strong>Total:</strong> KSh <?= number_format($total, 2) ?></p>
      <a href="<?= count($cart_items) ? 'checkout.php' : '#' ?>" class="btn checkout-btn <?= count($cart_items) ? '' : 'disabled' ?>">Proceed to Checkout</a>
    </div>
  </main>

  <footer id="contact">
    <p>📞 Contact us at: <a href="mailto:support@bookzone.com">support@bookzone.com</a></p>
    <p>📍 Nairobi, Kenya</p>
    <p>© 2025 BookZone. All rights reserved.</p>
  </footer>

  <script>
    function updateQuantity(bookId, quantity) {
      if (quantity < 0) return;

      fetch("update_quantity.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `book_id=${bookId}&quantity=${quantity}`
      })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) location.reload();
      });
    }
  </script>
</body>
</html>
