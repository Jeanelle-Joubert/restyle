<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Not logged in or not admin - redirect to login
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - ReStyle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="#">ReStyle Admin</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($_SESSION['user']['username']) ?>
                </span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container mt-5">
        <h1>Admin Dashboard</h1>
        <p>This is the admin panel. Here you can manage the website, generate reports, etc.</p>

        <hr>
        <h2 class="mt-4">Generate Sales Report</h2>
        <form action="generate_report.php" method="get" class="row g-3">
            <div class="col-md-4">
                <label for="format" class="form-label">Select Format</label>
                <select name="format" id="format" class="form-select" required>
                    <option value="html">View in Browser</option>
                    <option value="csv">Download CSV</option>
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-danger mt-2">Generate Report</button>
            </div>
        </form>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
