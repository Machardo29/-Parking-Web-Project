<?php
// export_owner_report_csv.php - Export occupied slots as CSV for owner (fixed SQL)
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="owner_occupied_slots_report.csv"');
$output = fopen('php://output', 'w');
fputcsv($output, ['#', 'Space Number', 'Reserved By', 'Start Time', 'End Time', 'Cost (Ksh)']);
$stmt = $pdo->prepare("SELECT r.*, p.space_number, p.hourly_rate, u.full_name AS user_name FROM reservations r JOIN parking_spaces p ON r.space_id = p.id JOIN users u ON r.user_id = u.id WHERE r.end_time IS NOT NULL AND p.owner_id = ? ORDER BY r.start_time DESC");
$stmt->execute([$owner_id]);
$occupied = $stmt->fetchAll();
$total_cost = 0;
foreach ($occupied as $i => $row) {
    $start = strtotime($row['start_time']);
    $end = strtotime($row['end_time']);
    $hours = ceil(($end - $start) / 3600);
    $cost = $hours * $row['hourly_rate'];
    $total_cost += $cost;
    fputcsv($output, [
        $i+1,
        $row['space_number'],
        $row['user_name'],
        $row['start_time'],
        $row['end_time'],
        $cost
    ]);
}
fputcsv($output, ['', '', '', '', 'Total Cost', $total_cost]);
fclose($output);
exit;
