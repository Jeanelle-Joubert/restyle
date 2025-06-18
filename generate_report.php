<?php
session_start();
require 'db.php'; // adjust path if needed

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

$format = $_GET['format'] ?? 'html';

// Queries
$conn = new mysqli("localhost", "root", "", "restyle_db");
$totalSalesQuery = "SELECT SUM(total_amount) AS total_sales FROM orders";
$topProductsQuery = "
    SELECT p.title, SUM(oi.quantity) AS total_sold
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY p.id
    ORDER BY total_sold DESC
    LIMIT 10
";

$totalSales = $conn->query($totalSalesQuery)->fetch_assoc()['total_sales'];
$topProducts = $conn->query($topProductsQuery);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sales_report.csv"');

    $output = fopen("php://output", "w");
    fputcsv($output, ['Report Generated:', date('Y-m-d H:i:s')]);
    fputcsv($output, []);
    fputcsv($output, ['Total Sales', $totalSales]);
    fputcsv($output, []);
    fputcsv($output, ['Top Products']);
    fputcsv($output, ['Product', 'Units Sold']);

    while ($row = $topProducts->fetch_assoc()) {
        fputcsv($output, [$row['title'], $row['total_sold']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">
    <h1>Sales Report</h1>
    <p><strong>Total Sales:</strong> R<?= number_format($totalSales, 2) ?></p>
    <h2>Top Products</h2>
    <table class="table table-bordered">
        <thead>
            <tr><th>Product</th><th>Quantity Sold</th></tr>
        </thead>
        <tbody>
            <?php while ($row = $topProducts->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= $row['total_sold'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</body>
</html>
