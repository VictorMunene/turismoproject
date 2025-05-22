<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
include('includes/auth.php');

$auth = new Auth($pdo);
check_login();

if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_settings'])) {
        // Process settings update
        $site_title = $_POST['site_title'];
        $admin_email = $_POST['admin_email'];
        $items_per_page = (int)$_POST['items_per_page'];
        
        // Validate and update settings
        if (!empty($site_title) && filter_var($admin_email, FILTER_VALIDATE_EMAIL) && $items_per_page > 0) {
            // In a real application, you would save these to a database
            $_SESSION['settings_updated'] = true;
        }
    } elseif (isset($_POST['change_password'])) {
        // Process password change
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password === $confirm_password) {
            $success = $auth->changePassword($_SESSION['user_id'], $current_password, $new_password);
            if ($success) {
                $_SESSION['password_changed'] = true;
            } else {
                $error = "Current password is incorrect";
            }
        } else {
            $error = "New passwords don't match";
        }
    }
}

// Display success messages
if (isset($_SESSION['settings_updated'])) {
    $success = "Settings updated successfully!";
    unset($_SESSION['settings_updated']);
}
if (isset($_SESSION['password_changed'])) {
    $success = "Password changed successfully!";
    unset($_SESSION['password_changed']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Turismo Motors Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .settings-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 1;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Turismo Motors</h2>
                <small>Admin Dashboard</small>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="adminvehicles.php"><i class="fas fa-car"></i> Vehicles</a></li>
                    <li><a href="reports.php"><i class="fas fa-users"></i> Reports</a></li>
                    <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                    <li><a href="test-drives.php"><i class="fas fa-calendar-check"></i> Test Drives</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <header class="header">
                <h1>System Settings</h1>
                <div class="user-info">
                    <span>Welcome, Administrator</span>
                </div>
            </header>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="settings-section">
                <h2>General Settings</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="site_title">Site Title</label>
                            <input type="text" id="site_title" name="site_title" value="Turismo Motors">
                        </div>
                        <div class="form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" id="admin_email" name="admin_email" value="admin@turismomotors.com">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="items_per_page">Items Per Page</label>
                            <select id="items_per_page" name="items_per_page">
                                <option value="10">10</option>
                                <option value="25" selected>25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="update_settings" class="btn primary">Save Settings</button>
                </form>
            </div>

            <div class="settings-section">
                <h2>Change Password</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn primary">Change Password</button>
                </form>
            </div>

            <div class="settings-section">
                <h2>System Information</h2>
                <div class="system-info">
                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Database:</strong> MySQL</p>
                    <p><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
                </div>
                <div class="action-buttons" style="margin-top: 20px;">
                    <a href="backup.php" class="btn primary"><i class="fas fa-database"></i> Create Backup</a>
                    <a href="system_logs.php" class="btn"><i class="fas fa-file-alt"></i> View Logs</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>