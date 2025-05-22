<?php
session_start();

// Include files and get database connection
$pdo = include('includes/dbconnect.php'); // Get the PDO connection

include('includes/checklogin.php');
include('includes/auth.php');

// Initialize the Auth class with the PDO connection
$auth = new Auth($pdo);

// Check login and admin status
check_login();

// Redirect if not admin
if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

// Mark message as read if requested
if (isset($_GET['mark_as_read']) && is_numeric($_GET['mark_as_read'])) {
    $messageId = $_GET['mark_as_read'];
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $stmt->execute([$messageId]);
    header('Location: index.php');
    exit;
}

// Delete message if requested
if (isset($_GET['delete_message']) && is_numeric($_GET['delete_message'])) {
    $messageId = $_GET['delete_message'];
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$messageId]);
    header('Location: index.php');
    exit;
}

// Get counts for dashboard
$vehiclesCount = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$usersCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$messagesCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$testDrivesCount = $pdo->query("SELECT COUNT(*) FROM test_drives WHERE status = 'pending'")->fetchColumn();

// Get latest messages
$latestMessages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// Get latest test drive requests
$latestTestDrives = $pdo->query("
    SELECT t.*, v.make, v.model, u.name as user_name 
    FROM test_drives t
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Turismo Motors</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .unread {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-badge.confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge.cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-view {
            background-color: #3498db;
            color: white;
        }
        .btn-mark-read {
            background-color: #2ecc71;
            color: white;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Dashboard Overview</h1>
                <div class="user-info">
                    <span>Welcome, Administrator</span>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #3498db;">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $vehiclesCount; ?></h3>
                        <p>Total Vehicles</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #2ecc71;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $usersCount; ?></h3>
                        <p>Registered Users</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #e74c3c;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $messagesCount; ?></h3>
                        <p>New Messages</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #f39c12;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $testDrivesCount; ?></h3>
                        <p>Pending Test Drives</p>
                    </div>
                </div>
            </div>

            <!-- Recent Messages -->
            <section class="recent-section">
                <div class="section-header">
                    <h2>Recent Messages</h2>
                    <a href="messages.php" class="btn btn-view">View All Messages</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestMessages as $message): ?>
                            <tr class="<?php echo $message['is_read'] ? '' : 'unread'; ?>">
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <?php echo $message['is_read'] ? 'Read' : 'Unread'; ?>
                                </td>
                                <td class="action-buttons">
                                    <a href="messages.php?action=view&id=<?php echo $message['id']; ?>" class="btn btn-view">View</a>
                                    <?php if (!$message['is_read']): ?>
                                        <a href="index.php?mark_as_read=<?php echo $message['id']; ?>" class="btn btn-mark-read">Mark Read</a>
                                    <?php endif; ?>
                                    <a href="index.php?delete_message=<?php echo $message['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent Test Drives -->
            <section class="recent-section">
                <div class="section-header">
                    <h2>Recent Test Drive Requests</h2>
                    <a href="test-drives.php" class="btn btn-view">View All Requests</a>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Vehicle</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($latestTestDrives as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($request['make'] . ' ' . $request['model']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($request['requested_date'])); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $request['status']; ?>">
                                        <?php echo ucfirst($request['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="test-drives.php?action=view&id=<?php echo $request['id']; ?>" class="btn btn-view">View</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    
    <script>
        // Confirm before deleting
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this message?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>