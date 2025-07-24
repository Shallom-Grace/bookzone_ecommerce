<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$orders_query = $conn->prepare("
    SELECT id, customer_name, delivery_address, total, created_at 
    FROM orders 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$orders_query->bind_param("i", $user_id);
$orders_query->execute();
$orders_result = $orders_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .order-summary {
            max-width: 600px;
            margin: 15px auto;
            padding: 20px;
            background: #fff;
            border-left: 5px solid #3498db;
            border-radius: 5px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .order-summary h3 {
            margin: 0 0 10px;
        }

        .order-summary p {
            margin: 5px 0;
        }

        .order-summary a {
            display: inline-block;
            margin-top: 10px;
            color: #fff;
            background-color: #3498db;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .order-summary a:hover {
            background-color: #2980b9;
        }

        header {
  background: #2c3e50;
  padding: 15px;
  color: white;
  text-align: center;
}

nav a {
  margin: 0 10px;
  color: white;
  text-decoration: none;
}

footer {
  background: #2c3e50;
  color: white;
  padding: 20px;
  text-align: center;
}
footer a {
  color: #f1c40f;
  text-decoration: none;
}
footer a:hover {
  text-decoration: underline;
}

    </style>
</head>
<body>
    <header>
    <h1>🛒 BookZone Orders</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

<h2>Your Orders</h2>

<?php while ($order = $orders_result->fetch_assoc()): ?>
    <div class="order-summary">
        <h3>Order #<?= $order['id'] ?></h3>
        <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
        <p><strong>Total:</strong> KSh <?= number_format($order['total'], 2) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
        <a href="order_details.php?id=<?= $order['id'] ?>">View Details</a>
    </div>
<?php endwhile; ?>
  <footer id="contact">
    <p>📞 Contact us at: <a href="mailto:support@bookzone.com">support@bookzone.com</a></p>
    <p>📍 Nairobi, Kenya</p>
    <p>© 2025 BookZone. All rights reserved.</p>
  </footer>

</body>
</html>
