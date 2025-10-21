<?php
include 'db.php';
include 'filter.php';

// Handle new registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $event = trim($_POST['event']);

    if ($name && $email && $event) {
        $stmt = $conn->prepare("INSERT INTO participants (name, email, event) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $event);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: tracker.php");
    exit;
}

// Handle attendance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    $attendance = $_POST['attendance'] ?? [];

    // Reset all to 0 first
    $conn->query("UPDATE participants SET attended = 0");

    // Then mark selected as attended
    foreach ($attendance as $id => $val) {
        $stmt = $conn->prepare("UPDATE participants SET attended = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Redirect back with filter if any
    $redirect = isset($_GET['event']) ? "tracker.php?event=" . urlencode($_GET['event']) : "tracker.php";
    header("Location: $redirect");
    exit;
}
?>
