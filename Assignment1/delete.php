<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die('Participant ID not specified.');
}

$id = intval($_GET['id']);

// Delete participant
$stmt = $conn->prepare("DELETE FROM participants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php"); // redirect back to main page
exit;
?>
