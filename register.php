<?php
// register.php - User registration
require_once __DIR__ . '/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $role = isset($_POST['role']) && $_POST['role'] === 'owner' ? 'owner' : 'user';

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $message = 'Username already exists!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $password, $email, $full_name, $phone, $role])) {
            header('Location: login.php?registered=1');
            exit;
        } else {
            $message = 'Registration failed!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f0f2f5; }
        .card { max-width: 400px; margin: 40px auto; border-radius: 10px; }
        .btn-primary { background: #1976d2; border: none; }
    </style>
</head>
<body>
<div class="card shadow p-4">
    <h3 class="mb-3 text-center">Register</h3>
    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-2">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
        </div>
        <div class="mb-2">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-2">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-2">
            <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="mb-2">
            <input type="text" name="phone" class="form-control" placeholder="Phone" required>
        </div>
        <div class="mb-2">
            <select name="role" class="form-select" required>
                <option value="">Select Role</option>
                <option value="user">User</option>
                <option value="owner">Car Park Owner</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
        <div class="mt-2 text-center">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </form>
</div>
</body>
</html>
