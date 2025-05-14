<?php
include 'php/db.php';
session_start();  // Make sure to start the session

// Save the current URL for redirect after login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];  // Save the current event page URL
    header("Location: index.html");  // Redirect to login page
    exit();
}

if (!isset($_GET['id'])) {
  echo "Invalid event ID.";
  exit;
}

$event_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? null;

$register_success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
  if (!$user_id) {
    $register_success = "Please log in to register.";
  } else {
    // Check if already registered
    $check = $conn->prepare("SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?");
    $check->bind_param("ii", $user_id, $event_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
      $register_success = "✅ You're already registered for this event!";
    } else {
      // Register user
      $stmt = $conn->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
      $stmt->bind_param("ii", $user_id, $event_id);
      if ($stmt->execute()) {
        $register_success = "🎉 Registered successfully!";
        header("Location: dashboard.php");
        exit();
      } else {
        $register_success = "❌ Registration failed. Try again.";
      }
    }
  }
}

$sql = "SELECT events.*, users.name AS creator_name 
        FROM events 
        JOIN users ON events.created_by = users.id 
        WHERE events.id = $event_id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
  echo "Event not found.";
  exit;
}

$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title><?php echo htmlspecialchars($event['title']); ?> - Event Details</title>
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
    .event-details {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.1);
      max-width: 700px;
      margin: auto;
    }
    h1 {
      margin-top: 0;
    }
    .meta {
      font-size: 14px;
      color: #666;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
<nav>
  <a href="dashboard.php">🏠 Back to Dashboard</a>
  <a href="events.php">📅 All Events</a>
  <a href="php/logout.php">🚪 Logout</a>
</nav>
<div class="event-details">
  <h1><?php echo htmlspecialchars($event['title']); ?></h1>
  <div class="meta">
    📍 Created by: <?php echo htmlspecialchars($event['creator_name']); ?><br>
    📆 Date: <?php echo date('F j, Y', strtotime($event['event_date'])); ?><br>
  </div>

  <?php if (!empty($event['upload_path'])): ?>
    <img src="<?php echo htmlspecialchars($event['upload_path']); ?>" alt="Event Image">
  <?php endif; ?>

  <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
  
  <?php if ($user_id): ?>
  <form method="POST">
    <button type="submit" name="register" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer;">
      ✅ Register for this Event
    </button>
  </form>
<?php else: ?>
  <p><strong><a href="index.html">Log in</a> to register for this event.</strong></p>
<?php endif; ?>

<?php if ($register_success): ?>
  <p style="margin-top: 10px; color: green;"><?php echo $register_success; ?></p>
<?php endif; ?>

</div>

</body>
</html>
