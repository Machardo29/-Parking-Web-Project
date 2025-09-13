<?php
session_start();
// owner_dashboard.php - Car Park Owner Dashboard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #e0e0e0 50%, #1976d2 40%, #43a047 10%); font-family: 'Montserrat', sans-serif; }
        .center-box { max-width: 600px; margin: 80px auto; border-radius: 18px; background: #fff; padding: 40px 32px; box-shadow: 0 4px 24px #1976d2aa; }
        .btn-primary { background: #1976d2; border: none; font-weight: 600; }
        .logo { font-size: 2.2rem; font-weight: 700; color: #1976d2; letter-spacing: 1px; margin-bottom: 18px; }
        .desc { color: #555; margin-bottom: 28px; }
        .dashboard-links .btn { margin-bottom: 16px; font-size: 1.1rem; }
    </style>
</head>
<body>
<div class="center-box text-center">
    <div class="logo">Car Park Owner Dashboard</div>
    <div class="desc">Welcome! Manage your parking spaces, reservations, and view reports below.</div>
    <div class="dashboard-links d-grid gap-2">
        <a href="owner_view_spaces.php" class="btn btn-outline-primary">View Parking Spaces</a>
        <a href="owner_manage_spaces.php" class="btn btn-outline-primary">Manage Parking Spaces</a>
        <a href="owner_view_reservations.php" class="btn btn-outline-primary">View Reservations</a>
        <a href="owner_add_reservation.php" class="btn btn-outline-primary">Add Reservation</a>
        <a href="owner_reports.php" class="btn btn-outline-primary">View Reports</a>
        <a href="logout.php" class="btn btn-primary w-100 mt-3">Logout</a>
    </div>
</div>
</body>
</html>
