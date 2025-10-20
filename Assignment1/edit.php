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

    header("Location: index.php"); // redirect to main page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Participant</title>
</head>
<body>
<h2>Edit Participant</h2>
<form method="POST">
    <input type="text" name="name" value="<?= htmlspecialchars($participant['name']) ?>" required>
    <input type="email" name="email" value="<?= htmlspecialchars($participant['email']) ?>" required>
    <select name="event" required>
        <option value="Tech Conference" <?= $participant['event'] === 'Tech Conference' ? 'selected' : '' ?>>Tech Conference</option>
        <option value="Workshop" <?= $participant['event'] === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
        <option value="Seminar" <?= $participant['event'] === 'Seminar' ? 'selected' : '' ?>>Seminar</option>
    </select>
    <button type="submit">Update</button>
</form>
</body>
</html>
