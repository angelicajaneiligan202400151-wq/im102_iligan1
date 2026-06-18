<?php
include 'config.php';

$id = (int)($_GET['id'] ?? 0);

$product = $conn->query("
SELECT
    p.*,
    c.name AS category,
    s.name AS supplier
FROM products p
JOIN categories c ON p.category_id = c.id
JOIN suppliers s ON p.supplier_id = s.id
WHERE p.id = $id
")->fetch_assoc();

if(!$product){
    die("Product not found");
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $conn->query("
    DELETE FROM products
    WHERE id = $id
    ");

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h2>Delete Product</h2>

    <p><strong>Name:</strong>
        <?= htmlspecialchars($product['name']) ?>
    </p>

    <p><strong>Category:</strong>
        <?= htmlspecialchars($product['category']) ?>
    </p>

    <p><strong>Supplier:</strong>
        <?= htmlspecialchars($product['supplier']) ?>
    </p>

    <p><strong>Price:</strong>
        ₱<?= number_format($product['price'],2) ?>
    </p>

    <p><strong>Stock:</strong>
        <?= $product['stock'] ?>
    </p>

    <p style="color:red;">
        Are you sure you want to delete this product?
    </p>

    <form method="POST">

        <button type="submit" class="delete-btn">
            Yes, Delete
        </button>

        <a href="index.php" class="back-btn">
            Cancel
        </a>

    </form>

</div>

</body>
</html>