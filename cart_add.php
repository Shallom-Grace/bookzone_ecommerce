<?php
session_start();
require 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to add items to your cart.";
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;

if ($book_id <= 0) {
    echo "Invalid book selected.";
    exit();
}

$check = $conn->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
$check->bind_param("ii", $user_id, $book_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?");
    $update->bind_param("ii", $user_id, $book_id);
    $update->execute();
    echo "Book quantity updated in cart.";
} else {
    $insert = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $book_id);
    $insert->execute();
    echo "Book added to cart.";
}
?>
