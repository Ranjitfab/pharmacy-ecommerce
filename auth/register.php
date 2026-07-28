<?php
require "../includes/auth.php";
require "../db/db.php";

$error = "";

if (isset($_POST['register'])) {
    $myDB = new myDB();

    // Checks if the email is already taken
    $myDB->select('users', '*', ['email' => $_POST['email']]);
    if ($myDB->res->num_rows > 0) {
        $error = "That email is already registered.";
    } else {
        $myDB->insert('users', [
            'first_name'     => $_POST['first_name'],
            'last_name'      => $_POST['last_name'],
            'email'          => $_POST['email'],
            'password'       => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'role'           => 'customer', // registration always creates a customer, never an admin
            'contact_number' => $_POST['contact_number'],
            'address'        => $_POST['address'],
        ]);

        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body class="font-sans bg-[#f4f7f6] flex justify-center items-center min-h-screen m-0 p-5">
    <div class="bg-white p-10 rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.05)] w-full max-w-[500px]">
        <h1 class="mt-0 text-2xl text-[#333] text-center mb-6 font-bold">Create an Account</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-600 p-2.5 rounded-lg text-sm text-center mb-4 border border-red-200"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="flex flex-col gap-4">
            <div class="flex gap-4">
                <input type="text" name="first_name" placeholder="First Name" required class="flex-1 p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
                <input type="text" name="last_name" placeholder="Last Name" required class="flex-1 p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            </div>
            <input type="email" name="email" placeholder="Email" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <input type="password" name="password" placeholder="Password" required class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <input type="text" name="contact_number" placeholder="Contact Number" class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <input type="text" name="address" placeholder="Address" class="p-3 border border-[#ddd] rounded-lg text-sm outline-none transition-colors duration-200 focus:border-primary w-full box-border">
            <button type="submit" name="register" class="bg-primary text-white border-none p-3 rounded-lg text-base font-medium cursor-pointer transition-colors duration-200 mt-2 hover:bg-primary-hover">Register</button>
        </form>

        <p class="text-center text-sm text-[#666] mt-5 mb-0">Already have an account? <a href="login.php" class="text-primary no-underline hover:underline">Login here</a></p>
    </div>
</body>
</html>