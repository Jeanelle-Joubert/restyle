<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<div class='alert alert-warning'>Your cart is empty. <a href='browse.php'>Shop now</a></div>";
    exit;
}

$total = 0;
foreach ($cart as $item) {
    if (is_array($item) && isset($item['price'], $item['quantity'])) {
        $total += $item['price'] * $item['quantity'];
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $card_number = trim($_POST['card_number'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');
    $billing_address = trim($_POST['billing_address'] ?? '');

    if ($name && $email && $address && $card_number && $expiry && $cvv && $billing_address) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $total]);
        $orderId = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($cart as $item) {
            if (is_array($item) && isset($item['id'], $item['quantity'], $item['price'])) {
                $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            }
        }

        unset($_SESSION['cart']);
        header("Location: confirmation.php?order_id=$orderId");
        exit;
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Checkout - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Checkout</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="row">
                <div class="col-md-6">
                    <h4>Shipping Details</h4>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" required
                            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Shipping Address</label>
                        <textarea name="address" id="address" class="form-control" required
                            rows="3"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>Payment Details</h4>
                    <div class="mb-3">
                        <label for="card_number" class="form-label">Card Number</label>
                        <input type="text" name="card_number" id="card_number" class="form-control" maxlength="19"
                            placeholder="1234 5678 9012 3456" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <label for="expiry" class="form-label">Expiry Date</label>
                            <input type="text" name="expiry" id="expiry" class="form-control" placeholder="MM/YY"
                                required>
                        </div>
                        <div class="col">
                            <label for="cvv" class="form-label">CVV</label>
                            <input type="text" name="cvv" id="cvv" class="form-control" maxlength="4" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="billing_address" class="form-label">Billing Address</label>
                        <textarea name="billing_address" id="billing_address" class="form-control" required
                            rows="3"><?= htmlspecialchars($_POST['billing_address'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <h4 class="mt-4">Order Summary</h4>
            <ul class="list-group mb-4">
                <?php foreach ($cart as $item): ?>
                    <?php if (is_array($item)): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($item['title']) ?></strong> x <?= (int) $item['quantity'] ?>
                            </div>
                            <span>R<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <li class="list-group-item d-flex justify-content-between">
                    <strong>Total</strong>
                    <strong>R<?= number_format($total, 2) ?></strong>
                </li>
            </ul>

            <button type="submit" class="btn btn-success btn-lg w-100">Place Order</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>