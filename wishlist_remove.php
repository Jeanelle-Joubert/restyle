<?php
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_POST['product_id'])) {
    exit('Unauthorized or incomplete data');
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);

require 'db.php';

$sql = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
$stmt = $pdo->prepare($sql);

if ($stmt->execute(['user_id' => $user_id, 'product_id' => $product_id])) {
    echo "Removed from wishlist";
} else {
    echo "Error removing from wishlist";
}
