<?php
// reports.php - Simple admin reports
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
// Daily occupancy
$today = date('Y-m-d');
$daily_occupancy = $pdo->query("SELECT COUNT(*) FROM reservations WHERE DATE(start_time) = '$today'")->fetchColumn();
// Weekly revenue
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$weekly_revenue = 0;
$stmt = $pdo->prepare("SELECT r.start_time, r.end_time, p.hourly_rate FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.end_time IS NOT NULL AND DATE(r.start_time) BETWEEN ? AND ?");
$stmt->execute([$week_start, $week_end]);
$res = $stmt->fetchAll();
foreach ($res as $row) {
    $start = strtotime($row['start_time']);
    $end = strtotime($row['end_time']);
    $hours = ceil(($end - $start) / 3600);
    $weekly_revenue += $hours * $row['hourly_rate'];
}
// Vehicle type usage
$type_usage = $pdo->query("SELECT vehicle_type, COUNT(*) as count FROM reservations GROUP BY vehicle_type")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .card { border-radius: 10px; }
        .bg-blue { background: #1976d2; color: #fff; }
        .bg-green { background: #43a047; color: #fff; }
        .bg-grey { background: #e0e0e0; color: #333; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Reports</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center bg-blue mb-2">
                <h5>Today's Occupancy</h5>
                <div class="display-6"><?= $daily_occupancy ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center bg-green mb-2">
                <h5>This Week's Revenue</h5>
                <div class="display-6">Ksh<?= $weekly_revenue ?: 0 ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center bg-grey mb-2">
                <h5>Vehicle Type Usage</h5>
                <?php foreach ($type_usage as $row): ?>
                    <div><?= ucfirst($row['vehicle_type']) ?>: <?= $row['count'] ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
