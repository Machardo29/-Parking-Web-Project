<?php
session_start();
// admin_dashboard.php - Admin dashboard
require_once __DIR__ . '/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
// Stats
$total_spaces = $pdo->query("SELECT COUNT(*) FROM parking_spaces")->fetchColumn();
$occupied = $pdo->query("SELECT COUNT(*) FROM parking_spaces WHERE status = 'occupied'")->fetchColumn();
$reserved = $pdo->query("SELECT COUNT(*) FROM parking_spaces WHERE status = 'reserved'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_reservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .card { border-radius: 10px; }
        .stat-box { min-width: 180px; min-height: 100px; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 8px; margin: 5px; font-size: 1.5rem; }
        .bg-grey { background: #e0e0e0; color: #333; }
        .bg-blue { background: #1976d2; color: #fff; }
        .bg-green { background: #43a047; color: #fff; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Admin Dashboard</h2>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box bg-grey">Total Spaces<br><?= $total_spaces ?></div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-blue">Reserved<br><?= $reserved ?></div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-green">Occupied<br><?= $occupied ?></div>
        </div>
        <div class="col-md-3">
            <div class="stat-box bg-grey">Users<br><?= $total_users ?></div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Total Reservations</h5>
                <div class="display-6"><?= $total_reservations ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="manage_spaces.php" class="btn btn-primary w-100 mb-2">Manage Spaces</a>
            <a href="view_reservations.php" class="btn btn-primary w-100 mb-2">View Reservations</a>
        </div>
        <div class="col-md-4">
            <a href="reports.php" class="btn btn-success w-100 mb-2">View Reports</a>
        </div>
    </div>
</div>
</body>
</html>
