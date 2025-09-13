<?php
// export_owner_report_pdf.php - Simple printable HTML for owner report (like receipt)
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT r.*, p.space_number, p.hourly_rate, u.full_name AS user_name FROM reservations r JOIN parking_spaces p ON r.space_id = p.id JOIN users u ON r.user_id = u.id WHERE r.end_time IS NOT NULL AND p.owner_id = ? ORDER BY r.start_time DESC");
$stmt->execute([$owner_id]);
$occupied = $stmt->fetchAll();
$total_cost = 0;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Occupied Slots Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .receipt-box { max-width: 900px; margin: 40px auto; border-radius: 10px; background: #fff; padding: 30px; box-shadow: 0 2px 8px #bbb; }
        .btn-primary { background: #1976d2; border: none; }
    </style>
</head>
<body>
<div class="receipt-box">
    <h3 class="text-center mb-3">Occupied Slots Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Space Number</th>
                <th>Reserved By</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Cost (Ksh)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($occupied as $i => $row):
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
    <div class="text-center">
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</div>
</body>
</html>
