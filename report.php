<?php
include 'config.php';

/* SUMMARY */

$summary = $conn->query("
SELECT
    COUNT(*) AS total_products,
    SUM(stock) AS total_stock,
    SUM(price * stock) AS total_value
FROM products
")->fetch_assoc();

/* CATEGORY REPORT */

$categoryReport = $conn->query("
SELECT
    c.name,
    COUNT(p.id) AS products,
    COALESCE(SUM(p.stock),0) AS total_stock,
    COALESCE(SUM(p.price * p.stock),0) AS total_value,
    COALESCE(AVG(p.price),0) AS average_price
FROM categories c
LEFT JOIN products p
    ON c.id = p.category_id
GROUP BY c.id, c.name
ORDER BY total_value DESC
");

/* SUPPLIER REPORT */

$supplierReport = $conn->query("
SELECT
    s.name,
    COUNT(p.id) AS products,
    COALESCE(SUM(p.stock),0) AS total_stock
FROM suppliers s
LEFT JOIN products p
    ON s.id = p.supplier_id
GROUP BY s.id, s.name
ORDER BY products DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h1>Inventory Reports</h1>

<a href="index.php" class="back-btn">
    Back to Products
</a>

<br><br>

<div class="cards">

    <div class="card">
        <h3>Total Products</h3>
        <p><?= $summary['total_products'] ?></p>
    </div>

    <div class="card">
        <h3>Total Stock</h3>
        <p><?= $summary['total_stock'] ?></p>
    </div>

    <div class="card">
        <h3>Total Inventory Value</h3>
        <p>
            ₱<?= number_format($summary['total_value'],2) ?>
        </p>
    </div>

</div>

<h2>Category Report</h2>

<table>

<tr>
    <th>Category</th>
    <th>Products</th>
    <th>Total Stock</th>
    <th>Total Value</th>
    <th>Average Price</th>
</tr>

<?php while($row = $categoryReport->fetch_assoc()): ?>

<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= $row['products'] ?></td>
    <td><?= $row['total_stock'] ?></td>
    <td>₱<?= number_format($row['total_value'],2) ?></td>
    <td>₱<?= number_format($row['average_price'],2) ?></td>
</tr>

<?php endwhile; ?>

</table>

<br>

<h2>Supplier Report</h2>

<table>

<tr>
    <th>Supplier</th>
    <th>Products</th>
    <th>Total Stock</th>
</tr>

<?php while($row = $supplierReport->fetch_assoc()): ?>

<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= $row['products'] ?></td>
    <td><?= $row['total_stock'] ?></td>
</tr>

<?php endwhile; ?>

</table>

<br><br>
</div>

</body>
</html>