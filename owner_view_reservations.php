<?php
// owner_view_reservations.php - Owner's view of reservations for their spaces
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
// Only show reservations for spaces owned by this owner
$sql = "SELECT r.*, u.username, p.space_number, p.hourly_rate FROM reservations r JOIN users u ON r.user_id = u.id JOIN parking_spaces p ON r.space_id = p.id WHERE p.owner_id = ? ORDER BY r.created_at DESC LIMIT 50";
$stmt = $pdo->prepare($sql);
$stmt->execute([$owner_id]);
$reservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .btn-primary { background: #1976d2; border: none; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Reservations for My Spaces</h2>
        <a href="owner_dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>User</th>
                <th>Space</th>
                <th>License Plate</th>
                <th>Vehicle Type</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr>
                <td><?= htmlspecialchars($r['username']) ?></td>
                <td><?= htmlspecialchars($r['space_number']) ?></td>
                <td><?= htmlspecialchars($r['license_plate']) ?></td>
                <td><?= ucfirst($r['vehicle_type']) ?></td>
                <td><?= ucfirst($r['status']) ?></td>
                <td><?= $r['start_time'] ?></td>
                <td><?= $r['end_time'] ?? '-' ?></td>
                <td>
                    <?php
                    if ($r['end_time']) {
                        $start = strtotime($r['start_time']);
                        $end = strtotime($r['end_time']);
                        $hours = ceil(($end - $start) / 3600);
                        $cost = $hours * $r['hourly_rate'];
                        echo 'Ksh' . $cost;
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
