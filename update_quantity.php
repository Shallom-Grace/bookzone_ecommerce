<?php
session_start();
require 'db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

if ($book_id <= 0 || $quantity < 0) {
    echo json_encode(["success" => false, "message" => "Invalid book or quantity."]);
    exit();
}

if ($quantity == 0) {
    $delete = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND book_id = ?");
    $delete->bind_param("ii", $user_id, $book_id);
    if ($delete->execute()) {
        echo json_encode(["success" => true, "message" => "Item removed from cart."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to remove item."]);
    }
} else {
    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?");
    $update->bind_param("iii", $quantity, $user_id, $book_id);
    if ($update->execute()) {
        echo json_encode(["success" => true, "message" => "Quantity updated."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update quantity."]);
    }
}
?>
