<?php
// index.php - Entry point for Car Parking System
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'owner') {
        header('Location: owner_dashboard.php');
        exit;
    } else {
        header('Location: user_dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap">
    <style>
        body { background: linear-gradient(135deg, #e0e0e0 50%, #1976d2 40%, #43a047 10%); font-family: 'Montserrat', sans-serif; }
        .center-box { max-width: 420px; margin: 80px auto; border-radius: 18px; background: #fff; padding: 40px 32px; box-shadow: 0 4px 24px #1976d2aa; }
        .btn-primary { background: #1976d2; border: none; font-weight: 600; }
        .btn-success { background: #43a047; border: none; font-weight: 600; }
        .logo { font-size: 2.2rem; font-weight: 700; color: #1976d2; letter-spacing: 1px; margin-bottom: 18px; }
        .desc { color: #555; margin-bottom: 28px; }
    </style>
</head>
<body>
<div class="center-box text-center">
    <div class="logo">Car Parking System</div>
    <div class="desc">A simple, modern parking management portal for your school.</div>
    <a href="login.php" class="btn btn-primary w-100 mb-3">Login</a>
    <a href="register.php" class="btn btn-success w-100">Register</a>
</div>
</body>
</html>
