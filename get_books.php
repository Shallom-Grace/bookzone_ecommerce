<?php
header("Content-Type: application/json");
include 'db_connect.php';

$sql = "SELECT id, title, author, price, image_url, stock FROM books";
$result = $conn->query($sql);

$books = [];

while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode($books);
?>
