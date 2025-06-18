<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission

    $userId = $_SESSION['user']['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = $_POST['price'];
    $category = $_POST['category'];

    // Validate inputs (simplified)
    if (!$title || !$description || !$price || !$category || !isset($_FILES['image'])) {
        $error = "Please fill in all fields.";
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = "Price must be a positive number.";
    } else {
        // Validate and upload image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $image = $_FILES['image'];

        if ($image['error'] !== UPLOAD_ERR_OK) {
            $error = "Error uploading image.";
        } elseif (!in_array($image['type'], $allowedTypes)) {
            $error = "Only JPG, PNG, and GIF images are allowed.";
        } else {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir))
                mkdir($uploadDir, 0755, true);

            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $filename = uniqid('prod_', true) . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($image['tmp_name'], $destination)) {
                // Insert into DB
                $stmt = $pdo->prepare("INSERT INTO products (user_id, title, description, price, category, image_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                $success = $stmt->execute([$userId, $title, $description, $price, $category, $destination]);

                if ($success) {
                    $_SESSION['success'] = "Product added successfully!";
                    header("Location: my_store.php");
                    exit();
                } else {
                    $error = "Failed to add product to database.";
                }
            } else {
                $error = "Failed to move uploaded file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Add New Product - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container mt-5" style="max-width: 600px;">
        <h2>Add New Product</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="title" class="form-label">Product Title</label>
                <input type="text" id="title" name="title" class="form-control" required
                    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control"
                    required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price ($)</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" required
                    value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="">Select category</option>
                    <option value="men" <?= (($_POST['category'] ?? '') === 'men') ? 'selected' : '' ?>>Men</option>
                    <option value="women" <?= (($_POST['category'] ?? '') === 'women') ? 'selected' : '' ?>>Women</option>
                    <option value="accessories" <?= (($_POST['category'] ?? '') === 'accessories') ? 'selected' : '' ?>>
                        Accessories</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*" required />
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</body>

</html>