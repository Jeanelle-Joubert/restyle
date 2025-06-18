<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user']['id']) || !isset($_POST['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized or incomplete data']);
    exit;
}

$user_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);

require 'db.php';

// Check if product is already in wishlist
$sql_check = "SELECT COUNT(*) FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute(['user_id' => $user_id, 'product_id' => $product_id]);
$exists = $stmt_check->fetchColumn() > 0;

if ($exists) {
    // Remove from wishlist
    $sql_remove = "DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    $stmt_remove = $pdo->prepare($sql_remove);
    if ($stmt_remove->execute(['user_id' => $user_id, 'product_id' => $product_id])) {
        echo json_encode(['status' => 'removed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove from wishlist']);
    }
} else {
    // Add to wishlist
    $sql_add = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
    $stmt_add = $pdo->prepare($sql_add);
    if ($stmt_add->execute(['user_id' => $user_id, 'product_id' => $product_id])) {
        echo json_encode(['status' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add to wishlist']);
    }
}
