<?php

//Goes to dashboard and shop if logged in, otherwise redirects to login page

require "includes/auth.php";

if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: customer/shop.php");
    }
} else {
    header("Location: auth/login.php");
}
exit();