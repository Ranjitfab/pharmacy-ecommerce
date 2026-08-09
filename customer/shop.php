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

$categories_lookup = [];
// Reset the pointer just in case, or just loop and populate
while ($cat = $categories->fetch_assoc()) {
    $categories_lookup[$cat['category_id']] = $cat['category_name'];
}
$categories->data_seek(0); // reset pointer for the filter buttons loop
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RxStock - Shop</title>
    <!-- FontAwesome CDN for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.css">
    <style>
        .cat-btn.active {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }

        .cat-btn:hover:not(.active) {
            border-color: #cbd5e1;
            color: #1f2937;
        }

        .drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .cart-drawer.open {
            right: 0;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body class="font-sans bg-[#f8fafc] m-0 text-gray-800">

    <!-- HEADER -->
    <header class="flex justify-between items-center p-4 md:px-8 border-b border-black/5 bg-[#f8fafc]">
        <div class="flex items-center gap-6">
            <div class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-pills bg-primary text-white p-1.5 rounded-md text-sm"></i> RxStock
            </div>
            <div class="flex gap-2">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/dashboard.php" class="bg-primary text-white border-none px-5 py-2.5 rounded-full font-semibold cursor-pointer flex items-center gap-2 transition-colors duration-200 no-underline hover:bg-primary-hover">Admin</a>
                <?php endif; ?>
                <a href="../auth/logout.php" class="bg-primary text-white border-none px-5 py-2.5 rounded-full font-semibold cursor-pointer flex items-center gap-2 transition-colors duration-200 no-underline hover:bg-primary-hover">Logout</a>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                <input type="text" id="search-input" class="bg-black/5 border-none py-2.5 pr-4 pl-10 rounded-full outline-none w-[250px] font-inherit text-sm" placeholder="Search products">
            </div>
            <button class="bg-primary text-white border-none px-5 py-2.5 rounded-full font-semibold cursor-pointer flex items-center gap-2 transition-colors duration-200 hover:bg-primary-hover" onclick="openCartDrawer()">
                <i class="fa-solid fa-cart-shopping"></i> Cart <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs" id="cart-badge">0</span>
            </button>
        </div>
    </header>

    <!-- MAIN -->
    <div class="py-10 px-8 max-w-[1400px] mx-auto">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="m-0 mb-2 text-3xl text-gray-800 font-bold">Shop</h1>
                <p class="m-0 text-gray-500 text-[15px]">Everyday health, in stock and ready.</p>
            </div>
            <div class="flex gap-3">
                <button class="cat-btn active bg-white border border-gray-200 px-4 py-2 rounded-full cursor-pointer text-gray-500 font-medium transition-colors duration-200 text-sm" onclick="filterProducts('all', this)">All</button>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <button class="cat-btn bg-white border border-gray-200 px-4 py-2 rounded-full cursor-pointer text-gray-500 font-medium transition-colors duration-200 text-sm" onclick="filterProducts('<?= $cat['category_id'] ?>', this)"><?= htmlspecialchars($cat['category_name']) ?></button>
                <?php endwhile; ?>
            </div>
        </div>

        <div id="product-grid" class="grid grid-cols-[repeat(auto-fill,minmax(280px,1fr))] gap-6">
            <?php while ($row = $products->fetch_assoc()): ?>
                <div class="card bg-white rounded-xl p-6 shadow-[0_2px_4px_rgba(0,0,0,0.02)] flex flex-col relative transition-all duration-200 border border-gray-200 hover:shadow-[0_10px_20px_rgba(0,0,0,0.05)] hover:-translate-y-0.5" data-category="<?= $row['category_id'] ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-10 h-10 bg-[#f8fafc] rounded-lg flex justify-center items-center text-primary text-lg">
                            <i class="fa-solid fa-file-prescription"></i>
                        </div>
                        <?php
                        $catName = isset($categories_lookup[$row['category_id']]) ? $categories_lookup[$row['category_id']] : 'General';
                        ?>
                        <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-semibold"><?= htmlspecialchars($catName) ?></span>
                    </div>

                    <h3 class="m-0 mb-1 text-lg text-gray-800 font-bold"><?= htmlspecialchars($row['product_name']) ?></h3>
                    <p class="text-[13px] text-gray-500 m-0 mb-4">Unit details</p>

                    <div class="flex items-start gap-3 text-[13px] font-medium text-gray-800 mb-6">
                        <?php
                        $qty = $row['quantity'];
                        $dotClass = $qty >= 20 ? 'border-primary/20 bg-primary/5' : ($qty > 0 ? 'border-yellow-500/30 bg-yellow-50' : 'border-gray-200 bg-gray-100');
                        $fillClass = $qty >= 20 ? 'bg-primary' : ($qty > 0 ? 'bg-yellow-500' : 'bg-transparent');
                        $stockText = $qty > 0 ? 'In stock' : 'Out of stock';
                        if ($qty > 0 && $qty < 20) $stockText = 'Low stock';

                        $fillPercentage = $qty >= 20 ? 100 : ($qty / 20) * 100;
                        ?>
                        <div class="w-5 h-5 rounded-[5px] flex items-end overflow-hidden relative border <?= $dotClass ?>">
                            <div class="w-full absolute bottom-0 left-0 transition-all duration-300 <?= $fillClass ?>" style="height: <?= $fillPercentage ?>%;"></div>
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span><?= $qty ?> units</span>
                            <span class="text-gray-500 text-xs font-normal mt-1"><?= $stockText ?></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-auto">
                        <div class="text-lg font-bold text-gray-800">₱<?= number_format($row['price'], 2) ?></div>
                        <button class="bg-transparent border border-primary text-primary px-4 py-2 rounded-full font-semibold cursor-pointer transition-colors duration-200 text-sm hover:not(:disabled):bg-primary hover:not(:disabled):text-white disabled:border-gray-200 disabled:text-gray-500 disabled:cursor-not-allowed" onclick="addToCart(<?= $row['product_id'] ?>)" <?= $qty <= 0 ? 'disabled' : '' ?>>
                            Add to cart
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- CART DRAWER -->
    <div class="drawer-overlay fixed top-0 left-0 w-screen h-screen bg-black/20 opacity-0 invisible transition-all duration-300 z-[99] backdrop-blur-[2px]" id="drawer-overlay" onclick="closeCartDrawer()"></div>
    <div class="cart-drawer fixed top-0 -right-[420px] w-[400px] max-w-[100vw] h-screen bg-white z-[100] shadow-[-4px_0_24px_rgba(0,0,0,0.1)] transition-all duration-300 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col" id="cart-drawer">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="m-0 text-lg font-bold">Your cart</h2>
            <button class="bg-transparent border-none text-xl text-gray-500 cursor-pointer transition-colors duration-200 hover:text-gray-800" onclick="closeCartDrawer()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-4" id="drawer-body">
            <!-- Items injected here by JS -->
            <div style="text-align:center; padding:40px; color:#9ca3af;">Loading cart...</div>
        </div>
        <div class="p-6 border-t border-gray-200 bg-white">
            <div class="flex justify-between mb-4 font-bold text-base text-gray-800">
                <span>Subtotal</span>
                <span class="text-lg">₱<span id="drawer-subtotal">0.00</span></span>
            </div>
            <button class="w-full bg-primary text-white border-none p-4 rounded-lg font-semibold text-base cursor-pointer transition-colors duration-200 hover:bg-primary-hover" onclick="location.href='checkout.php'">Go to checkout</button>
        </div>
    </div>

    <!-- TOAST CONTAINER -->
    <div id="toast-container" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[1000] flex flex-col gap-2.5"></div>

    <script src="../assets/js/jquery.min.js"></script>
    <script>
        // Tracks the active category so search and category filtering
        // combine correctly instead of one overriding the other.
        var currentCategory = 'all';

        function filterProducts(categoryId, btn) {
            $('.cat-btn').removeClass('active');
            $(btn).addClass('active');
            currentCategory = categoryId;
            applyFilters();
        }

        function applyFilters() {
            var searchTerm = $('#search-input').val().trim().toLowerCase();

            $('.card').each(function() {
                var card = $(this);
                var matchesCategory = currentCategory === 'all' || card.data('category') == currentCategory;
                var productName = card.find('h3').text().toLowerCase();
                var matchesSearch = searchTerm === '' || productName.indexOf(searchTerm) !== -1;

                if (matchesCategory && matchesSearch) {
                    card.show();
                } else {
                    card.hide();
                }
            });
        }

        // Fetch cart immediately on load to get the badge count
        $(document).ready(function() {
            refreshCartDrawer();

            $('#search-input').on('input', applyFilters);
        });
    </script>
    <script src="../assets/js/cart.js"></script>
</body>

</html>