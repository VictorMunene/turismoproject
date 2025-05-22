<?php
session_start();
require_once('includes/dbconnect.php');
require_once('includes/auth.php');
require_once('includes/checklogin.php');

$auth = new Auth($pdo);
check_login();

if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}
// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_vehicle'])) {
        $id = $_POST['id'];
        $is_sold = isset($_POST['is_sold']) ? 1 : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $sale_price = !empty($_POST['sale_price']) ? $_POST['sale_price'] : null;
        $sale_date = !empty($_POST['sale_date']) ? $_POST['sale_date'] : null;

        $stmt = $pdo->prepare("
            UPDATE vehicles 
            SET is_sold = ?, 
                is_active = ?,
                sale_price = ?,
                sale_date = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$is_sold, $is_active, $sale_price, $sale_date, $id]);
        
        $_SESSION['success'] = "Vehicle updated successfully!";
        header("Location: vehicles.php");
        exit;
    }
}

// Get all vehicles with their status
$vehicles = $pdo->query("
    SELECT 
        v.*,
        CONCAT(v.make, ' ', v.model) as full_name,
        IFNULL(u.username, 'System') as updated_by
    FROM vehicles v
    LEFT JOIN users u ON v.updated_by = u.id
    ORDER BY v.is_sold ASC, v.is_active DESC, v.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vehicles | Turismo Motors Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-available {
            background-color: #2ecc71;
            color: white;
        }
        .status-sold {
            background-color: #e74c3c;
            color: white;
        }
        .status-inactive {
            background-color: #95a5a6;
            color: white;
        }
        .vehicle-actions {
            display: flex;
            gap: 5px;
        }
        .edit-form {
            display: none;
            background: #f9f9f9;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .thumbnail {
            width: 80px;
            height: auto;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>  
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
            <h1>Vehicle Inventory Management</h1>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <div class="filters">
                <a href="vehicles.php?filter=all" class="btn">All Vehicles</a>
                <a href="vehicles.php?filter=available" class="btn">Available</a>
                <a href="vehicles.php?filter=sold" class="btn">Sold</a>
                <a href="vehicles.php?filter=inactive" class="btn">Inactive</a>
            </div>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Vehicle</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td>
                                <?php if (!empty($vehicle['image_path'])): ?>
                                    <img src="../<?= htmlspecialchars($vehicle['image_path']) ?>" 
                                         alt="<?= htmlspecialchars($vehicle['full_name']) ?>" 
                                         class="thumbnail">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($vehicle['full_name']) ?></strong><br>
                                <small><?= htmlspecialchars($vehicle['year']) ?></small>
                            </td>
                            <td>KES <?= number_format($vehicle['price'], 2) ?></td>
                            <td>
                                <?php if ($vehicle['is_sold']): ?>
                                    <span class="status-badge status-sold">Sold</span>
                                <?php elseif (!$vehicle['is_active']): ?>
                                    <span class="status-badge status-inactive">Inactive</span>
                                <?php else: ?>
                                    <span class="status-badge status-available">Available</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= date('M j, Y', strtotime($vehicle['updated_at'])) ?>
                                <?php if (!empty($vehicle['updated_by'])): ?>
                                    <br><small>by <?= htmlspecialchars($vehicle['updated_by']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary toggle-edit-form" 
                                        data-vehicle-id="<?= $vehicle['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                
                                <div class="edit-form" id="edit-form-<?= $vehicle['id'] ?>">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $vehicle['id'] ?>">
                                        
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_sold" 
                                                    <?= $vehicle['is_sold'] ? 'checked' : '' ?>>
                                                Mark as sold
                                            </label>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_active" 
                                                    <?= ($vehicle['is_active'] ?? 1) ? 'checked' : '' ?>>
                                                Active listing
                                            </label>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Sale Price (KES)</label>
                                            <input type="number" name="sale_price" 
                                                   value="<?= $vehicle['sale_price'] ?? $vehicle['price'] ?>" 
                                                   step="0.01" min="0">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Sale Date</label>
                                            <input type="date" name="sale_date" 
                                                   value="<?= $vehicle['sale_date'] ?? date('Y-m-d') ?>">
                                        </div>
                                        
                                        <button type="submit" name="update_vehicle" class="btn btn-success">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        // Toggle edit forms
        document.querySelectorAll('.toggle-edit-form').forEach(button => {
            button.addEventListener('click', function() {
                const formId = 'edit-form-' + this.dataset.vehicleId;
                const form = document.getElementById(formId);
                
                // Hide all other forms first
                document.querySelectorAll('.edit-form').forEach(f => {
                    if (f.id !== formId) f.style.display = 'none';
                });
                
                // Toggle current form
                form.style.display = form.style.display === 'block' ? 'none' : 'block';
            });
        });
    </script>
</body>
</html>