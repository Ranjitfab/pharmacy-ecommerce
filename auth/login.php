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
    <title>Login</title>
</head>

<body>
    <h1>Login</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>

    <p>No account yet? <a href="register.php">Register here</a></p>
</body>

</html>