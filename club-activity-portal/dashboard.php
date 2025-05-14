<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Fetch upcoming events
$sql = "SELECT * FROM events WHERE event_date >= ? ORDER BY event_date ASC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
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
    .event {
      border-bottom: 1px solid #ddd;
      margin-bottom: 10px;
      padding-bottom: 10px;
    }
    .event h3 {
      margin: 0;
    }
    .event a {
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
  <a href="all_clubs.php">🏛️ All Clubs</a>
  <?php if ($role === 'admin'): ?>
    <a href="create_club.php">🏗️ Create Club</a>
    <a href="admin-dashboard.php">🛠️ Admin Dashboard</a>
  <?php endif; ?>
  <a href="php/logout.php">🚪 Logout</a>
</nav>

<div class="container">
  <div class="card">
    <h2>📢 Upcoming Events</h2>
    <?php if ($result->num_rows > 0): ?>
      <?php while ($event = $result->fetch_assoc()): ?>
        <div class="event">
          <h3><a href="event.php?id=<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['title']); ?></a></h3>
          <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
          <p><?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 100))) . '...'; ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No upcoming events at the moment.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
