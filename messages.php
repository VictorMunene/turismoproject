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
    $messageId = $_GET['id'] ?? null;

    switch ($action) {
        case 'view':
            // Mark as read when viewing
            if ($messageId) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$messageId]);
                
                $message = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
                $message->execute([$messageId]);
                $messageDetails = $message->fetch(PDO::FETCH_ASSOC);
            }
            break;
            
        case 'delete':
            if ($messageId) {
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$messageId]);
                header('Location: messages.php');
                exit;
            }
            break;
            
        case 'mark_read':
            if ($messageId) {
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$messageId]);
                header('Location: messages.php');
                exit;
            }
            break;
    }
}

// Get all messages
$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Turismo Motors</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* [Same styles as in the dashboard] */
        .message-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message-details h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .message-info {
            margin-bottom: 15px;
        }
        .message-info p {
            margin: 5px 0;
        }
        .message-content {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #3498db;
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
                <h1>Messages Management</h1>
                <div class="user-info">
                    <span>Welcome, Administrator</span>
                </div>
            </header>

            <?php if (isset($messageDetails)): ?>
                <section class="message-details">
                    <h3>Message Details</h3>
                    <div class="message-info">
                        <p><strong>From:</strong> <?php echo htmlspecialchars($messageDetails['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($messageDetails['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($messageDetails['phone'] ?? 'N/A'); ?></p>
                        <p><strong>Subject:</strong> <?php echo htmlspecialchars($messageDetails['subject']); ?></p>
                        <p><strong>Received:</strong> <?php echo date('M j, Y g:i a', strtotime($messageDetails['created_at'])); ?></p>
                    </div>
                    <div class="message-content">
                        <p><?php echo nl2br(htmlspecialchars($messageDetails['message'])); ?></p>
                    </div>
                    <div class="action-buttons">
                        <a href="messages.php" class="btn btn-view">Back to Messages</a>
                        <a href="mailto:<?php echo htmlspecialchars($messageDetails['email']); ?>" class="btn btn-mark-read">Reply</a>
                        <a href="messages.php?action=delete&id=<?php echo $messageDetails['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                    </div>
                </section>
            <?php endif; ?>

            <section class="recent-section">
                <div class="section-header">
                    <h2>All Messages</h2>
                    <div class="filters">
                        <a href="messages.php?filter=unread" class="btn">Unread Only</a>
                        <a href="messages.php" class="btn">Show All</a>
                    </div>
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
                            <?php foreach ($messages as $message): ?>
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
                                        <a href="messages.php?action=mark_read&id=<?php echo $message['id']; ?>" class="btn btn-mark-read">Mark Read</a>
                                    <?php endif; ?>
                                    <a href="messages.php?action=delete&id=<?php echo $message['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>