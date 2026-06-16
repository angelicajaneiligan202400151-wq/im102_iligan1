<?php
include 'config.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Categories Dropdown
$categories = $conn->query("
    SELECT id, name
    FROM categories
    ORDER BY name
");

// Main Query
$sql = "
SELECT
    p.id,
    p.name AS product_name,
    p.description,
    p.price,
    p.stock,
    p.category_id,
    p.supplier_id,
    p.created_at,
    c.name AS category,
    s.name AS supplier
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN suppliers s ON p.supplier_id = s.id
WHERE 1=1
";

if (!empty($search)) {
    $search_safe = $conn->real_escape_string($search);

    $sql .= "
    AND (
        p.name LIKE '%$search_safe%'
        OR p.description LIKE '%$search_safe%'
    )";
}

if (!empty($category)) {
    $category_safe = (int)$category;
    $sql .= " AND c.id = $category_safe";
}

$sql .= " ORDER BY p.id ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Statistics Query
$statsSql = "
SELECT
    COUNT(*) AS total_products,
    SUM(stock) AS total_stock,
    SUM(price * stock) AS total_value,
    SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS low_stock
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE 1=1
";

if (!empty($search)) {
    $statsSql .= "
    AND (
        p.name LIKE '%$search_safe%'
        OR p.description LIKE '%$search_safe%'
    )";
}

if (!empty($category)) {
    $statsSql .= " AND c.id = $category_safe";
}

$stats = $conn->query($statsSql)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Product Inventory</h1>

    <a href="add.php" class="add-btn">
        + Add Product
    </a>

    <br><br>

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Search Product"
            value="<?= htmlspecialchars($search) ?>"
        >

        <select name="category">

            <option value="">
                All Categories
            </option>

            <?php
            $categories->data_seek(0);

            while($cat = $categories->fetch_assoc()):
            ?>

            <option
                value="<?= $cat['id'] ?>"
                <?= ($category == $cat['id']) ? 'selected' : '' ?>
            >
                <?= $cat['name'] ?>
            </option>

            <?php endwhile; ?>

        </select>

        <button type="submit">
            Filter
        </button>

        <a href="index.php">
            <button type="button">
                Reset
            </button>
        </a>

    </form>

    <!-- SUMMARY CARDS -->

    <div class="cards">

        <div class="card">
            <h3>Total Products</h3>
            <p><?= $stats['total_products'] ?? 0 ?></p>
        </div>

        <div class="card">
            <h3>Total Stock</h3>
            <p><?= $stats['total_stock'] ?? 0 ?></p>
        </div>

        <div class="card">
            <h3>Total Value</h3>
            <p>
                ₱<?= number_format($stats['total_value'] ?? 0, 2) ?>
            </p>
        </div>

        <div class="card">
            <h3>Low Stock</h3>
            <p><?= $stats['low_stock'] ?? 0 ?></p>
        </div>

    </div>

    <!-- PRODUCT TABLE -->

    <table>

        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>

        <?php while($row = $result->fetch_assoc()): ?>

        <tr class="<?= ($row['stock'] < 20) ? 'low-stock' : '' ?>">

            <td><?= $row['id'] ?></td>

            <td><?= htmlspecialchars($row['product_name']) ?></td>

            <td><?= htmlspecialchars($row['description']) ?></td>

            <td>
                ₱<?= number_format($row['price'], 2) ?>
            </td>

            <td><?= $row['stock'] ?></td>

            <td><?= htmlspecialchars($row['category']) ?></td>

            <td><?= htmlspecialchars($row['supplier']) ?></td>

            <td><?= $row['created_at'] ?></td>

            <td>
    <a href="edit.php?id=<?= $row['id'] ?>" class="btn edit-btn">
        Edit
    </a>

    <a href="delete.php?id=<?= $row['id'] ?>" class="btn delete-btn"
       onclick="return confirm('Delete this product?')">
        Delete
    </a>
</td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>

<?php
$conn->close();
?>