<?php
session_start();

$filter = isset($_GET['event']) ? $_GET['event'] : '';
$participants = $_SESSION['participants'] ?? [];

if ($filter) {
    $participants = array_filter($participants, fn($p) => $p['event'] === $filter);
}

$total = count($participants);
$attended = count(array_filter($participants, fn($p) => $p['attended']));

return [
    'filter' => $filter,
    'participants' => $participants,
    'total' => $total,
    'attended' => $attended
];
?>