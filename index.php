<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ReStyle - Marketplace for Preloved Clothing</title>
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
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="my_store.php">My Store</a></li>
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

    <!-- Hero Section -->
    <section class="hero-section py-5 text-center">
        <div class="container">
            <h1 class="display-4">Buy & Sell Preloved Clothing Easily</h1>
            <p class="lead">Join ReStyle today and refresh your wardrobe sustainably.</p>
            <a href="browse.php" class="btn btn-accent btn-lg mx-2">Browse Clothes</a>
            <a href="my_store.php" class="btn btn-accent btn-lg mx-2">Sell Your Clothes</a>
        </div>
    </section>

    <!-- Featured Categories -->
    <section class="container my-5">
        <h2 class="mb-4">Shop by Category</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <a href="browse.php?category=women" class="text-decoration-none">
                    <div class="card h-100 category-card-red">
                        <div class="card-body">
                            <h5 class="card-title text-white">Women’s Clothing</h5>
                            <p class="card-text text-white">Trendy preloved clothes for women.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="browse.php?category=men" class="text-decoration-none">
                    <div class="card h-100 category-card-red">
                        <div class="card-body">
                            <h5 class="card-title text-white">Men’s Clothing</h5>
                            <p class="card-text text-white">Quality preloved clothes for men.</p>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="browse.php?category=accessories" class="text-decoration-none">
                    <div class="card h-100 category-card-red">
                        <div class="card-body">
                            <h5 class="card-title text-white">Accessories</h5>
                            <p class="card-text text-white">Find preloved bags, jewelry, and more.</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center py-3 mt-auto">
        <div class="container">
            &copy; 2025 ReStyle. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>
