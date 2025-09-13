<?php
// view_spaces.php - Public grid view of all parking spaces
require_once 'includes/db.php';

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$spaces = $pdo->query("SELECT * FROM parking_spaces ORDER BY space_number")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parking Spaces - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/parking_spaces.css">
    <style>
        body { background: #f4f6f8; }
        .status-available { background: #e0e0e0; color: #333; }
        .status-reserved { background: #1976d2; color: #fff; }
        .status-occupied { background: #43a047; color: #fff; }
        .space-box { min-width: 120px; min-height: 80px; display: flex; align-items: center; justify-content: center; font-weight: bold; border-radius: 8px; margin: 5px; flex-direction: column; }
        .space-box-label { font-size: 0.9rem; }
        .status-label { position: absolute; top: 5px; right: 5px; background: rgba(255, 255, 255, 0.8); padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4">Parking Spaces</h2>
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
</div>
</body>
</html>
