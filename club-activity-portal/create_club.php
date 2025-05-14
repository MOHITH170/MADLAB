<?php
session_start();
include 'php/db.php';

if ($_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $description = $_POST['description'];
  $created_by = $_SESSION['user_id'];

  $stmt = $conn->prepare("INSERT INTO clubs (name, description, created_by) VALUES (?, ?, ?)");
  $stmt->bind_param("ssi", $name, $description, $created_by);
  $stmt->execute();

  header("Location: manage_clubs.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Club</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f8fb;
    }

    header {
      background-color: #003366;
      color: white;
      padding: 20px;
      text-align: center;
    }

    nav {
      background-color: #0055aa;
      padding: 15px;
      text-align: center;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      margin: 0 20px;
    }

    .container {
      max-width: 600px;
      background: white;
      margin: 40px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #003366;
    }

    input[type="text"], textarea {
      width: 100%;
      padding: 12px;
      margin: 15px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    textarea {
      resize: vertical;
      min-height: 120px;
    }

    button {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      width: 100%;
    }

    button:hover {
      background-color: #0056b3;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #007bff;
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <h1>Create a New Club</h1>
</header>

<nav>
  <a href="dashboard.php">Dashboard</a>
  <a href="manage_clubs.php">Manage Clubs</a>
  <a href="php/logout.php">Logout</a>
</nav>

<div class="container">
  <h2>🛠️ Club Details</h2>
  <form method="POST">
    <input type="text" name="name" placeholder="Club Name" required>
    <textarea name="description" placeholder="Club Description (optional)"></textarea>
    <button type="submit">Create Club</button>
  </form>

  <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
