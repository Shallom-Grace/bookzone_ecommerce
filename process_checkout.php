<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT c.book_id, c.quantity, b.title, b.price, b.image_url
    FROM cart c
    JOIN books b ON c.book_id = b.id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];

    // Update stock in books table
    $update_stock = $conn->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
    $update_stock->bind_param("ii", $qty, $book_id);
    $update_stock->execute();

}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        img { width: 50px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        input[type=text] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        input[type=submit] { background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="index.php">Shop Now</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Image</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Image"></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>KSh <?= number_format($item['price'], 2) ?></td>
                    <td>KSh <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Total: KSh <?= number_format($total, 2) ?></strong></p>

        <form action="process_checkout.php" method="POST">
            <div class="form-group">
                <label for="customer_name">Full Name:</label>
                <input type="text" name="customer_name" id="customer_name" required>
            </div>
            <div class="form-group">
                <label for="delivery_address">Delivery Address:</label>
                <input type="text" name="delivery_address" id="delivery_address" required>
            </div>
            <input type="submit" value="Place Order">
        </form>
    <?php endif; ?>
</div>
</body>
</html>
