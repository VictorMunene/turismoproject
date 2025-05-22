<?php
session_start();
session_unset(); // clear old session data

// Set only admin-specific session data


$pdo = include('includes/dbconnect.php');
require_once('includes/auth.php');
$auth = new Auth($pdo);



// Check if the user is already logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check admin credentials
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_admin = 1");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header('Location: admin.php');
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login | Turismo Motors</title>
        <link rel="stylesheet" href="admin_login.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
    <body>
        <div class="admin-login-container">
            <div class="admin-login-box">
                <div class="logo">
                    <h2>Turismo Motors</h2>
                    <small>Admin Portal</small>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-admin-login">Login</button>
                </form>
            </div>
        </div>
    </body>
</html>