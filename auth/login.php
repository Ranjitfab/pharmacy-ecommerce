<?php
require "../includes/auth.php";
require "../db/db.php";
/*
INSERT INTO users (first_name, last_name, email, password, role)
VALUES ('Admin', 'User', 'admin@pharmacy.com', '$2b$10$esb8aJ2mLm2hXvmSR3kgROvt3uSeX9XV/LUoHr0t3VOgCaIvQsDom', 'admin');
*/
$error = "";

if (isset($_POST['login'])) {
    $myDB = new myDB();
    $myDB->select('users', '*', ['email' => $_POST['email']]);

    if ($myDB->res->num_rows === 1) {
        $user = $myDB->res->fetch_assoc();
        //Checks if password is correct
        if (password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['role']      = $user['role'];
            $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../customer/shop.php");
            }
            exit();
        }
    }

    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>

<body class="font-sans bg-[#f4f7f6] flex justify-center items-center min-h-screen m-0 p-5">
    <div class="bg-white p-10 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] w-full max-w-[450px]">
        <h1 class="mt-0 text-2xl text-[#333] text-center mb-6 font-bold">Login</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-600 p-2.5 rounded-lg text-sm text-center mb-4 border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="flex flex-col gap-4">
            <input type="email" name="email" placeholder="Email" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <input type="password" name="password" placeholder="Password" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <button type="submit" name="login" class="bg-primary text-white border-none p-3 rounded-lg text-base font-medium cursor-pointer transition-colors duration-200 mt-2 hover:bg-primary-hover">Login</button>
        </form>

        <p class="text-center text-sm text-[#666] mt-5 mb-0">No account yet? <a href="register.php" class="text-primary no-underline hover:underline">Register here</a></p>
    </div>
</body>

</html>