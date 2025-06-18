<?php
session_start();
require 'db.php';

$searchTerm = $_GET['q'] ?? '';
$products = [];

if (!empty($searchTerm)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC");
    $likeTerm = "%" . $searchTerm . "%";
    $stmt->execute([$likeTerm, $likeTerm]);
    $products = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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
                    <li class="nav-item"><a class="nav-link" href="#">Cart</a></li>
                    <li class="nav-item"><a class="nav-link" href="wishlist.php">Wishlist</a></li>
                    <li class="nav-item"><a class="nav-link active" href="search.php">Search</a></li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex" method="GET" action="search.php">
                    <input class="form-control me-2" type="search" name="q" value="<?= htmlspecialchars($searchTerm) ?>"
                        placeholder="Search items..." aria-label="Search">
                    <button class="btn btn-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Search Results -->
    <div class="container my-5">
        <h2 class="mb-4">Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h2>

        <?php if (empty($searchTerm)): ?>
            <div class="alert alert-info">Please enter a search term.</div>
        <?php elseif (count($products) === 0): ?>
            <div class="alert alert-warning">No results found.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($product['title']) ?>" style="object-fit: cover; height: 250px;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                                <p class="text-primary fw-bold">$<?= number_format($product['price'], 2) ?></p>
                                <span class="badge bg-secondary mb-2"><?= ucfirst($product['category']) ?></span>
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
</body>

</html>