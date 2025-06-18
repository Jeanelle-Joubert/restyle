<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Fetch the user's products
$userId = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Store - ReStyle</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">ReStyle</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="my_store.php">My Store</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-3" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search..." aria-label="Search">
                    <button class="btn btn-light" type="submit">Search</button>
                </form>

                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <span class="nav-link">Hello, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- My Store Content -->
    <div class="container my-5">

        <!-- Success message if product added -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <h1 class="mb-4">My Store</h1>
        <p>Welcome to your store! Here you can manage your listings, add new products, and track your sales.</p>

        <!-- Button linking to add_product.php -->
        <a href="add_product.php" class="btn btn-primary mb-4">+ Add New Product</a>

        <!-- Product Listings -->
        <div class="row">
            <?php if (count($products) === 0): ?>
                <div class="alert alert-info">You haven’t listed any products yet.</div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($product['title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="card-text"><strong>R<?= number_format($product['price'], 2) ?></strong></p>
                                <p class="card-text"><small
                                        class="text-muted"><?= htmlspecialchars(ucfirst($product['category'])) ?></small></p>

                                <!-- Delete Form -->
                                <form method="POST" action="delete_product.php"
                                      onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm mt-2">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center py-3 mt-auto">
        <div class="container">
            &copy; 2025 ReStyle. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
