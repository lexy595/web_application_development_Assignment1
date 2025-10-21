<?php
include 'db.php';

$eventFilter = $_GET['event'] ?? '';

// Fetch participants
if ($eventFilter) {
    $stmt = $conn->prepare("SELECT * FROM participants WHERE event = ?");
    $stmt->bind_param("s", $eventFilter);
} else {
    $stmt = $conn->prepare("SELECT * FROM participants");
}
$stmt->execute();
$result = $stmt->get_result();
$participants = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Participants</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        button { margin: 10px 0; }
    </style>
</head>
<body>
<h2>Participants <?= $eventFilter ? "for $eventFilter" : "" ?></h2>
<button onclick="window.print()">Print</button>
<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Event</th>
        <th>Attended</th>
    </tr>
    <?php foreach ($participants as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['event']) ?></td>
            <td><?= $p['attended'] ? 'Yes' : 'No' ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
