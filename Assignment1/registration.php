<?php
session_start();

if (!isset($_SESSION['participants'])) {
    $_SESSION['participants'] = [];
}

// Handle new registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $event = trim($_POST['event']);

    if ($name && $email && $event) {
        $_SESSION['participants'][] = [
            'name' => $name,
            'email' => $email,
            'event' => $event,
            'attended' => false
        ];
    }

    header("Location: tracker.php");
    exit;
}

// Handle attendance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    $filter = isset($_GET['event']) ? $_GET['event'] : '';
    foreach ($_SESSION['participants'] as $index => &$participant) {
        $participant['attended'] = isset($_POST['attendance'][$index]);
    }
    unset($participant);
    header("Location: tracker.php" . ($filter ? "?event=$filter" : ""));
    exit;
}
?>
