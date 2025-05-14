<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];

// Fetch all clubs
$sql = "SELECT * FROM clubs ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Clubs</title>
  <style>
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f4f4f4;
      margin: 0;
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
      display: flex;
      justify-content: space-around;
    }
    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }
    .container {
      padding: 30px;
    }
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card h2 {
      margin-top: 0;
    }
    .club {
      border-bottom: 1px solid #ddd;
      margin-bottom: 10px;
      padding-bottom: 10px;
    }
    .club h3 {
      margin: 0;
    }
    .club a {
      color: #003366;
      text-decoration: none;
    }
  </style>
</head>
<body>

<header>
  <h1>Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
  <p>Your role: <?php echo htmlspecialchars($role); ?></p>
</header>

<nav>
  <a href="my_events.php">🎟️ My Events</a>
  <a href="events.php">📅 All Events</a>
  <a href="all_clubs.php">🏛️ All Clubs</a> <!-- "All Clubs" link here -->
  <?php if ($role === 'admin'): ?>
    <a href="create_club.php">🏗️ Create Club</a>
    <a href="admin-dashboard.php">🛠️ Admin Dashboard</a>
  <?php endif; ?>
  <a href="php/logout.php">🚪 Logout</a>
</nav>

<div class="container">
  <div class="card">
    <h2>🏛️ Available Clubs</h2>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($club = $result->fetch_assoc()): ?>
        <div class="club">
          <h3><a href="club.php?id=<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></a></h3>
          <p><?php echo nl2br(htmlspecialchars($club['description'])); ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No clubs available at the moment.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
