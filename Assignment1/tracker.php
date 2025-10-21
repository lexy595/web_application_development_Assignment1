<?php
include 'db.php';
include 'Filter.php';

// Get the current event filter from GET
$filter = $_GET['event'] ?? '';

// Fetch participants
if ($filter) {
    $stmt = $conn->prepare("SELECT * FROM participants WHERE event = ?");
    $stmt->bind_param("s", $filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM participants");
}
$stmt->execute();
$result = $stmt->get_result();
$participants = $result->fetch_all(MYSQLI_ASSOC);

// Count attended participants
$total = count($participants);
$attended = count(array_filter($participants, fn($p) => $p['attended']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Registration and Attendance Tracker</title>

</head>
<body>

<h2>Event Registration</h2>
<form method="POST" action="registration.php">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <select name="event" required>
        <option value="">Select Event</option>
        <option value="Tech Conference">Tech Conference</option>
        <option value="Workshop">Workshop</option>
        <option value="Seminar">Seminar</option>
    </select>
    <button type="submit" name="register">Register</button>
</form>

<hr>

<h2>Filter by Event</h2>
<form method="GET" action="">
    <select name="event">
        <option value="">All Events</option>
        <option value="Tech Conference" <?= $filter === 'Tech Conference' ? 'selected' : '' ?>>Tech Conference</option>
        <option value="Workshop" <?= $filter === 'Workshop' ? 'selected' : '' ?>>Workshop</option>
        <option value="Seminar" <?= $filter === 'Seminar' ? 'selected' : '' ?>>Seminar</option>
    </select>
    <button type="submit">Filter</button>
</form>

<h3>Participants (<?= $attended ?>/<?= $total ?> Attended)</h3>

<form method="POST" action="registration.php<?= $filter ? '?event=' . urlencode($filter) : '' ?>">
    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Event</th>
            <th>Attended</th>
            <th>Actions</th>
        </tr>
        <?php if ($participants): ?>
            <?php foreach ($participants as $i => $p): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['email']) ?></td>
                    <td><?= htmlspecialchars($p['event']) ?></td>
                    <td><input type="checkbox" name="attendance[<?= $p['id'] ?>]" <?= $p['attended'] ? 'checked' : '' ?>></td>
                    <td>
                        <a href="edit.php?id=<?= $p['id'] ?>">Edit</a> |
                        <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a> |
                        <a href="print.php?event=<?= urlencode($p['event']) ?>" target="_blank">Print</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No participants found.</td></tr>
        <?php endif; ?>
    </table>

    <br>
    <button type="submit" name="update_attendance">Update Attendance</button>
</form>

</body>
</html>
