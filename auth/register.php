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
    <title>Register</title>
</head>
<body>
    <h1>Create an Account</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="contact_number" placeholder="Contact Number">
        <input type="text" name="address" placeholder="Address">
        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>