<?php
// print_receipt.php - Print reservation receipt (mock)
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    exit('Not authorized.');
}
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit('Invalid reservation.');
}
$res_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT r.*, p.space_number, p.hourly_rate FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.id = ? AND r.user_id = ?");
$stmt->execute([$res_id, $user_id]);
$r = $stmt->fetch();
if (!$r) exit('Reservation not found.');

// Calculate duration and cost
$start = strtotime($r['start_time']);
$end = $r['end_time'] ? strtotime($r['end_time']) : time();
$hours = ceil(($end - $start) / 3600);
$cost = $hours * $r['hourly_rate'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .receipt-box { max-width: 400px; margin: 40px auto; border-radius: 10px; background: #fff; padding: 30px; box-shadow: 0 2px 8px #bbb; }
        .btn-primary { background: #1976d2; border: none; }
    </style>
</head>
<body>
<div class="receipt-box">
    <h3 class="text-center mb-3">Parking Receipt</h3>
    <table class="table">
        <tr><th>Space Number</th><td><?= htmlspecialchars($r['space_number']) ?></td></tr>
        <tr><th>License Plate</th><td><?= htmlspecialchars($r['license_plate']) ?></td></tr>
        <tr><th>Vehicle Type</th><td><?= ucfirst($r['vehicle_type']) ?></td></tr>
        <tr><th>Start Time</th><td><?= $r['start_time'] ?></td></tr>
        <tr><th>End Time</th><td><?= $r['end_time'] ?? '-' ?></td></tr>
        <tr><th>Duration</th><td><?= $hours ?> hour(s)</td></tr>
        <tr><th>Total Cost</th><td><b>Ksh<?= $cost ?></b></td></tr>
    </table>
    <div class="text-center">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</div>
</body>
</html>
