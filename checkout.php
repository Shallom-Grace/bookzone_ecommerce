<?php
session_start();
include 'db_connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$cart_stmt = $conn->prepare("SELECT c.book_id, c.quantity, b.title, b.price FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?");
$cart_stmt->bind_param("i", $user_id);
$cart_stmt->execute();
$cart_result = $cart_stmt->get_result();
$cart_items = $cart_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $payment = $_POST['payment'];

    if (empty($cart_items)) {
        echo "<script>alert('Your cart is empty.'); window.location='cart.php';</script>";
        exit();
    }

    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, delivery_address, total, payment_method) VALUES (?, ?, ?, ?, ?)");
    $order_stmt->bind_param("issds", $user_id, $name, $address, $total, $payment);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $item_stmt->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
        $item_stmt->execute();
    }

    $clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart->bind_param("i", $user_id);
    $clear_cart->execute();

    echo "<script>alert('Order placed successfully!'); window.location='orders.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="checkout.css">
</head>
<body>
  <header>
    <h1>💳 BookZone Checkout</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

  <main class="checkout-section">
    <h2>Enter Your Details</h2>

    <form id="checkout-form" class="checkout-form" method="POST">
      <label for="name">Full Name:</label>
      <input type="text" name="name" id="name" placeholder="e.g. Jane Doe" required>

      <label for="address">Delivery Address:</label>
      <input type="text" name="address" id="address" placeholder="e.g. Nairobi, Kenya" required>

      <label for="payment">Payment Method:</label>
      <select name="payment" id="payment">
        <option value="mpesa">M-Pesa</option>
        <option value="paypal">PayPal</option>
        <option value="card">Credit Card</option>
      </select>

      <button type="submit" class="btn">Place Order</button>
    </form>
  </main>

  <footer id="contact">
    <p>📞 Contact us at: <a href="mailto:support@bookzone.com">support@bookzone.com</a></p>
    <p>📍 Nairobi, Kenya</p>
    <p>© 2025 BookZone. All rights reserved.</p>
  </footer>
</body>
</html>