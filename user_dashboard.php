<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Debug session info
file_put_contents(__DIR__ . '/session_debug.log', print_r($_SESSION, true));

// user_dashboard.php - User dashboard
require_once __DIR__ . '/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch available spaces
global $pdo;
$spaces = $pdo->query("SELECT * FROM parking_spaces ORDER BY space_number")->fetchAll();
if (!$spaces) {
    $spaces = [];
}
// Fetch user's reservations
$stmt = $pdo->prepare("SELECT r.*, p.space_number FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.user_id = ? ORDER BY r.created_at DESC LIMIT 5");
try {
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
} catch (Exception $e) {
    $reservations = [];
}
// Basic stats
try {
    $total_spaces = $pdo->query("SELECT COUNT(*) FROM parking_spaces")->fetchColumn();
} catch (Exception $e) {
    $total_spaces = 0;
}
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$user_id]);
    $active_res = $stmt->fetchColumn();
} catch (Exception $e) {
    $active_res = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/parking_spaces.css">
    <style>
        body { background: #f4f6f8; }
        /* Remove conflicting old styles for .space-box and status classes */
        .status-available, .status-reserved, .status-occupied { background: unset; color: unset; }
        .space-box { min-width: unset; min-height: unset; font-weight: unset; border-radius: unset; margin: unset; position: unset; }
        .space-box-label, .status-label { position: unset; font-size: unset; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>User Dashboard</h2>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Total Spaces</h5>
                <div class="display-6"><?= $total_spaces ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>Active Reservations</h5>
                <div class="display-6"><?= $active_res ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center">
                <h5>My Reservations</h5>
                <a href="my_reservations.php" class="btn btn-primary">View All</a>
            </div>
        </div>
    </div>
    <h4>Available Parking Spaces</h4>
    <div class="d-flex flex-wrap mb-4">
        <?php if (empty($spaces)): ?>
            <div class="alert alert-info w-100 text-center">
                No parking spaces available at the moment.
            </div>
        <?php else: ?>
            <?php foreach ($spaces as $space): ?>
                <?php
                    $status_class = 'status-available';
                    if ($space['status'] === 'reserved') $status_class = 'status-reserved';
                    if ($space['status'] === 'occupied') $status_class = 'status-occupied';
                ?>
                <div class="space-box <?= $status_class ?> m-1">
                    <img src="<?= $space['image'] ? htmlspecialchars($space['image']) : 'assets/images/gettyimages-172263592-612x612.jpg' ?>" alt="Parking Lot" />
                    <div class="space-box-label">Parking Lot Number: <b><?= htmlspecialchars($space['space_number']) ?></b></div>
                    <div class="space-box-label">Car Type: <?= ucfirst($space['vehicle_type']) ?></div>
                    <div class="space-box-label">Rate: Ksh<?= $space['hourly_rate'] ?>/hr</div>
                    <div class="space-box-label">Spaces: <b><?= (int)$space['available_spaces'] ?></b> / <b><?= (int)$space['total_spaces'] ?></b></div>
                    <span class="status-label">
                        <?= ucfirst($space['status']) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <h4>Recent Reservations</h4>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Space</th>
                <th>License Plate</th>
                <th>Vehicle Type</th>
                <th>Status</th>
                <th>Start Time</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($reservations)): ?>
            <tr>
                <td colspan="5" class="text-center">
                    No recent reservations found.
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['space_number']) ?></td>
                    <td><?= htmlspecialchars($r['license_plate']) ?></td>
                    <td><?= ucfirst($r['vehicle_type']) ?></td>
                    <td><?= ucfirst($r['status']) ?></td>
                    <td><?= $r['start_time'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="reserve.php" class="btn btn-success">Reserve a Space</a>
</div>
</body>
</html>
