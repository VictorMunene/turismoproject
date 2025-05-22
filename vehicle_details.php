<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
check_login();

$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$source = isset($_GET['source']) ? $_GET['source'] : 'vehicle_list';

if ($source === 'vehicles') {
    // Query for vehicles table
    $query = "
        SELECT v.*, GROUP_CONCAT(vp.photo_path) AS photos
        FROM vehicles v
        LEFT JOIN vehicle_photos vp ON v.id = vp.vehicle_id
        WHERE v.id = ?
        GROUP BY v.id
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $vehicle_id);
} else {
    // Query for vehicle_list table
    $query = "
        SELECT v.*, m.model, m.engine_type, m.transmission_type, b.name AS brand, c.name AS car_type,
               CONCAT('Uploads/vehicle_images/', v.id, '.jpg') AS photos
        FROM vehicle_list v
        JOIN model_list m ON v.model_id = m.id
        JOIN brand_list b ON m.brand_id = b.id
        JOIN car_type_list c ON m.car_type_id = c.id
        WHERE v.id = ? AND v.delete_flag = 0
    ";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $vehicle_id);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vehicle = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if (!$vehicle) {
    die("Vehicle not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(($vehicle['brand'] ?? $vehicle['make']) . ' ' . $vehicle['model']); ?> - Turismo Motors</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="vehicle_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="vehicles.php">Vehicles</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li class="phone"><a href="tel:+254708300400"><i class="fas fa-phone"></i> +254708300400</a></li>
                    <li><a class="login" href="login.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="vehicle-details">
            <div class="container">
                <h1><?php echo htmlspecialchars(($vehicle['brand'] ?? $vehicle['make']) . ' ' . $vehicle['model']); ?></h1>
                <div class="vehicle-details-grid">
                    <div class="vehicle-image">
                        <?php if ($vehicle['photos']): ?>
                            <div class="photo-gallery">
                                <?php foreach (explode(',', $vehicle['photos']) as $photo): ?>
                                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars(($vehicle['brand'] ?? $vehicle['make']) . ' ' . $vehicle['model']); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <img src="Uploads/vehicle_images/placeholder.jpg" alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="vehicle-info">
                        
                        <ul>
                        <?php if ($source === 'vehicles'): ?>
                            <li><strong>Make:</strong> <?php echo htmlspecialchars($vehicle['make']); ?></li>
                            <li><strong>Model:</strong> <?php echo htmlspecialchars($vehicle['model']); ?></li>
                            <li><strong>Year:</strong> <?php echo $vehicle['year'] ?: 'N/A'; ?></li>
                            <li><strong>Description:</strong> <?php echo htmlspecialchars($vehicle['description'] ?: 'No description'); ?></li>
                            <li><strong>Fuel Type:</strong> <?php echo htmlspecialchars($vehicle['variant'] ?: 'N/A'); ?></li>
                            <li><strong>Mileage:</strong> <?php echo htmlspecialchars($vehicle['mileage'] ?: 'N/A'); ?> km</li>
                            <li><strong>Engine Capacity:</strong> <?php echo htmlspecialchars($vehicle['engine_number'] ?: 'N/A'); ?></li>
                            <li><strong>Transmission:</strong> <?php echo !empty($vehicle['transmission']) ? htmlspecialchars($vehicle['transmission']) : 
                            (!empty($vehicle['transmission_type']) ? htmlspecialchars($vehicle['transmission_type']) : 'N/A'); ?></li>
                            <li><strong>Car Type:</strong> <?php echo !empty($vehicle['car_type']) ? htmlspecialchars($vehicle['car_type']) : 
                            (!empty($vehicle['car_type']) ? htmlspecialchars($vehicle['car_type']) : 'N/A'); ?></li>
                            <?php else: ?>
                                <li><strong>Variant:</strong> <?php echo htmlspecialchars($vehicle['variant']); ?></li>
                                <li><strong>Mileage:</strong> <?php echo htmlspecialchars($vehicle['mileage']); ?> km</li>
                                <li><strong>Fuel Type:</strong> <?php echo htmlspecialchars($vehicle['engine_type']); ?></li>
                                <li><strong>Transmission:</strong> <?php echo htmlspecialchars($vehicle['transmission_type']); ?></li>
                                <li><strong>Car Type:</strong> <?php echo htmlspecialchars($vehicle['car_type']); ?></li>
                                <li><strong>MV Number:</strong> <?php echo htmlspecialchars($vehicle['mv_number']); ?></li>
                                <li><strong>Plate Number:</strong> <?php echo htmlspecialchars($vehicle['plate_number']); ?></li>
                                <li><strong>Engine Number:</strong> <?php echo htmlspecialchars($vehicle['engine_number']); ?></li>
                                <li><strong>Chassis Number:</strong> <?php echo htmlspecialchars($vehicle['chasis_number']); ?></li>
                            <?php endif; ?>
                        </ul>
                        <p class="price">KES <?php echo number_format($vehicle['price'], 2); ?></p>
                        <a href="contact.php" class="btn primary">Schedule Test Drive</a>
                        <a href="contact.php"<?php echo $vehicle['id']; ?> class="btn btn-primary">Buy Now</a> 
                    </div>
                </div>
            </div>
        </section>
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
                        <li><i class="fas fa-envelope"></i> turismo.motors.ltd@gmail.com</li>
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

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>