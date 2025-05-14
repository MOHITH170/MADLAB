<?php
session_start();
include 'php/db.php';

$sql = "SELECT events.*, users.name AS creator_name FROM events 
        JOIN users ON events.created_by = users.id 
        ORDER BY event_date ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upcoming Events</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      padding: 40px;
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
    .event {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .event h2 {
      margin-top: 0;
    }
    .meta {
      font-size: 14px;
      color: #555;
    }
  </style>
</head>
<body>
<?php if (isset($_SESSION['role'])): ?>
  <nav>
    <a href="dashboard.php">🏠 Back to Dashboard</a>
    <a href="my_events.php">🎟️ My Events</a>
    <a href="php/logout.php">🚪 Logout</a>
  </nav>
<?php endif; ?>


  <h1>📅 Upcoming Club Events</h1>

  <?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="event">
      <h2>
  <a href="event.php?id=<?php echo $row['id']; ?>">
    <?php echo htmlspecialchars($row['title']); ?>
  </a>
</h2>

        <p class="meta">
          📍 Organized by: <?php echo htmlspecialchars($row['creator_name']); ?><br>
          📆 Date: <?php echo date('F j, Y', strtotime($row['event_date'])); ?>
        </p>
        <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No events available.</p>
  <?php endif; ?>

</body>
</html>
