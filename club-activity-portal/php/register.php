<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Invalid email format.";
        exit();
    }

    if (strlen($password) < 6) {
        echo "❌ Password must be at least 6 characters long.";
        exit();
    }

    // Check for existing email
    $emailCheckSql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $emailCheckResult = $conn->query($emailCheckSql);

    if ($emailCheckResult->num_rows > 0) {
        echo "❌ This email is already registered.";
        exit();
    }

    // Hash password & insert
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashedPassword', 'member')";

    if ($conn->query($sql) === TRUE) {
        echo "<h3>✅ Registration successful! Redirecting to login...</h3>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = '../index.html';
                }, 3000);
              </script>";
    } else {
        echo "❌ Error: " . $conn->error;
    }

    $conn->close();
}
?>
