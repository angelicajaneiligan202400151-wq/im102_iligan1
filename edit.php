<?php
include 'config.php';

$id = (int)$_GET['id'];

$product = $conn->query("
SELECT *
FROM products
WHERE id = $id
")->fetch_assoc();

if(!$product){
    die("Product not found");
}

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

    $sql = "
    UPDATE products
    SET
        name='$name',
        description='$description',
        price=$price,
        stock=$stock,
        category_id=$category_id,
        supplier_id=$supplier_id
    WHERE id=$id
    ";

    $conn->query($sql);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Edit Product</h2>

<form method="POST">

<p>Name</p>

<input
type="text"
name="name"
value="<?= $product['name'] ?>"
required
>

<p>Description</p>

<textarea
name="description"
required
><?= $product['description'] ?></textarea>

<p>Price</p>

<input
type="number"
step="0.01"
name="price"
value="<?= $product['price'] ?>"
required
>

<p>Stock</p>

<input
type="number"
name="stock"
value="<?= $product['stock'] ?>"
required
>

<p>Category</p>

<select name="category_id" required>

<?php while($cat = $categories->fetch_assoc()): ?>

<option
value="<?= $cat['id'] ?>"
<?= $cat['id'] == $product['category_id']
? 'selected'
: '' ?>
>
<?= $cat['name'] ?>
</option>

<?php endwhile; ?>

</select>

<p>Supplier</p>

<select name="supplier_id" required>

<?php while($sup = $suppliers->fetch_assoc()): ?>

<option
value="<?= $sup['id'] ?>"
<?= $sup['id'] == $product['supplier_id']
? 'selected'
: '' ?>
>
<?= $sup['name'] ?>
</option>

<?php endwhile; ?>

</select>

<br><br>

<button type="submit">
Update Product
</button>

<a href="index.php">
Back
</a>

</form>

</div>

</body>
</html>