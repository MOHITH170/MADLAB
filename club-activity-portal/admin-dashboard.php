<?php
session_start();
include 'php/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

$admin_id = $_SESSION['user_id'];
// Fetch clubs created by this admin
$clubs_stmt = $conn->prepare("SELECT id, name FROM clubs WHERE created_by = ?");
$clubs_stmt->bind_param("i", $admin_id);
$clubs_stmt->execute();
$clubs_result = $clubs_stmt->get_result();

$name = $_SESSION['name'];
$role = $_SESSION['role'];

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');
$message = '';

// Handle event creation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $event_date = $_POST['event_date'];
    $upload_path = null;

    if (isset($_FILES['event_file']) && $_FILES['event_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = basename($_FILES["event_file"]["name"]);
        $target_file = $upload_dir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["event_file"]["tmp_name"], $target_file)) {
            $upload_path = $target_file;
        }
    }

    $club_id = $_POST['club_id'];
    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, created_by, upload_path, club_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisi", $title, $description, $event_date, $admin_id, $upload_path, $club_id);

    if ($stmt->execute()) {
        $message = "✅ Event created successfully!";
    } else {
        $message = "❌ Failed to create event.";
    }
}

// Fetch all upcoming events
$all_stmt = $conn->prepare("SELECT events.*, users.name AS creator_name 
                            FROM events 
                            JOIN users ON events.created_by = users.id 
                            WHERE event_date >= ? 
                            ORDER BY event_date ASC");
$all_stmt->bind_param("s", $today);
$all_stmt->execute();
$all_events = $all_stmt->get_result();

// Fetch only events created by this admin
$admin_stmt = $conn->prepare("SELECT * FROM events WHERE created_by = ? ORDER BY event_date DESC");
$admin_stmt->bind_param("i", $admin_id);
$admin_stmt->execute();
$admin_events = $admin_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial, sans-serif; background-color: #eef; margin: 0; }
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
      justify-content: center;
      gap: 30px;
    }
    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }
    .container {
      padding: 30px;
      max-width: 1000px;
      margin: auto;
    }
    .card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 30px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    input, textarea, select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
    }
    button {
      background: #007bff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .event {
      background: white;
      border-radius: 10px;
      margin-bottom: 20px;
      padding: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .event img {
      width: 100%;
      border-radius: 10px 10px 0 0;
      max-height: 300px;
      object-fit: cover;
      margin-bottom: 15px;
    }
    .event h3 {
      margin-top: 0;
      color: #003366;
    }
    .event a {
      color: #003366;
      text-decoration: none;
      font-weight: bold;
    }
    .message {
      color: green;
      font-weight: bold;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<header>
  <h1>Welcome, <?php echo htmlspecialchars($name); ?> 👨‍💼</h1>
  <p>Your role: <?php echo htmlspecialchars($role); ?></p>
</header>

<nav>
  <a href="dashboard.php">Dashboard</a>
  <a href="create_club.php">Create Club</a>
  <a href="manage_clubs.php">Manage Clubs</a>
  <a href="php/logout.php">Logout</a>
</nav>

<div class="container">

  <div class="card">
    <h2>📝 Create New Event</h2>
    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="title" placeholder="Event Title" required>
      <textarea name="description" rows="4" placeholder="Event Description" required></textarea>
      <input type="date" name="event_date" required>
      <input type="file" name="event_file">
      <label for="club_id">Select Club:</label>
      <select name="club_id" required>
        <option value="">-- Choose a club --</option>
        <?php while ($club = $clubs_result->fetch_assoc()): ?>
          <option value="<?php echo $club['id']; ?>"><?php echo htmlspecialchars($club['name']); ?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit">Create Event</button>
    </form>
    <?php if ($message): ?>
      <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>📅 All Upcoming Events</h2>
    <?php if ($all_events->num_rows > 0): ?>
      <?php while ($event = $all_events->fetch_assoc()): ?>
        <div class="event">
          <?php if (!empty($event['upload_path'])): ?>
            <img src="<?php echo htmlspecialchars($event['upload_path']); ?>" alt="Event Image">
          <?php endif; ?>
          <h3><a href="event.php?id=<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['title']); ?></a></h3>
          <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
          <p><strong>By:</strong> <?php echo htmlspecialchars($event['creator_name']); ?></p>
          <p><?php echo nl2br(htmlspecialchars(substr($event['description'], 0, 100))) . '...'; ?></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No upcoming events found.</p>
    <?php endif; ?>
  </div>

  <div class="card">
    <h2>📌 Your Created Events</h2>
    <?php if ($admin_events->num_rows > 0): ?>
      <?php while ($event = $admin_events->fetch_assoc()): ?>
        <div class="event">
          <?php if (!empty($event['upload_path'])): ?>
            <img src="<?php echo htmlspecialchars($event['upload_path']); ?>" alt="Event Image">
          <?php endif; ?>
          <h3><?php echo htmlspecialchars($event['title']); ?></h3>
          <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?></p>
          <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
          <p><a href="event.php?id=<?php echo $event['id']; ?>">🔍 View Details</a></p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>You haven’t created any events yet.</p>
    <?php endif; ?>
  </div>

</div>

</body>
</html>
