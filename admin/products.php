<?php
require "../includes/auth.php";
require "../db/db.php";
requireAdmin();

$myDB = new myDB();
$myDB->select('products', '*');
$products = $myDB->res;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
</head>

<body>
    <h1>Manage Products</h1>
    <p>Logged in as <?= htmlspecialchars($_SESSION['full_name']) ?> | <a href="../auth/logout.php">Logout</a></p>

    <!-- ADD PRODUCT - plain form POST, page reload -->
    <h2>Add Product</h2>
    <form method="POST" action="../db/product_requests.php">
        <input type="text" name="product_name" placeholder="Product Name" required>
        <input type="number" name="category_id" placeholder="Category ID" required>
        <input type="number" name="supplier_id" placeholder="Supplier ID" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="number" name="quantity" placeholder="Quantity" required>
        <input type="number" name="reorder_level" placeholder="Reorder Level" required>
        <input type="date" name="expiration_date">
        <label><input type="checkbox" name="requires_prescription"> Requires Prescription</label>
        <button type="submit" name="add_product">Add Product</button>
    </form>

    <!-- PRODUCT TABLE - edit/delete use AJAX in product.js -->
    <h2>Current Products</h2>
    <table id="product-table" border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Reorder Level</th>
                <th>Expiration</th>
                <th>Rx Required</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $products->fetch_assoc()): ?>
                <tr data-id="<?= $row['product_id'] ?>">
                    <td><?= $row['product_id'] ?></td>
                    <td class="cell-name"><?= htmlspecialchars($row['product_name']) ?></td>
                    <td class="cell-price"><?= $row['price'] ?></td>
                    <td class="cell-quantity"><?= $row['quantity'] ?></td>
                    <td class="cell-reorder"><?= $row['reorder_level'] ?></td>
                    <td class="cell-expiration"><?= $row['expiration_date'] ?></td>
                    <td class="cell-rx"><?= $row['requires_prescription'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <button onclick="editProduct(this)">Edit</button>
                        <button onclick="deleteProduct(this)">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <script src="../assets/js/product.js"></script>
</body>

</html>