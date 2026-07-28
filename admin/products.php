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
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body class="font-sans bg-[#f4f7f6] m-0 text-[#333]">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white min-h-screen shadow-md flex flex-col">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-xl font-bold text-primary m-0">RxStock Admin</h1>
            </div>
            <nav class="flex-1 p-4 flex flex-col gap-2">
                <a href="dashboard.php" class="text-gray-600 px-4 py-3 rounded-lg font-medium no-underline hover:bg-gray-50 transition-colors">Dashboard</a>
                <a href="products.php" class="bg-primary/10 text-primary px-4 py-3 rounded-lg font-medium no-underline">Manage Products</a>
            </nav>
            <div class="p-4 border-t border-gray-100">
                <a href="../auth/logout.php" class="text-red-500 font-medium no-underline flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-lg transition-colors">Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto h-screen">
            <header class="mb-8 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold m-0 mb-2">Manage Products</h2>
                    <p class="text-gray-500 m-0">Logged in as <?= htmlspecialchars($_SESSION['full_name']) ?></p>
                </div>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                <h3 class="font-bold text-lg m-0 mb-4">Add New Product</h3>
                <form method="POST" action="../db/product_requests.php" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input type="text" name="product_name" placeholder="Product Name" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    <input type="number" name="category_id" placeholder="Category ID" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    <input type="number" name="supplier_id" placeholder="Supplier ID" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    
                    <textarea name="description" placeholder="Description" class="col-span-1 md:col-span-3 p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border h-24 resize-y"></textarea>
                    
                    <input type="number" step="0.01" name="price" placeholder="Price" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    <input type="number" name="quantity" placeholder="Quantity" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    <input type="number" name="reorder_level" placeholder="Reorder Level" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                    
                    <input type="date" name="expiration_date" class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border text-gray-500">
                    
                    <div class="flex items-center gap-2 px-2">
                        <input type="checkbox" name="requires_prescription" id="rx_req" class="w-4 h-4 text-primary rounded border-gray-300 focus:ring-primary">
                        <label for="rx_req" class="text-sm font-medium text-gray-700">Requires Prescription</label>
                    </div>
                    
                    <div class="col-span-1 md:col-span-3 flex justify-end mt-2">
                        <button type="submit" name="add_product" class="bg-primary text-white border-none py-2 px-6 rounded-lg font-medium cursor-pointer transition-colors duration-200 hover:bg-primary-hover">Add Product</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-lg m-0 mb-4">Current Products</h3>
                <div class="overflow-x-auto">
                    <table id="product-table" class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50 rounded-tl-lg">ID</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Name</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Price</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Qty</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Reorder</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Expiration</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50">Rx</th>
                                <th class="p-3 border-b border-gray-200 text-sm font-semibold text-gray-600 bg-gray-50 rounded-tr-lg">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $products->fetch_assoc()): ?>
                                <tr data-id="<?= $row['product_id'] ?>" class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="p-3 text-sm text-gray-700 align-middle"><?= $row['product_id'] ?></td>
                                    <td class="cell-name p-3 text-sm font-medium text-gray-800 align-middle"><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td class="cell-price p-3 text-sm text-gray-700 align-middle">₱<?= $row['price'] ?></td>
                                    <td class="cell-quantity p-3 text-sm text-gray-700 align-middle"><?= $row['quantity'] ?></td>
                                    <td class="cell-reorder p-3 text-sm text-gray-700 align-middle"><?= $row['reorder_level'] ?></td>
                                    <td class="cell-expiration p-3 text-sm text-gray-700 align-middle"><?= $row['expiration_date'] ?: 'N/A' ?></td>
                                    <td class="cell-rx p-3 text-sm text-gray-700 align-middle">
                                        <?php if ($row['requires_prescription']): ?>
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">Yes</span>
                                        <?php else: ?>
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-semibold">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-3 text-sm align-middle">
                                        <div class="flex gap-2">
                                            <button onclick="editProduct(this)" class="bg-indigo-50 text-indigo-600 border border-indigo-200 py-1 px-3 rounded hover:bg-indigo-100 transition-colors text-xs font-medium cursor-pointer">Edit</button>
                                            <button onclick="deleteProduct(this)" class="bg-red-50 text-red-600 border border-red-200 py-1 px-3 rounded hover:bg-red-100 transition-colors text-xs font-medium cursor-pointer">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/product.js"></script>
</body>
</html>