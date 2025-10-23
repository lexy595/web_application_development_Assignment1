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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Participants - Event Tracker</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Print-specific styles */
        @media print {
            body {
                background: white;
                color: black;
                font-size: 12pt;
                line-height: 1.3;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-header {
                text-align: center;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #eee;
            }
            
            .print-header h1 {
                margin: 0;
                color: var(--primary-color);
                font-size: 1.5rem;
            }
            
            .print-meta {
                margin: 0.5rem 0;
                color: #666;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 1rem 0;
                font-size: 0.9em;
            }
            
            th, td {
                padding: 0.5rem;
                border: 1px solid #ddd;
                text-align: left;
            }
            
            th {
                background-color: #f5f5f5;
                font-weight: 600;
            }
            
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
        }
    </style>
</head>
<body class="print-content">
    <div class="container">
        <div class="print-header">
            <h1>Event Participants</h1>
            <div class="print-meta">
                <?php if ($eventFilter): ?>
                    <p>Event: <?= htmlspecialchars($eventFilter) ?></p>
                <?php endif; ?>
                <p>Generated on: <?= date('F j, Y, g:i a') ?></p>
                <p>Total Participants: <?= count($participants) ?></p>
            </div>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Event</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($participants) > 0): ?>
                        <?php foreach ($participants as $i => $p): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['event']) ?></td>
                                <td>
                                    <span class="status-badge <?= $p['attended'] ? 'status-attended' : 'status-pending' ?>">
                                        <?= $p['attended'] ? 'Attended' : 'Pending' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No participants found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="no-print" style="margin-top: 2rem; text-align: center;">
            <button onclick="window.print()" class="btn btn-print">
                <i class="material-icons">print</i> Print This Page
            </button>
            <a href="tracker.php" class="btn" style="margin-left: 1rem;">
                <i class="material-icons">arrow_back</i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <script>
        // Auto-print when the page loads (only for printing, not in preview)
        window.onload = function() {
            // Check if this is a print request (not a regular page load)
            if (window.location.search.indexOf('print=1') > -1) {
                window.print();
                // Close the window after printing (if not in print preview)
                window.onafterprint = function() {
                    setTimeout(function() {
                        window.close();
                    }, 200);
                };
            }
        };
    </script>
</body>
</html>
