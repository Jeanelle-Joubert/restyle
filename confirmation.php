<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    echo "<div class='alert alert-danger'>No order specified.</div>";
    exit;
}

// Fetch order info, verify ownership
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "<div class='alert alert-danger'>Order not found or access denied.</div>";
    exit;
}

// Fetch order items with product info
$stmt = $pdo->prepare("
    SELECT oi.quantity, oi.price, p.title
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Order Confirmation - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5">
        <h2>Thank You for Your Order!</h2>
        <p>Your order <strong>#<?= htmlspecialchars($order_id) ?></strong> was placed successfully on
            <?= htmlspecialchars(date('F j, Y, g:i a', strtotime($order['created_at']))) ?>.
        </p>

        <h4>Order Details</h4>
        <ul class="list-group mb-3">
            <?php foreach ($order_items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($item['title']) ?></strong> x <?= $item['quantity'] ?>
                    </div>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between">
                <strong>Total</strong>
                <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
            </li>
        </ul>

        <a href="browse.php" class="btn btn-primary">Continue Shopping</a>
        <a href="index.php" class="btn btn-secondary ms-2">Home</a>
    </div>
</body>

</html>