<?php
include 'db.php';

// Handle new registration
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $event = $_POST['event'];

    $stmt = $conn->prepare("INSERT INTO participants (name, email, event, attended) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $email, $event);
    $stmt->execute();

    header("Location: tracker.php");
    exit;
}

// Handle attendance update
if (isset($_POST['update_attendance'])) {
    $attendance = $_POST['attendance'] ?? [];

    // First, set all to not attended for the filtered event (if any)
    $eventFilter = $_GET['event'] ?? '';
    if ($eventFilter) {
        $stmt = $conn->prepare("UPDATE participants SET attended = 0 WHERE event = ?");
        $stmt->bind_param("s", $eventFilter);
    } else {
        $stmt = $conn->prepare("UPDATE participants SET attended = 0");
    }
    $stmt->execute();

    // Then, mark selected participants as attended
    foreach ($attendance as $id => $value) {
        $id = intval($id);
        $stmt = $conn->prepare("UPDATE participants SET attended = 1 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    // Redirect back to filtered view if applicable
    $redirect = "tracker.php";
    if ($eventFilter) $redirect .= '?event=' . urlencode($eventFilter);
    header("Location: $redirect");
    exit;
}
?>