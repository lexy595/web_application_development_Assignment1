<?php
include 'db.php'; // your DB connection

if (!isset($_GET['id'])) {
    die('Participant ID not specified.');
}

$id = intval($_GET['id']);

// Fetch participant details
$stmt = $conn->prepare("SELECT * FROM participants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$participant = $result->fetch_assoc();

if (!$participant) {
    die('Participant not found.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $event = $_POST['event'];

    $stmt = $conn->prepare("UPDATE participants SET name=?, email=?, event=? WHERE id=?");
    $stmt->bind_param("sssi", $name, $email, $event, $id);
    $stmt->execute();

    header("Location: tracker.php"); // redirect to main page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant - Event Tracker</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material-icons" rel="stylesheet">
</head>
<body>
<header>
    <div class="header-content">
        <h1>Edit Participant</h1>
        <a href="tracker.php" class="btn">
            <i class="material-icons">arrow_back</i> Back to Dashboard
        </a>
    </div>
</header>

<main class="container">
    <div class="form-container animate-fade-in">
        <form method="POST" class="card">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($participant['name']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($participant['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="event">Event</label>
                <select id="event" name="event" required>
                    <option value="">Select Event</option>
                    <option value="Tech Conference" <?= $participant['event'] === 'Tech Conference' ? 'selected' : '' ?>>Tech Conference</option>
                    <option value="Workshop" <?= $participant['event'] === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
                    <option value="Seminar" <?= $participant['event'] === 'Seminar' ? 'selected' : '' ?>>Seminar</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">
                    <i class="material-icons">save</i> Update Participant
                </button>
            </div>
        </form>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Event Registration System. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
