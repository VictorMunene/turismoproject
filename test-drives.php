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

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $drive_id = $_GET['id'] ?? null;

    switch ($action) {
        case 'approve':
            if ($drive_id) {
                $stmt = $pdo->prepare("UPDATE test_drives SET status = 'approved' WHERE id = ?");
                $stmt->execute([$drive_id]);
                $_SESSION['drive_updated'] = "Test drive approved successfully";
            }
            break;
            
        case 'reject':
            if ($drive_id) {
                $stmt = $pdo->prepare("UPDATE test_drives SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$drive_id]);
                $_SESSION['drive_updated'] = "Test drive rejected";
            }
            break;
            
        case 'complete':
            if ($drive_id) {
                $stmt = $pdo->prepare("UPDATE test_drives SET status = 'completed' WHERE id = ?");
                $stmt->execute([$drive_id]);
                $_SESSION['drive_updated'] = "Test drive marked as completed";
            }
            break;
    }
    
    if ($drive_id) {
        header("Location: test-drives.php");
        exit;
    }
}

// Display success message
if (isset($_SESSION['drive_updated'])) {
    $success = $_SESSION['drive_updated'];
    unset($_SESSION['drive_updated']);
}

// Get test drives
$testDrives = $pdo->query("
    SELECT t.*, v.make, v.model, u.name as user_name, u.email as user_email, u.phone as user_phone
    FROM test_drives t
    JOIN vehicles v ON t.vehicle_id = v.id
    JOIN users u ON t.user_id = u.id
    ORDER BY t.requested_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Filter by status if requested
$status_filter = $_GET['status'] ?? 'all';
if (in_array($status_filter, ['pending', 'approved', 'rejected', 'completed'])) {
    $testDrives = array_filter($testDrives, function($drive) use ($status_filter) {
        return $drive['status'] === $status_filter;
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Drives | Turismo Motors Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        .status-badge.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-badge.approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-badge.completed {
            background-color: #cce5ff;
            color: #004085;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters a {
            margin-right: 10px;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
            background: #f1f1f1;
            color: #333;
        }
        .filters a.active {
            background: #3498db;
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
        <main class="main-content">
            <header class="header">
                <h1>Test Drive Management</h1>
                <div class="user-info">
                    <span>Welcome, Administrator</span>
                </div>
            </header>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <div class="filters">
                <strong>Filter by status:</strong>
                <a href="test-drives.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">All</a>
                <a href="test-drives.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">Pending</a>
                <a href="test-drives.php?status=approved" class="<?php echo $status_filter === 'approved' ? 'active' : ''; ?>">Approved</a>
                <a href="test-drives.php?status=rejected" class="<?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">Rejected</a>
                <a href="test-drives.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">Completed</a>
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
                        <?php foreach ($testDrives as $drive): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($drive['user_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($drive['user_email']); ?></small><br>
                                <small><?php echo htmlspecialchars($drive['user_phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($drive['make'] . ' ' . $drive['model']); ?></td>
                            <td><?php echo date('M j, Y g:i a', strtotime($drive['requested_date'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo $drive['status']; ?>">
                                    <?php echo ucfirst($drive['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($drive['status'] === 'pending'): ?>
                                        <a href="test-drives.php?action=approve&id=<?php echo $drive['id']; ?>" class="btn btn-success">Approve</a>
                                        <a href="test-drives.php?action=reject&id=<?php echo $drive['id']; ?>" class="btn btn-danger">Reject</a>
                                    <?php elseif ($drive['status'] === 'approved'): ?>
                                        <a href="test-drives.php?action=complete&id=<?php echo $drive['id']; ?>" class="btn btn-primary">Complete</a>
                                    <?php endif; ?>
                                    <a href="test-drive-details.php?id=<?php echo $drive['id']; ?>" class="btn">Details</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>