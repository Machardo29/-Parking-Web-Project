<?php
// reserve.php - Reserve a parking space
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$message = '';

// Fetch available spaces
$stmt = $pdo->prepare("SELECT * FROM parking_spaces WHERE status = 'available' AND available_spaces > 0 ORDER BY space_number");
$stmt->execute();
$spaces = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $space_id = $_POST['space_id'];
    $license_plate = trim($_POST['license_plate']);
    $vehicle_type = $_POST['vehicle_type'];
    // Get rate and available_spaces
    $stmt = $pdo->prepare("SELECT hourly_rate, available_spaces FROM parking_spaces WHERE id = ?");
    $stmt->execute([$space_id]);
    $row = $stmt->fetch();
    if ($row && $row['available_spaces'] > 0) {
        $rate = $row['hourly_rate'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        // Insert reservation
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, space_id, license_plate, vehicle_type, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        if ($stmt->execute([$user_id, $space_id, $license_plate, $vehicle_type, $start_time, $end_time])) {
            // Decrement available_spaces
            $pdo->prepare("UPDATE parking_spaces SET available_spaces = available_spaces - 1 WHERE id = ? AND available_spaces > 0")->execute([$space_id]);
            header('Location: my_reservations.php?reserved=1');
            exit;
        } else {
            $message = 'Reservation failed!';
        }
    } else {
        $message = 'No available spaces for this parking lot!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve Space - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .card { max-width: 500px; margin: 40px auto; border-radius: 10px; }
        .btn-success { background: #43a047; border: none; }
    </style>
</head>
<body>
<div class="card shadow p-4">
    <h3 class="mb-3 text-center">Reserve a Parking Space</h3>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="space_id" class="form-label">Select Space</label>
            <select name="space_id" id="space_id" class="form-select" required>
                <option value="">-- Select --</option>
                <?php foreach ($spaces as $space): ?>
                    <option value="<?= $space['id'] ?>">
                        <?= htmlspecialchars($space['space_number']) ?> (<?= ucfirst($space['vehicle_type']) ?>, Ksh<?= $space['hourly_rate'] ?>/hr)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="license_plate" class="form-label">License Plate</label>
            <input type="text" name="license_plate" id="license_plate" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vehicle Type</label>
            <select name="vehicle_type" class="form-select" required>
                <option value="two-wheeler">Two-wheeler</option>
                <option value="four-wheeler">Four-wheeler</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time</label>
            <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time</label>
            <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Reserve</button>
        <div class="mt-2 text-center">
            <a href="user_dashboard.php">Back to Dashboard</a>
        </div>
    </form>
</div>
</body>
</html>
