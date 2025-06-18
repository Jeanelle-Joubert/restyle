<?php
session_start();
require 'db.php';

// Check if product_id is provided
if (!isset($_POST['product_id'])) {
    header('Location: browse.php');
    exit;
}

$product_id = (int) $_POST['product_id'];

// Fetch product info from the database
$stmt = $pdo->prepare("SELECT id, title, price FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    // Product not found, redirect
    header('Location: browse.php');
    exit;
}

// Initialize the cart if not set or not an array
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update product quantity
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$product_id] = [
        'id' => $product['id'],
        'title' => $product['title'],
        'price' => $product['price'],
        'quantity' => 1
    ];
}

// Redirect back to browse page
header('Location: browse.php');
exit;
