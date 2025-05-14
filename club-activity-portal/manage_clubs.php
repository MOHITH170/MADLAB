<?php
session_start();
include 'php/db.php';

if ($_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

$admin_id = $_SESSION['user_id'];

// Delete club
if (isset($_GET['delete'])) {
  $club_id = intval($_GET['delete']);
  $conn->query("DELETE FROM clubs WHERE id = $club_id AND created_by = $admin_id");
}

$result = $conn->query("SELECT * FROM clubs WHERE created_by = $admin_id");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manage Clubs</title>
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
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
    }

    .top-actions {
      text-align: right;
      margin-bottom: 20px;
    }

    .top-actions a {
      background-color: #28a745;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
    }

    .club-card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      transition: transform 0.2s;
    }

    .club-card:hover {
      transform: translateY(-5px);
    }

    .club-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .club-header h2 {
      margin: 0;
      color: #003366;
    }

    .club-description {
      margin-top: 10px;
      color: #333;
    }

    .delete-btn {
      color: red;
      font-size: 14px;
      text-decoration: none;
    }

    .delete-btn:hover {
      text-decoration: underline;
    }

    .back-link {
      display: block;
      margin-top: 30px;
      text-align: center;
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <h1>📋 Manage Your Clubs</h1>
</header>

<nav>
  <a href="dashboard.php">Dashboard</a>
  <a href="create_club.php">Create Club</a>
  <a href="php/logout.php">Logout</a>
</nav>

<div class="container">

  <div class="top-actions">
    <a href="create_club.php">➕ Create New Club</a>
  </div>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($club = $result->fetch_assoc()): ?>
      <div class="club-card">
        <div class="club-header">
          <h2><?php echo htmlspecialchars($club['name']); ?></h2>
          <a class="delete-btn" href="?delete=<?php echo $club['id']; ?>" onclick="return confirm('Are you sure you want to delete this club?')">🗑️ Delete</a>
        </div>
        <div class="club-description">
          <?php echo nl2br(htmlspecialchars($club['description'])); ?>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No clubs created yet.</p>
  <?php endif; ?>

  <a class="back-link" href="dashboard.php">← Back to Dashboard</a>

</div>

</body>
</html>
