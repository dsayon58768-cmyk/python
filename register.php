<?php
// register.php - User registration for the Snake Game

require_once "db.php";

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm_password"] ?? "";

    // Validation
    if ($username === "" || $email === "" || $password === "" || $confirm === "") {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    // Check for existing username/email
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or email is already registered.";
        }
    }

    // Insert new user
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Snake Game</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background: #1e1e2f;
        color: #eee;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .container {
        background: #2a2a3d;
        padding: 30px 40px;
        border-radius: 10px;
        width: 320px;
        box-shadow: 0 0 20px rgba(0,255,0,0.2);
    }
    h2 {
        text-align: center;
        color: #7CFC00;
    }
    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: none;
        border-radius: 5px;
        box-sizing: border-box;
    }
    button {
        width: 100%;
        padding: 10px;
        background: #7CFC00;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        margin-top: 10px;
    }
    button:hover {
        background: #66c800;
    }
    .error {
        color: #ff5555;
        font-size: 14px;
        margin: 5px 0;
    }
    .success {
        color: #7CFC00;
        text-align: center;
        font-weight: bold;
    }
    a {
        color: #7CFC00;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="container">
    <h2>🐍 Snake Game</h2>
    <h3 style="text-align:center;">Register</h3>

    <?php if ($success): ?>
        <p class="success">Registration successful! You can now <a href="login.php">log in</a>.</p>
    <?php else: ?>
        <?php foreach ($errors as $error): ?>
            <p class="error">⚠ <?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>

        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="password" name="password" placeholder="Password">
            <input type="password" name="confirm_password" placeholder="Confirm Password">
            <button type="submit">Register</button>
        </form>
        <p style="text-align:center; margin-top:10px;">Already have an account? <a href="login.php">Login</a></p>
    <?php endif; ?>
</div>
</body>
</html>
