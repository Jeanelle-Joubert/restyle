<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch wishlist products with product details
$sql = "SELECT p.* FROM products p
        INNER JOIN wishlist w ON p.id = w.product_id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$wishlistProducts = $stmt->fetchAll();
$wishlistIds = array_column($wishlistProducts, 'id');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Wishlist - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                    <li class="nav-item"><a class="nav-link" href="my_store.php">My Store</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
                    <li class="nav-item"><a class="nav-link active" href="wishlist.php">Wishlist</a></li>
                </ul>

                <form class="d-flex me-3" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search..." aria-label="Search"
                        required>
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

    <!-- Wishlist Content -->
    <div class="container my-5">
        <h1 class="mb-4">My Wishlist</h1>

        <?php if (empty($wishlistProducts)): ?>
            <div class="alert alert-info">Your wishlist is empty.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($wishlistProducts as $product): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($product['title']) ?>" style="object-fit: cover; height: 250px;" />
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text mb-1"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="text-primary fw-bold mb-2">R<?= number_format($product['price'], 2) ?></p>
                                <span class="badge bg-secondary mb-3"><?= ucfirst($product['category']) ?></span>

                                <button class="btn btn-outline-danger btn-sm mb-2 wishlist-toggle"
                                    data-product-id="<?= $product['id'] ?>" title="Remove from Wishlist">
                                    <i class="fa-solid fa-heart"></i> Remove from Wishlist
                                </button>

                                <a href="#" class="btn btn-outline-primary mt-auto">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center py-3 mt-auto">
        <div class="container">
            &copy; 2025 ReStyle. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.wishlist-toggle').click(function (e) {
                e.preventDefault();
                const btn = $(this);
                const productId = btn.data('product-id');

                $.post('wishlist_add.php', { product_id: productId }, function (response) {
                    if (response.status === 'removed') {
                        btn.closest('.col-md-4').fadeOut(300, function () { $(this).remove(); });
                        if ($('.col-md-4').length === 1) {
                            $('.container.my-5').append('<div class="alert alert-info mt-3">Your wishlist is empty.</div>');
                        }
                    }
                }, 'json');
            });
        });
    </script>
</body>

</html>