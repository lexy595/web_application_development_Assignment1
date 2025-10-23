<?php
include 'db.php';

// Get filter
$filter = $_GET['event'] ?? '';

// Fetch participants (filtered or all)
if ($filter) {
    $stmt = $conn->prepare("SELECT * FROM participants WHERE event = ?");
    $stmt->bind_param("s", $filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM participants");
}
$stmt->execute();
$result = $stmt->get_result();
$participants = $result->fetch_all(MYSQLI_ASSOC);

// Count totals
$total = count($participants);
$attended = count(array_filter($participants, fn($p) => $p['attended']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration and Attendance Tracker</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<header>
    <div class="header-content">
        <h1>Event Registration System</h1>
        <div class="header-actions">
            <a href="print.php" class="btn btn-print no-print">
                <i class="material-icons">print</i> Print List
            </a>
        </div>
    </div>
</header>

<main class="container">
    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <h3><?= $total ?></h3>
            <p>Total Participants</p>
        </div>
        <div class="stat-card">
            <h3><?= $attended ?></h3>
            <p>Attended</p>
        </div>
        <div class="stat-card">
            <h3><?= $total > 0 ? round(($attended / $total) * 100) : 0 ?>%</h3>
            <p>Attendance Rate</p>
        </div>
    </div>

    <!-- Registration Form -->
    <section class="card">
        <h2><i class="material-icons">person_add</i> Register New Participant</h2>
        <form method="POST" action="registration.php" class="form-grid">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter email address" required>
            </div>
            <div class="form-group">
                <label for="event">Event</label>
                <select id="event" name="event" required>
                    <option value="">Select an event</option>
                    <option value="Tech Conference">Tech Conference</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Seminar">Seminar</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" name="register" class="btn">
                    <i class="material-icons">person_add</i> Register Participant
                </button>
            </div>
        </form>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <h2><i class="material-icons">filter_list</i> Filter Participants</h2>
        <form method="GET" action="" class="filter-form">
            <div class="form-group" style="margin-bottom: 0;">
                <select name="event" onchange="this.form.submit()">
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
