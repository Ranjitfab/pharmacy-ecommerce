<?php
require "../includes/auth.php";
require "../db/db.php";
requireLogin();

$myDB = new myDB();

$myDB->select('products', '*');
$products = $myDB->res;

$myDB2 = new myDB(); // separate instance so its result doesn't overwrite $products
$myDB2->select('categories', '*');
$categories = $myDB2->res;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop</title>
    <style>
        #product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .card { border: 1px solid #ccc; border-radius: 8px; padding: 12px; }
        .badge { background: #eee; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; }
    </style>
</head>
<body>
    <h1>Shop</h1>
    <p>
        Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> |
        <a href="cart.php">View Cart</a> |
        <a href="../auth/logout.php">Logout</a>
    </p>

    <label for="category-filter">Filter by category:</label>
    <select id="category-filter" onchange="filterProducts()">
        <option value="all">All Categories</option>
        <?php while ($cat = $categories->fetch_assoc()): ?>
            <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
        <?php endwhile; ?>
    </select>

    <div id="product-grid">
        <?php while ($row = $products->fetch_assoc()): ?>
            <div class="card" data-category="<?= $row['category_id'] ?>">
                <h3><?= htmlspecialchars($row['product_name']) ?></h3>
                <?php if ($row['requires_prescription']): ?>
                    <span class="badge">Prescription required</span>
                <?php endif; ?>
                <p>₱<?= number_format($row['price'], 2) ?></p>
                <p>
                    <?php if ($row['quantity'] > 0): ?>
                        In stock: <?= $row['quantity'] ?>
                    <?php else: ?>
                        <strong>Out of stock</strong>
                    <?php endif; ?>
                </p>
                <button
                    onclick="addToCart(<?= $row['product_id'] ?>)"
                    <?= $row['quantity'] <= 0 ? 'disabled' : '' ?>>
                    Add to Cart
                </button>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        // Client-side filter - no need for an extra AJAX round trip
        // since all products are already rendered on the page.
        function filterProducts() {
            var selected = document.getElementById("category-filter").value;
            var cards = document.querySelectorAll("#product-grid .card");

            cards.forEach(function (card) {
                if (selected === "all" || card.dataset.category === selected) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }
    </script>
    <script src="../assets/js/cart.js"></script>
</body>
</html>