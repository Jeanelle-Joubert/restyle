<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user']['id'] ?? null;

$categoryFilter = $_GET['category'] ?? '';
$orderBy = $_GET['order_by'] ?? 'created_at_desc';
$priceMin = isset($_GET['price_min']) ? (int) $_GET['price_min'] : 0;
$priceMax = isset($_GET['price_max']) ? (int) $_GET['price_max'] : 1000;

$query = "SELECT * FROM products WHERE price BETWEEN :price_min AND :price_max";
$params = [
    ':price_min' => $priceMin,
    ':price_max' => $priceMax
];

if ($categoryFilter) {
    $query .= " AND category = :category";
    $params[':category'] = $categoryFilter;
}

switch ($orderBy) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'created_at_asc':
        $query .= " ORDER BY created_at ASC";
        break;
    case 'created_at_desc':
    default:
        $query .= " ORDER BY created_at DESC";
        break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$wishlistIds = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $wishlistIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Browse Products - ReStyle</title>
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
                    <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
                    <li class="nav-item"><a class="nav-link active" href="browse.php">Browse</a></li>
                </ul>

                <form class="d-flex me-3" action="search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search products..."
                        aria-label="Search">
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

    <!-- Browse Content -->
    <div class="container my-5">
        <h1 class="mb-4">Browse Products</h1>
        <div class="row">
            <!-- Sidebar Filters -->
            <aside class="col-md-3">
                <form id="filters-form" method="GET" action="browse.php">
                    <div class="mb-4">
                        <label for="category" class="form-label fw-bold">Category</label>
                        <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <option value="women" <?= $categoryFilter === 'women' ? 'selected' : '' ?>>Women</option>
                            <option value="men" <?= $categoryFilter === 'men' ? 'selected' : '' ?>>Men</option>
                            <option value="accessories" <?= $categoryFilter === 'accessories' ? 'selected' : '' ?>>
                                Accessories</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Price Range (R)</label>
                        <div class="price-labels d-flex justify-content-between text-muted">
                            <span id="price-min-label"><?= $priceMin ?></span>
                            <span id="price-max-label"><?= $priceMax ?></span>
                        </div>
                        <input type="range" class="form-range" min="0" max="1000" step="10" id="price_min"
                            name="price_min" value="<?= $priceMin ?>" oninput="priceMinLabel.innerText = this.value" />
                        <input type="range" class="form-range mt-3" min="0" max="1000" step="10" id="price_max"
                            name="price_max" value="<?= $priceMax ?>" oninput="priceMaxLabel.innerText = this.value" />
                    </div>

                    <div class="mb-4">
                        <label for="order_by" class="form-label fw-bold">Order By</label>
                        <select class="form-select" id="order_by" name="order_by" onchange="this.form.submit()">
                            <option value="created_at_desc" <?= $orderBy === 'created_at_desc' ? 'selected' : '' ?>>Newest
                            </option>
                            <option value="created_at_asc" <?= $orderBy === 'created_at_asc' ? 'selected' : '' ?>>Oldest
                            </option>
                            <option value="price_asc" <?= $orderBy === 'price_asc' ? 'selected' : '' ?>>Price: Low to High
                            </option>
                            <option value="price_desc" <?= $orderBy === 'price_desc' ? 'selected' : '' ?>>Price: High to
                                Low</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="browse.php" class="btn btn-secondary">Clear Filters</a>
                    </div>
                </form>
            </aside>

            <!-- Product Grid -->
            <main class="col-md-9">
                <?php if ($categoryFilter): ?>
                    <p class="text-muted">Showing results for category:
                        <strong><?= htmlspecialchars($categoryFilter) ?></strong>
                    </p>
                <?php endif; ?>

                <?php if (empty($products)): ?>
                    <div class="alert alert-warning">No products found.</div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                            <?php $inWishlist = in_array($product['id'], $wishlistIds); ?>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <div class="card h-100">
                                    <a href="product_detail.php?id=<?= $product['id'] ?>"
                                        class="text-decoration-none text-dark">
                                        <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top"
                                            alt="<?= htmlspecialchars($product['title']) ?>"
                                            style="object-fit: cover; height: 250px;" />
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                            <p class="card-text mb-1"><?= htmlspecialchars($product['description']) ?></p>
                                            <p class="text-primary fw-bold mb-2">R<?= number_format($product['price'], 2) ?></p>
                                            <span class="badge bg-secondary mb-3"><?= ucfirst($product['category']) ?></span>
                                        </div>
                                    </a>
                                    <div class="card-body pt-0">
                                        <?php if ($user_id): ?>
                                            <button class="btn btn-outline-danger btn-sm mb-2 wishlist-toggle"
                                                data-product-id="<?= $product['id'] ?>" title="Add or Remove from Wishlist">
                                                <i class="<?= $inWishlist ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i> Wishlist
                                            </button>
                                        <?php endif; ?>
                                        <form method="POST" action="add_to_cart.php" class="mt-auto">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 text-center">
        <div class="container">&copy; 2025 ReStyle. All rights reserved.</div>
    </footer>

    <script>
        const priceMinLabel = document.getElementById('price-min-label');
        const priceMaxLabel = document.getElementById('price-max-label');
        const priceMinInput = document.getElementById('price_min');
        const priceMaxInput = document.getElementById('price_max');

        priceMinInput.addEventListener('input', function () {
            let minVal = parseInt(this.value);
            let maxVal = parseInt(priceMaxInput.value);
            if (minVal > maxVal) {
                priceMaxInput.value = minVal;
                priceMaxLabel.innerText = minVal;
            }
            priceMinLabel.innerText = this.value;
        });

        priceMaxInput.addEventListener('input', function () {
            let maxVal = parseInt(this.value);
            let minVal = parseInt(priceMinInput.value);
            if (maxVal < minVal) {
                priceMinInput.value = maxVal;
                priceMinLabel.innerText = maxVal;
            }
            priceMaxLabel.innerText = this.value;
        });

        // Wishlist toggle
        $(document).ready(function () {
            $('.wishlist-toggle').click(function () {
                const btn = $(this);
                const productId = btn.data('product-id');

                $.post('wishlist_add.php', { product_id: productId }, function (response) {
                    if (response.status === 'added') {
                        btn.find('i').removeClass('fa-regular').addClass('fa-solid');
                    } else if (response.status === 'removed') {
                        btn.find('i').removeClass('fa-solid').addClass('fa-regular');
                    } else {
                        alert(response.message || 'Error updating wishlist.');
                    }
                }, 'json');
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>