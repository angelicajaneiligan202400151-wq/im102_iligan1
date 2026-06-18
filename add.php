<?php
include 'config.php';

$categories = $conn->query("
    SELECT id,name
    FROM categories
    ORDER BY name
");

$suppliers = $conn->query("
    SELECT id,name
    FROM suppliers
    ORDER BY name
");

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    $category_id = (int)$_POST['category_id'];
    $supplier_id = (int)$_POST['supplier_id'];

    if(
        !empty($name) &&
        !empty($description) &&
        $category_id > 0 &&
        $supplier_id > 0
    ){

        $sql = "
        INSERT INTO products
        (
            name,
            description,
            price,
            stock,
            category_id,
            supplier_id
        )
        VALUES
        (
            '$name',
            '$description',
            $price,
            $stock,
            $category_id,
            $supplier_id
        )
        ";

        $conn->query($sql);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Add Product</h2>

<form method="POST">

<p>Name</p>
<input type="text" name="name" required>

<p>Description</p>
<textarea name="description" required></textarea>

<p>Price</p>
<input type="number" step="0.01" name="price" required>

<p>Stock</p>
<input type="number" name="stock" required>

<p>Category</p>

<select name="category_id" required>

<option value="">
-- Select Category --
</option>

<?php while($cat = $categories->fetch_assoc()): ?>

<option value="<?= $cat['id'] ?>">
<?= $cat['name'] ?>
</option>

<?php endwhile; ?>

</select>

<p>Supplier</p>

<select name="supplier_id" required>

<option value="">

</option>

<?php while($sup = $suppliers->fetch_assoc()): ?>

<option value="<?= $sup['id'] ?>">
<?= $sup['name'] ?>
</option>

<?php endwhile; ?>

</select>

<br><br>

<button type="submit">
Save Product
</button>

<a href="index.php">
Back
</a>

</form>

</div>

</body>
</html>