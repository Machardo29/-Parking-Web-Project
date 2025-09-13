<?php
// my_reservations.php - User's reservations
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch reservations
$stmt = $pdo->prepare("SELECT r.*, p.space_number, p.hourly_rate FROM reservations r JOIN parking_spaces p ON r.space_id = p.id WHERE r.user_id = ? ORDER BY r.created_at DESC");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();

// Cancel reservation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $res_id = $_GET['cancel'];
    // Set reservation status to cancelled
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$res_id, $user_id]);
    // Free up the parking space
    $stmt = $pdo->prepare("UPDATE parking_spaces SET status = 'available' WHERE id = (SELECT space_id FROM reservations WHERE id = ?)");
    $stmt->execute([$res_id]);
    header('Location: my_reservations.php?cancelled=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Reservations - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .btn-success { background: #43a047; border: none; }
        .btn-primary { background: #1976d2; border: none; }
        .btn-danger { background: #b71c1c; border: none; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>My Reservations</h2>
        <a href="user_dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
    <?php if (isset($_GET['reserved'])): ?>
        <div class="alert alert-success">Reservation successful!</div>
    <?php endif; ?>
    <?php if (isset($_GET['cancelled'])): ?>
        <div class="alert alert-info">Reservation cancelled.</div>
    <?php endif; ?>
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Space</th>
                <th>License Plate</th>
                <th>Vehicle Type</th>
                <th>Status</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Total Cost</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $r): ?>
            <tr>
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
                <td>
                    <?php if ($r['status'] === 'active'): ?>
                        <a href="my_reservations.php?cancel=<?= $r['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this reservation?')">Cancel</a>
                    <?php endif; ?>
                    <a href="print_receipt.php?id=<?= $r['id'] ?>" class="btn btn-primary btn-sm" target="_blank">Print Receipt</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
