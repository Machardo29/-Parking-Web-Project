<?php
// owner_manage_spaces.php - Owner CRUD for their parking spaces
require_once 'includes/db.php';
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
$owner_id = $_SESSION['user_id'];
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
            $dir = 'assets/images';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $target = $dir . '/' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $target;
            }
        }
        $stmt = $pdo->prepare("INSERT INTO parking_spaces (space_number, vehicle_type, hourly_rate, status, image, location, owner_id, total_spaces, available_spaces) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$space_number, $vehicle_type, $hourly_rate, $status, $image, $location, $owner_id, $total_spaces, $available_spaces])) {
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
        $cur = $pdo->prepare("SELECT available_spaces, total_spaces FROM parking_spaces WHERE id=? AND owner_id=?");
        $cur->execute([$id, $owner_id]);
        $cur_row = $cur->fetch();
        $available_spaces = $cur_row ? $cur_row['available_spaces'] : $total_spaces;
        // If total_spaces increased, increase available_spaces by the same amount
        if ($cur_row && $total_spaces > $cur_row['total_spaces']) {
            $available_spaces += ($total_spaces - $cur_row['total_spaces']);
        }
        // Don't let available_spaces exceed total_spaces
        if ($available_spaces > $total_spaces) $available_spaces = $total_spaces;
        $stmt = $pdo->prepare("UPDATE parking_spaces SET space_number=?, vehicle_type=?, hourly_rate=?, status=?, image=?, location=?, total_spaces=?, available_spaces=? WHERE id=? AND owner_id=?");
        if ($stmt->execute([$space_number, $vehicle_type, $hourly_rate, $status, $image, $location, $total_spaces, $available_spaces, $id, $owner_id])) {
            $message = 'Space updated!';
        } else {
            $message = 'Failed to update space!';
        }
    }
}
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM parking_spaces WHERE id=? AND owner_id=?")->execute([$id, $owner_id]);
    $message = 'Space deleted!';
}
// Fetch all spaces for this owner
$spaces = $pdo->prepare("SELECT * FROM parking_spaces WHERE owner_id = ? ORDER BY space_number");
$spaces->execute([$owner_id]);
$spaces = $spaces->fetchAll();
// For edit form
$edit_space = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM parking_spaces WHERE id=? AND owner_id=?");
    $stmt->execute([$_GET['edit'], $owner_id]);
    $edit_space = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage My Spaces - Car Parking System</title>
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
        <h2>Manage My Parking Spaces</h2>
        <a href="owner_dashboard.php" class="btn btn-secondary">Dashboard</a>
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
                    <select name="vehicle_type" class="form-control" required>
                        <option value="two-wheeler" <?= (isset($edit_space) && $edit_space['vehicle_type'] == 'two-wheeler') ? 'selected' : '' ?>>Two-wheeler</option>
                        <option value="four-wheeler" <?= (isset($edit_space) && $edit_space['vehicle_type'] == 'four-wheeler') ? 'selected' : '' ?>>Four-wheeler</option>
                    </select>
                </div>
                <div class="mb-2">
                    <input type="number" step="0.01" name="hourly_rate" class="form-control" placeholder="Hourly Rate" value="<?= $edit_space['hourly_rate'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="location" class="form-control" placeholder="Location" value="<?= $edit_space['location'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="file" name="image" class="form-control">
                    <?php if (isset($edit_space) && $edit_space['image']): ?>
                        <input type="hidden" name="existing_image" value="<?= $edit_space['image'] ?>">
                        <img src="<?= $edit_space['image'] ?>" class="img-thumb mt-1">
                    <?php endif; ?>
                </div>
                <div class="mb-2">
                    <input type="number" name="total_spaces" class="form-control" placeholder="Total Spaces" min="1" value="<?= $edit_space['total_spaces'] ?? 1 ?>" required>
                </div>
                <?php if ($edit_space): ?>
                    <select name="status" class="form-control mb-2">
                        <option value="available" <?= $edit_space['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="reserved" <?= $edit_space['status'] == 'reserved' ? 'selected' : '' ?>>Reserved</option>
                        <option value="occupied" <?= $edit_space['status'] == 'occupied' ? 'selected' : '' ?>>Occupied</option>
                    </select>
                <?php endif; ?>
                <button type="submit" name="<?= $edit_space ? 'edit' : 'add' ?>" class="btn btn-success w-100 mt-2"><?= $edit_space ? 'Update' : 'Add' ?> Space</button>
            </form>
        </div>
        <div class="col-md-7">
            <h5>My Spaces</h5>
            <table class="table table-bordered bg-white">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Type</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($spaces as $space): ?>
                    <tr>
                        <td><?= htmlspecialchars($space['space_number']) ?></td>
                        <td><?= ucfirst($space['vehicle_type']) ?></td>
                        <td>Ksh<?= $space['hourly_rate'] ?>/hr</td>
                        <td><?= ucfirst($space['status']) ?></td>
                        <td><?= htmlspecialchars($space['location']) ?></td>
                        <td><?php if ($space['image']): ?><img src="<?= $space['image'] ?>" class="img-thumb"><?php endif; ?></td>
                        <td>
                            <a href="?edit=<?= $space['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="?delete=<?= $space['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this space?')">Delete</a>
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
