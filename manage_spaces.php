<?php
// manage_spaces.php - Admin CRUD for parking spaces
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$message = '';

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $space_number = trim($_POST['space_number']);
        $vehicle_type = $_POST['vehicle_type'];
        $hourly_rate = $_POST['hourly_rate'];
        $location = trim($_POST['location']);
        $status = 'available';
        $image = null;
        $total_spaces = isset($_POST['total_spaces']) ? max(1, (int)$_POST['total_spaces']) : 1;
        $available_spaces = $total_spaces;
        if (!empty($_FILES['image']['name'])) {
            $target = 'assets/images/' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $target;
            }
        }
        $stmt = $pdo->prepare("INSERT INTO parking_spaces (space_number, vehicle_type, hourly_rate, status, image, location, total_spaces, available_spaces) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$space_number, $vehicle_type, $hourly_rate, $status, $image, $location, $total_spaces, $available_spaces])) {
            $message = 'Space added!';
        } else {
            $message = 'Failed to add space!';
        }
    } elseif (isset($_POST['edit_id'])) {
        $id = $_POST['edit_id'];
        $space_number = trim($_POST['space_number']);
        $vehicle_type = $_POST['vehicle_type'];
        $hourly_rate = $_POST['hourly_rate'];
        $location = trim($_POST['location']);
        $status = $_POST['status'];
        $image = $_POST['existing_image'];
        if (!empty($_FILES['image']['name'])) {
            $target = 'assets/images/' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $target;
            }
        }
        $total_spaces = isset($_POST['total_spaces']) ? max(1, (int)$_POST['total_spaces']) : 1;
        // Fetch current available_spaces
        $cur = $pdo->prepare("SELECT available_spaces, total_spaces FROM parking_spaces WHERE id=?");
        $cur->execute([$id]);
        $cur_row = $cur->fetch();
        $available_spaces = $cur_row ? $cur_row['available_spaces'] : $total_spaces;
        // If total_spaces increased, increase available_spaces by the same amount
        if ($cur_row && $total_spaces > $cur_row['total_spaces']) {
            $available_spaces += ($total_spaces - $cur_row['total_spaces']);
        }
        // Don't let available_spaces exceed total_spaces
        if ($available_spaces > $total_spaces) $available_spaces = $total_spaces;
        $stmt = $pdo->prepare("UPDATE parking_spaces SET space_number=?, vehicle_type=?, hourly_rate=?, status=?, image=?, location=?, total_spaces=?, available_spaces=? WHERE id=?");
        if ($stmt->execute([$space_number, $vehicle_type, $hourly_rate, $status, $image, $location, $total_spaces, $available_spaces, $id])) {
            $message = 'Space updated!';
        } else {
            $message = 'Failed to update space!';
        }
    }
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM parking_spaces WHERE id=?")->execute([$id]);
    $message = 'Space deleted!';
}
// Fetch all spaces
$spaces = $pdo->query("SELECT * FROM parking_spaces ORDER BY space_number")->fetchAll();
// For edit form
$edit_space = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM parking_spaces WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $edit_space = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Spaces - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f8; }
        .btn-primary { background: #1976d2; border: none; }
        .btn-success { background: #43a047; border: none; }
        .btn-danger { background: #b71c1c; border: none; }
        .img-thumb { width: 60px; height: 40px; object-fit: cover; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Manage Parking Spaces</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary">Dashboard</a>
    </div>
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-md-5">
            <h5><?= $edit_space ? 'Edit Space' : 'Add New Space' ?></h5>
            <form method="post" enctype="multipart/form-data">
                <?php if ($edit_space): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_space['id'] ?>">
                <?php endif; ?>
                <div class="mb-2">
                    <input type="text" name="space_number" class="form-control" placeholder="Space Number" value="<?= $edit_space['space_number'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <select name="vehicle_type" class="form-select" required>
                        <option value="two-wheeler" <?= (isset($edit_space) && $edit_space['vehicle_type']=='two-wheeler')?'selected':'' ?>>Two-wheeler</option>
                        <option value="four-wheeler" <?= (isset($edit_space) && $edit_space['vehicle_type']=='four-wheeler')?'selected':'' ?>>Four-wheeler</option>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="number" name="hourly_rate" class="form-control" placeholder="Hourly Rate" value="<?= $edit_space['hourly_rate'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="location" class="form-control" placeholder="Location" value="<?= $edit_space['location'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="file" name="image" class="form-control">
                    <?php if ($edit_space && $edit_space['image']): ?>
                        <img src="<?= $edit_space['image'] ?>" class="img-thumb mt-1">
                        <input type="hidden" name="existing_image" value="<?= $edit_space['image'] ?>">
                    <?php else: ?>
                        <input type="hidden" name="existing_image" value="">
                    <?php endif; ?>
                </div>
                <div class="mb-2">
                    <input type="number" name="total_spaces" class="form-control" placeholder="Total Spaces" min="1" value="<?= $edit_space['total_spaces'] ?? 1 ?>" required>
                </div>
                <?php if ($edit_space): ?>
                <div class="mb-2">
                    <select name="status" class="form-select" required>
                        <option value="available" <?= $edit_space['status']=='available'?'selected':'' ?>>Available</option>
                        <option value="reserved" <?= $edit_space['status']=='reserved'?'selected':'' ?>>Reserved</option>
                        <option value="occupied" <?= $edit_space['status']=='occupied'?'selected':'' ?>>Occupied</option>
                    </select>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-<?= $edit_space ? 'success' : 'primary' ?> w-100">
                    <?= $edit_space ? 'Update' : 'Add' ?> Space
                </button>
                <?php if ($edit_space): ?>
                    <a href="manage_spaces.php" class="btn btn-secondary w-100 mt-2">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-7">
            <h5>All Spaces</h5>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>Space</th>
                        <th>Type</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($spaces as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['space_number']) ?></td>
                        <td><?= ucfirst($s['vehicle_type']) ?></td>
                        <td>Ksh<?= $s['hourly_rate'] ?>/hr</td>
                        <td><?= ucfirst($s['status']) ?></td>
                        <td><?= htmlspecialchars($s['location']) ?></td>
                        <td><?php if ($s['image']): ?><img src="<?= $s['image'] ?>" class="img-thumb"><?php endif; ?></td>
                        <td>
                            <a href="manage_spaces.php?edit=<?= $s['id'] ?>" class="btn btn-success btn-sm">Edit</a>
                            <a href="manage_spaces.php?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this space?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
