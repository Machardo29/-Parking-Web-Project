<?php
session_start();
require_once __DIR__ . '/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin_dashboard.php');
            exit;
        case 'owner':
            header('Location: owner_dashboard.php');
            exit;
        default:
            header('Location: user_dashboard.php');
            exit;
    }
}

$message = '';
$selected_role = $_POST['role'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            if ($role && $user['role'] !== $role) {
                $message = 'Role does not match account.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                switch ($user['role']) {
                    case 'admin':
                        header('Location: admin_dashboard.php');
                        break;
                    case 'owner':
                        header('Location: owner_dashboard.php');
                        break;
                    default:
                        header('Location: user_dashboard.php');
                        break;
                }
                exit();
            }
        } else {
            $message = 'Invalid credentials.';
        }
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Car Parking System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card { 
            max-width: 400px; 
            margin: 80px auto; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: none;
        }
        .card-header {
            background: linear-gradient(45deg, #1976d2, #42a5f5);
            color: white;
            border-radius: 15px 15px 0 0;
            text-align: center;
            padding: 20px;
        }
        .btn-primary { 
            background: linear-gradient(45deg, #1976d2, #42a5f5);
            border: none; 
            border-radius: 25px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.4);
        }
        .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #1976d2;
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
        }
        .alert {
            border-radius: 15px;
            border: none;
        }
    </style>
</head>
<body>
<div class="card shadow">
    <div class="card-header">
        <h3 class="mb-0">🚗 Car Parking Login</h3>
    </div>
    <div class="card-body p-4">
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">✅ Registration successful! Please login.</div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-danger">❌ <?= $message ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="👤 Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="🔒 Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            <div class="text-center">
                <a href="register.php" class="text-decoration-none">Don't have an account? Register</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
