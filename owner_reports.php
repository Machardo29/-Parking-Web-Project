<?php
// owner_reports.php - Reports for owner (only their spaces)
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
// Daily occupancy for owner's spaces
$today = date('Y-m-d');
$daily_occupancy = $pdo->prepare("SELECT COUNT(*) FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE DATE(r.start_time) = ? AND p.owner_id = ?");
$daily_occupancy->execute([$today, $owner_id]);
$daily_occupancy = $daily_occupancy->fetchColumn();
// Weekly revenue for owner's spaces
$week_start = date('Y-m-d', strtotime('monday this week'));
$week_end = date('Y-m-d', strtotime('sunday this week'));
$weekly_revenue = 0;
$stmt = $pdo->prepare("SELECT r.start_time, r.end_time, p.hourly_rate FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.end_time IS NOT NULL AND DATE(r.start_time) BETWEEN ? AND ? AND p.owner_id = ?");
$stmt->execute([$week_start, $week_end, $owner_id]);
$res = $stmt->fetchAll();
foreach ($res as $row) {
    $start = strtotime($row['start_time']);
    $end = strtotime($row['end_time']);
    $hours = ceil(($end - $start) / 3600);
    $weekly_revenue += $hours * $row['hourly_rate'];
}
// Vehicle type usage for owner's spaces
$type_usage = $pdo->prepare("SELECT r.vehicle_type, COUNT(*) as count FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE p.owner_id = ? GROUP BY r.vehicle_type");
$type_usage->execute([$owner_id]);
$type_usage = $type_usage->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reports - Car Parking System</title>
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
        <h2>My Reports</h2>
        <a href="owner_dashboard.php" class="btn btn-secondary">Dashboard</a>
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
    <!-- Occupied Slots Table and Export Buttons -->
    <div class="card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Occupied Slots (All Time)</h5>
            <div>
                <a href="export_owner_report_csv.php" class="btn btn-outline-primary btn-sm">Export as CSV</a>
                <a href="export_owner_report_pdf.php" class="btn btn-outline-danger btn-sm">Export as PDF</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Space</th>
                        <th>Reserved By</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Cost (Ksh)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT r.*, p.space_number, p.hourly_rate, u.full_name AS user_name FROM reservations r JOIN parking_spaces p ON r.space_id = p.id JOIN users u ON r.user_id = u.id WHERE r.end_time IS NOT NULL AND p.owner_id = ? ORDER BY r.start_time DESC");
                $stmt->execute([$owner_id]);
                $occupied = $stmt->fetchAll();
                $total_cost = 0;
                foreach ($occupied as $i => $row):
                    $start = strtotime($row['start_time']);
                    $end = strtotime($row['end_time']);
                    $hours = ceil(($end - $start) / 3600);
                    $cost = $hours * $row['hourly_rate'];
                    $total_cost += $cost;
                ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($row['space_number']) ?></td>
                        <td><?= htmlspecialchars($row['user_name']) ?></td>
                        <td><?= $row['start_time'] ?></td>
                        <td><?= $row['end_time'] ?></td>
                        <td><?= $cost ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total Cost</th>
                        <th><?= $total_cost ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
</body>
</html>
