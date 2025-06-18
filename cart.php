<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user']['id'] ?? null;

// Initialize cart
$cart = $_SESSION['cart'] ?? [];

// If cart is empty
if (empty($cart)) {
    $products = [];
} else {
    // Extract product IDs (keys of the cart)
    $product_ids = array_keys($cart);

    // Prepare placeholders for PDO
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    // Prepare and execute statement
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Calculate total price considering quantity
$total_price = 0;
foreach ($products as $product) {
    $id = $product['id'];
    $quantity = $cart[$id]['quantity'] ?? 1;
    $total_price += $product['price'] * $quantity;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Cart - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="style.css" rel="stylesheet" />
    <style>
        /* Product image styling */
        .cart-product-img {
            height: 60px;
            object-fit: cover;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h1>Your Cart</h1>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">Your cart is empty.</div>
            <a href="browse.php" class="btn btn-primary">Continue Shopping</a>
        <?php else: ?>
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product):
                        $id = $product['id'];
                        $quantity = $cart[$id]['quantity'] ?? 1;
                        $subtotal = $product['price'] * $quantity;
                        ?>
                        <tr>
                            <td class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($product['image_url']) ?>"
                                     alt="<?= htmlspecialchars($product['title']) ?>"
                                     class="cart-product-img">
                                <?= htmlspecialchars($product['title']) ?>
                            </td>
                            <td>R<?= number_format($product['price'], 2) ?></td>
                            <td><?= $quantity ?></td>
                            <td>R<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <form method="post" action="remove_from_cart.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-accent btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>R<?= number_format($total_price, 2) ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <a href="browse.php" class="btn btn-secondary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-accent">Checkout</a>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
