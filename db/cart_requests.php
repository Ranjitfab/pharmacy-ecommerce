<?php
require "../includes/auth.php";
require "db.php";
requireLogin();

$myDB = new myDB();
$userId = $_SESSION['user_id'];

// Creates or gets the cart of a user
function getOrCreateCartId($myDB, $userId)
{
    $myDB->select('cart', '*', ['user_id' => $userId]);

    if ($myDB->res->num_rows > 0) {
        $cart = $myDB->res->fetch_assoc();
        return $cart['cart_id'];
    }

    $myDB->insert('cart', ['user_id' => $userId]);

    $myDB->select('cart', '*', ['user_id' => $userId]);
    $cart = $myDB->res->fetch_assoc();
    return $cart['cart_id'];
}

// Add to cart AJAX 
if (isset($_POST['add_to_cart'])) {
    $cartId = getOrCreateCartId($myDB, $userId);
    $productId = $_POST['product_id'];
    $qtyToAdd = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $myDB->select('cart_items', '*', ['cart_id' => $cartId, 'product_id' => $productId]);

    //If item is already in cart, increase quantity instead of creating another row
    if ($myDB->res->num_rows > 0) {
        $existing = $myDB->res->fetch_assoc();
        $newQty = $existing['quantity'] + $qtyToAdd;

        $myDB->update(
            'cart_items',
            ['quantity' => $newQty],
            ['cart_item_id' => $existing['cart_item_id']]
        );
    } else {
        $myDB->insert('cart_items', [
            'cart_id'    => $cartId,
            'product_id' => $productId,
            'quantity'   => $qtyToAdd,
        ]);
    }

    echo "success";
    exit();
}

// Update quantity using +/-
if (isset($_POST['update_quantity'])) {
    $newQty = (int)$_POST['quantity'];

    if ($newQty <= 0) {
        $myDB->delete('cart_items', ['cart_item_id' => $_POST['cart_item_id']]);
        echo "removed";
        exit();
    }

    $myDB->update(
        'cart_items',
        ['quantity' => $newQty],
        ['cart_item_id' => $_POST['cart_item_id']]
    );

    echo "success";
    exit();
}

// ------------------------------------------------------------
// REMOVE ITEM - AJAX
// ------------------------------------------------------------
if (isset($_POST['remove_item'])) {
    $myDB->delete('cart_items', ['cart_item_id' => $_POST['cart_item_id']]);
    echo "success";
    exit();
}
<<<<<<< HEAD
=======

// ------------------------------------------------------------
// FETCH CART - AJAX (returns JSON for slide-out drawer)
// ------------------------------------------------------------
if (isset($_POST['fetch_cart'])) {
    $cartId = getOrCreateCartId($myDB, $userId);
    $myDB->select('cart_items', '*', ['cart_id' => $cartId]);
    
    $html = "";
    $total = 0;
    $count = 0;

    if ($myDB->res->num_rows > 0) {
        $cartItems = $myDB->res->fetch_all(MYSQLI_ASSOC);
        foreach ($cartItems as $item) {
            $productDB = new myDB();
            $productDB->select('products', '*', ['product_id' => $item['product_id']]);
            $product = $productDB->res->fetch_assoc();
            
            $subtotal = $product['price'] * $item['quantity'];
            $total += $subtotal;
            $count += $item['quantity'];
            
            $html .= '<div class="drawer-item flex items-center gap-4 pb-4 border-b border-gray-200 last:border-b-0 last:pb-0" data-cart-item-id="'.$item['cart_item_id'].'">';
            $html .= '<div class="w-11 h-11 bg-[#f8fafc] rounded-lg flex justify-center items-center text-primary text-lg border border-gray-200"><i class="fa-solid fa-prescription-bottle"></i></div>';
            $html .= '<div class="flex-1">';
            $html .= '<h4 class="m-0 mb-1 text-sm text-gray-800">'.htmlspecialchars($product['product_name']).'</h4>';
            $html .= '<p class="m-0 text-[13px] text-gray-500">₱'.number_format($product['price'], 2).' each</p>';
            $html .= '</div>';
            $html .= '<div class="flex items-center gap-1 bg-[#f8fafc] p-1 rounded-lg border border-gray-200">';
            $html .= '<button class="bg-transparent border-none py-1 px-2 cursor-pointer text-gray-500 font-bold transition-colors duration-200 hover:text-gray-800" onclick="changeQuantity(this, -1)">-</button>';
            $html .= '<span class="text-[13px] font-semibold min-w-[16px] text-center">'.$item['quantity'].'</span>';
            $html .= '<button class="bg-transparent border-none py-1 px-2 cursor-pointer text-gray-500 font-bold transition-colors duration-200 hover:text-gray-800" onclick="changeQuantity(this, 1)">+</button>';
            $html .= '</div>';
            $html .= '</div>';
        }
    } else {
        $html = '<div style="text-align:center; padding:40px; color:#9ca3af;">Your cart is empty.</div>';
    }

    echo json_encode([
        'html' => $html,
        'total' => number_format($total, 2),
        'count' => $count
    ]);
    exit();
}
>>>>>>> 270316e99ab4e14fb3342b04cec5a6abd8dbf750
