<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id'])) {
  // Redirect to login page if not logged in
  header("Location: login.html");
  exit;
}

$user_id = $_SESSION['user_id'];

// Fetch events the user registered for
$sql = "SELECT events.* FROM events 
        JOIN event_registrations ON events.id = event_registrations.event_id 
        WHERE event_registrations.user_id = ? 
        ORDER BY events.event_date ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Registered Events</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
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

    h1 {
      text-align: center;
      color: #333;
    }
    .event {
      background: white;
      padding: 20px;
      border-radius: 12px;
      margin: 20px auto;
      max-width: 700px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .event h2 {
      margin-top: 0;
    }
    .event p {
      color: #555;
    }
  </style>
</head>
<body>
<nav>
  <a href="dashboard.php">🏠 Back to Dashboard</a>
  <a href="events.php">📅 All Events</a>
  <a href="php/logout.php">🚪 Logout</a>
</nav>

<h1>📋 My Registered Events</h1>

<?php if ($result->num_rows > 0): ?>
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="event">
      <h2><a href="event.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h2>
      <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['event_date'])); ?></p>
      <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
    </div>
  <?php endwhile; ?>
<?php else: ?>
  <p style="text-align: center;">You haven’t registered for any events yet.</p>
<?php endif; ?>

</body>
</html>
