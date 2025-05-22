<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
check_login();

$transaction_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get transaction details
$query = "SELECT t.*, v.make, v.model, v.year, v.price, p.name as payment_method 
          FROM transactions t
          JOIN vehicles v ON t.vehicle_id = v.id
          JOIN payment_methods p ON t.payment_method_id = p.id
          WHERE t.id = ? AND t.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $transaction_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$transaction = mysqli_fetch_assoc($stmt);

if (!$transaction) {
    header("Location: vehicles.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Turismo Motors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
            <div class="container">
                <div class="logo">
                    <span>Turismo Motors</span>
                    <small>Your trusted car dealership</small>
                </div>
                <nav>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="vehicles.php" class="active">Vehicles</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li class="phone"><a href="tel:+254708300400"><i class="fas fa-phone"></i> +254708300400</a></li>
                        <li><a class="login" href="login.php">Log Out</a></li>
                    </ul>
                </nav>
            </div>
        </header>
    <main class="container">
        <div class="payment-success">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2>Payment Successful!</h2>
            <p>Thank you for your purchase. Your transaction has been completed.</p>
            
            <div class="transaction-details">
                <h3>Transaction Details</h3>
                <div class="detail-row">
                    <span>Vehicle:</span>
                    <span><?php echo htmlspecialchars($transaction['make'] . ' ' . $transaction['model'] . ' (' . $transaction['year'] . ')'); ?></span>
                </div>
                <div class="detail-row">
                    <span>Amount Paid:</span>
                    <span>KES <?php echo number_format($transaction['amount'], 2); ?></span>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <span><?php echo htmlspecialchars($transaction['payment_method']); ?></span>
                </div>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo htmlspecialchars($transaction['transaction_id']); ?></span>
                </div>
                <div class="detail-row">
                    <span>Date:</span>
                    <span><?php echo date('F j, Y, g:i a', strtotime($transaction['created_at'])); ?></span>
                </div>
            </div>
            
            <div class="actions">
                <a href="vehicles.php" class="btn btn-secondary">Browse More Vehicles</a>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
        </div>
    </main>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Turismo Motors</h3>
                    <p>Kenya's premier destination for quality pre-owned vehicles. Discover your perfect ride today.</p>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="vehicles.php">Vehicles</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Vehicle Categories</h3>
                    <ul>
                        <li>Sedans</li>
                        <li>SUVs</li>
                        <li>Trucks</li>
                        <li>Luxury Cars</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Ole dume road, off Ngong Road, Nairobi</li>
                        <li><i class="fas fa-phone"></i> +254708300400</li>
                        <li><i class="fas fa-envelope"></i>turismo.motors.ltd@gmail.com</li>
                    </ul>
                </div>
                <div class="social-links">
                <h3>Reach us on social media</h3>
                        <a href="https://x.com" target="https://x.com/turismoltd?s=21" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="https://www.instagram.com/turismo.motors.ltd?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="https://facebook.com" target="https://www.facebook.com/share/16ZQLNfnzg/?mibextid=wwXIfr" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://tiktok.com" target="https://www.tiktok.com/@turismo.motors.lt?_t=ZM-8vxuZ1qRIJR&_r=1" aria-label="LinkedIn"><i class="fa-brands fa-tiktok"></i></a>
                     </div>
            </div>
            <div class="copyright">
                <p>© <span id="year"></span> Turismo Motors. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Set the current year in the footer
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
   
</body>
</html>