<?php
session_start();

// Hardcoded user credentials for testing
$hardcoded_username = "admin";
$hardcoded_password = "password123"; // In real applications, use hashed passwords!

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username === $hardcoded_username && $password === $hardcoded_password) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        // Set a cookie (optional, for simple authentication)
        setcookie("auth", "loggedin", time() + 3600, "/"); // Expires in 1 hour

        header("Location: index.php"); // Redirect to the mailbox page
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>