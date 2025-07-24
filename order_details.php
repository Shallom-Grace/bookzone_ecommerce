<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$order_stmt = $conn->prepare("
    SELECT * FROM orders WHERE id = ? AND user_id = ?
");
$order_stmt->bind_param("ii", $order_id, $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order['id'] ?> Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 30px;
        }

        .order-container {
            background: white;
            max-width: 900px;
            margin: auto;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .info {
            margin-bottom: 20px;
        }

        .info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        table img {
            width: 60px;
            border-radius: 4px;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 15px;
        }

        a.back {
            display: inline-block;
            margin-top: 20px;
            background: #3498db;
            color: #fff;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
        }

        a.back:hover {
            background: #2980b9;
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
    <h1>🛒 BookZone order details</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="orders.php">Orders</a>
      <a href="#contact">Contact</a>
    </nav>
  </header>

<div class="order-container">
    <h2>Order #<?= $order['id'] ?> Details</h2>

    <div class="info">
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['delivery_address']) ?></p>
        <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Book</th>
                <th>Image</th>
                <th>Qty</th>
                <th>Price (KSh)</th>
                <th>Total (KSh)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $items_stmt = $conn->prepare("
            SELECT oi.quantity, oi.price, b.title, b.image_url
            FROM order_items oi
            JOIN books b ON oi.book_id = b.id
            WHERE oi.order_id = ?
        ");
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        while ($item = $items_result->fetch_assoc()):
        ?>
            <tr>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><img src="<?= $item['image_url'] ?>" alt="<?= $item['title'] ?>"></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <div class="total">Total: KSh <?= number_format($order['total'], 2) ?></div>

    <a href="orders.php" class="back">← Back to Orders</a>
</div>
  <footer id="contact">
    <p>📞 Contact us at: <a href="mailto:support@bookzone.com">support@bookzone.com</a></p>
    <p>📍 Nairobi, Kenya</p>
    <p>© 2025 BookZone. All rights reserved.</p>
  </footer>
</body>
</html>
