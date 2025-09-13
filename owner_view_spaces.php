<?php
// owner_view_spaces.php - Owner's view of their parking spaces
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
// Assuming parking_spaces table has an owner_id column. If not, this needs to be added for true ownership filtering.
$spaces = $pdo->prepare("SELECT * FROM parking_spaces WHERE owner_id = ? ORDER BY space_number");
$spaces->execute([$owner_id]);
$spaces = $spaces->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Parking Spaces - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/parking_spaces.css">
    <style>
        body { background: #f4f6f8; }
        .status-available { background: #e0e0e0; color: #333; }
        .status-reserved { background: #1976d2; color: #fff; }
        .status-occupied { background: #43a047; color: #fff; }
        .space-box { min-width: 120px; min-height: 80px; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 8px; margin: 5px; flex-direction: column; }
        .space-box-label { font-size: 14px; margin: 2px 0; }
        .status-label { font-weight: bold; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">My Parking Spaces</h2>
    <div class="d-flex flex-wrap mb-4">
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
    </div>
    <a href="owner_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>
</body>
</html>
