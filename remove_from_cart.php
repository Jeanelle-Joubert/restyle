<?php
session_start();

if (!isset($_POST['product_id'])) {
    header('Location: cart.php');
    exit;
}

$product_id = (int) $_POST['product_id'];

// Remove product from cart array
if (isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], fn($id) => $id !== $product_id);
    // Reindex array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

header('Location: cart.php');
exit;
