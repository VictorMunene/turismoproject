<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
check_login();

// Ensure database connection is valid
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch filter parameters from GET request
$make = isset($_GET['make']) ? mysqli_real_escape_string($conn, $_GET['make']) : '';
$model = isset($_GET['model']) ? mysqli_real_escape_string($conn, $_GET['model']) : '';
$year = isset($_GET['year']) ? (int)$_GET['year'] : '';
$price = isset($_GET['price']) ? (float)$_GET['price'] : '';
$fuel = isset($_GET['fuel_type']) ? mysqli_real_escape_string($conn, $_GET['fuel_type']) : '';
$sort = isset($_GET['sortBy']) ? mysqli_real_escape_string($conn, $_GET['sortBy']) : 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Number of vehicles per page
$offset = ($page - 1) * $limit;

//UNION query 
$query = "
    SELECT v.id, NULL AS mv_number, NULL AS plate_number, NULL AS variant, v.mileage, 
           v.price, 0 AS status, v.model AS model, 
           IFNULL(v.fuel_type, 'Petrol') AS engine_type, 
           v.make AS brand, 
           IFNULL(v.car_type, 'Sedan') AS car_type, 
           v.year, 
           IFNULL(v.transmission, 'Automatic') AS transmission_type, 
           MIN(vp.photo_path) AS photo_path, 
           'vehicles' AS source
    FROM vehicles v
    LEFT JOIN vehicle_photos vp ON v.id = vp.vehicle_id
    WHERE 1=1
    GROUP BY v.id
    UNION
    SELECT v.id, v.mv_number, v.plate_number, v.variant, v.mileage, v.price, v.status, 
           m.model, m.engine_type, b.name AS brand, c.name AS car_type, v.year, 
           m.transmission_type, CONCAT('Uploads/vehicle_images/', v.id, '.jpg') AS photo_path, 'vehicle_list' AS source
    FROM vehicle_list v
    JOIN model_list m ON v.model_id = m.id
    JOIN brand_list b ON m.brand_id = b.id
    JOIN car_type_list c ON m.car_type_id = c.id
    WHERE v.delete_flag = 0 AND v.status = 0
";

$total_query = "SELECT COUNT(*) AS total FROM (
    SELECT v.id
    FROM vehicles v
    WHERE 1=1
    
    UNION
    
    SELECT v.id
    FROM vehicle_list v
    JOIN model_list m ON v.model_id = m.id
    WHERE v.delete_flag = 0 AND v.status = 0
) AS total_count";


// Apply filters
$conditions = [];
if ($make) {
    $conditions[] = "(brand = '$make')";
}
if ($model) {
    $conditions[] = "(model = '$model')";
}
if ($year) {
    $conditions[] = "(year = $year)";
}
if ($price) {
    $conditions[] = "(price <= $price)";
}
if ($fuel) {
    $conditions[] = "(engine_type = '$fuel_type' OR engine_type IS NULL)"; // Handle NULL for vehicles table
}

if (!empty($conditions)) {
    $query = "
        SELECT * FROM (
            $query
        ) AS combined
        WHERE " . implode(" AND ", $conditions);
} else {
    $query = "SELECT * FROM ($query) AS combined";
}

// Apply sorting
switch ($sort) {
    case 'price-high':
        $query .= " ORDER BY price DESC";
        break;
    case 'price-low':
        $query .= " ORDER BY price ASC";
        break;
    case 'mileage':
        $query .= " ORDER BY mileage ASC, price DESC"; // Fallback for NULL mileage
        break;
    case 'oldest':
        $query .= " ORDER BY year ASC, id ASC";
        break;
    default:
        $query .= " ORDER BY id DESC";
}

// Get total number of vehicles for pagination
$total_query = "SELECT COUNT(*) AS total FROM ($query) AS total_count";
$total_result = mysqli_query($conn, $total_query);
if (!$total_result) {
    die("Total query failed: " . mysqli_error($conn) . "<br>Query: $total_query");
}
$total_row = mysqli_fetch_assoc($total_result);
$total_vehicles = (int)$total_row['total'];
$total_pages = ceil($total_vehicles / $limit) ?: 1;

// Add pagination to query
$query .= " LIMIT $limit OFFSET $offset";

// Execute main query
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Main query failed: " . mysqli_error($conn) . "<br>Query: $query");
}
$vehicles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $vehicles[] = $row;
}

// Fetch brands for filter dropdown
$brand_query = "SELECT name FROM brand_list WHERE status = 1 AND delete_flag = 0";
$brand_result = mysqli_query($conn, $brand_query);
if (!$brand_result) {
    die("Brand query failed: " . mysqli_error($conn));
}
$brands = [];
while ($row = mysqli_fetch_assoc($brand_result)) {
    $brands[] = $row['name'];
}
// Add makes from vehicles table to brands
$vehicles_brand_query = "SELECT DISTINCT make FROM vehicles";
$vehicles_brand_result = mysqli_query($conn, $vehicles_brand_query);
while ($row = mysqli_fetch_assoc($vehicles_brand_result)) {
    if (!in_array($row['make'], $brands)) {
        $brands[] = $row['make'];
    }
}
sort($brands);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Vehicles - Turismo Motors</title>
    <link rel="stylesheet" href="style.css">
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
                    <li><a href="vehicles.php" class="active">Vehicles</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li class="phone"><a href="tel:+254708300400"><i class="fas fa-phone"></i> +254708300400</a></li>
                    <li><a class="login" href="login.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="vehicles-hero">
            <div class="container">
                <h1>Our Vehicle Inventory</h1>
                <p>Browse our extensive collection of quality pre-owned vehicles</p>
            </div>
        </section>

        <section class="vehicle-filters">
            <div class="container">
                <form id="filterForm" class="filter-form" method="GET">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="make">Make</label>
                            <select id="make" name="make">
                                <option value="">All Makes</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo htmlspecialchars($brand); ?>" <?php echo $make === $brand ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- <div class="filter-group">
                            <label for="model">Model</label>
                            <select id="model" name="model" disabled>
                                <option value="">All Models</option>
                            </select>
                        </div> -->
                        <div class="filter-group">
                            <label for="yearFilter">Year</label>
                            <div class="searchable-select">
                                <input type="text" id="yearFilter" name="year" placeholder="Type or select year" 
                                    value="<?php echo isset($year) && $year !== '' ? htmlspecialchars($year) : ''; ?>"
                                    autocomplete="off">
                                <div class="dropdown-arrow"><i class="fas fa-chevron-down"></i></div>
                                <div class="options-container" style="display: none;">
                                    <div class="option" data-value="">Any Year</div>
                                    <?php 
                                    $currentYear = date('Y');
                                    for ($y = $currentYear; $y >= $currentYear - 10; $y--) { 
                                        $selected = isset($year) && $year == $y ? 'selected' : '';
                                    ?>
                                        <div class="option" data-value="<?php echo $y; ?>" <?php echo $selected; ?>>
                                            <?php echo $y; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="filter-group">
                            <label for="price">Max Price (KES)</label>
                            <select id="price" name="price">
                                <option value="">No Limit</option>
                                <option value="1000000" <?php echo $price === 1000000 ? 'selected' : ''; ?>>1,000,000</option>
                                <option value="3000000" <?php echo $price === 3000000 ? 'selected' : ''; ?>>3,000,000</option>
                                <option value="5000000" <?php echo $price === 5000000 ? 'selected' : ''; ?>>5,000,000</option>
                                <option value="8000000" <?php echo $price === 8000000 ? 'selected' : ''; ?>>8,000,000</option>
                                <option value="10000000" <?php echo $price === 10000000 ? 'selected' : ''; ?>>10,000,000</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="fuel">Fuel Type</label>
                            <select id="fuel" name="fuel">
                                <option value="">All Types</option>
                                <option value="Petrol" <?php echo $fuel === 'Petrol' ? 'selected' : ''; ?>>Petrol</option>
                                <option value="Diesel" <?php echo $fuel === 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                                <option value="Hybrid" <?php echo $fuel === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                <option value="Electric" <?php echo $fuel === 'Electric' ? 'selected' : ''; ?>>Electric</option>
                            </select>
                        </div>
                        <button type="submit" class="btn primary">Search</button>
                        <button type="reset" class="btn secondary">Reset</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="vehicle-listings">
            <div class="container">
                <div class="sort-options">
                    <span>Sort by:</span>
                    <select id="sortBy" name="sortBy" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="price-high" <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="price-low" <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="mileage" <?php echo $sort === 'mileage' ? 'selected' : ''; ?>>Mileage</option>
                    </select>
                    <span class="results-count">Showing <span id="resultsCount"><?php echo count($vehicles); ?></span> vehicles</span>
                </div>

                <div class="vehicle-grid" id="vehicleGrid">
                    <?php foreach ($vehicles as $vehicle): ?>
                        <div class="vehicle-card">
                            <?php if ($vehicle['photo_path']): ?>
                                <img src="<?php echo htmlspecialchars($vehicle['photo_path']); ?>" alt="<?php echo htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model']); ?>">
                            <?php else: ?>
                                <img src="Uploads/vehicle_images/placeholder.jpg" alt="No Image">
                            <?php endif; ?>
                            <div class="vehicle-info">
                                <h3>
                                    <?php echo htmlspecialchars($vehicle['brand']); ?>
                                    <?php if (!empty($vehicle['model'])): ?>
                                        <span class="model"><?php echo htmlspecialchars($vehicle['model']); ?></span>
                                    <?php endif; ?>
                                </h3>
                                <p class="price">KES <?php echo number_format($vehicle['price'], 2); ?></p>
                                <ul class="vehicle-details">
                                    <li>
                                        <i class="fas fa-tachometer-alt"></i> 
                                        <?php echo !empty($vehicle['mileage']) ? number_format($vehicle['mileage']) . ' km' : 'N/A'; ?>
                                    </li>
                                    <li>
                                        <i class="fas fa-gas-pump"></i> 
                                        <?php echo !empty($vehicle['fuel_type']) ? htmlspecialchars($vehicle['fuel_type']) : 
                                            (!empty($vehicle['engine_type']) ? htmlspecialchars($vehicle['engine_type']) : 'N/A'); ?>
                                    </li>
                                    <li>
                                        <i class="fas fa-cogs"></i> 
                                        <?php echo !empty($vehicle['transmission']) ? htmlspecialchars($vehicle['transmission']) : 
                                            (!empty($vehicle['transmission_type']) ? htmlspecialchars($vehicle['transmission_type']) : 'N/A'); ?>
                                    </li>
                                </ul>
                                <a href="vehicle_details.php?id=<?php echo $vehicle['id']; ?>&source=<?php echo $vehicle['source']; ?>" class="btn primary">View Details</a>
                                <a href="contact.php"<?php echo $vehicle['id']; ?> class="btn btn-primary">Buy Now</a>  
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="pagination">
                    <a href="?page=<?php echo $page - 1; ?>&make=<?php echo urlencode($make); ?>&model=<?php echo urlencode($model); ?>&year=<?php echo $year; ?>&price=<?php echo $price; ?>&fuel=<?php echo urlencode($fuel); ?>&sortBy=<?php echo $sort; ?>" class="btn secondary <?php echo $page <= 1 ? 'disabled' : ''; ?>" id="prevPage"><i class="fas fa-chevron-left"></i> Previous</a>
                    <span class="page-numbers" id="pageNumbers">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                    <a href="?page=<?php echo $page + 1; ?>&make=<?php echo urlencode($make); ?>&model=<?php echo urlencode($model); ?>&year=<?php echo $year; ?>&price=<?php echo $price; ?>&fuel=<?php echo urlencode($fuel); ?>&sortBy=<?php echo $sort; ?>" class="btn secondary <?php echo $page >= $total_pages ? 'disabled' : ''; ?>" id="nextPage">Next <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </section>

        <section class="test-drive-cta">
            <div class="container">
                <h2>Interested in a Vehicle?</h2>
                <p>Schedule a test drive today and experience the vehicle for yourself.</p>
                <a href="contact.php" class="btn primary">Schedule Test Drive</a>
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

    <script src="script.js"></script>
    <script>
        // Set the current year in the footer
        document.getElementById('year').textContent = new Date().getFullYear();

        // Handle model dropdown population based on selected make
        document.getElementById('make').addEventListener('change', function() {
            const make = this.value;
            const modelSelect = document.getElementById('model');
            modelSelect.disabled = true;
            modelSelect.innerHTML = '<option value="">All Models</option>';

            if (make) {
                // Fetch models from both vehicle_list and vehicles tables
                fetch(`get_models.php?make=${encodeURIComponent(make)}`)
                    .then(response => response.json())
                    .then(data => {
                        // Include models from vehicles table
                        fetch(`get_vehicles_models.php?make=${encodeURIComponent(make)}`)
                            .then(response => response.json())
                            .then(vehiclesModels => {
                                const allModels = [...new Set([...data, ...vehiclesModels])];
                                allModels.forEach(model => {
                                    const option = document.createElement('option');
                                    option.value = model;
                                    option.textContent = model;
                                    modelSelect.appendChild(option);
                                });
                                modelSelect.disabled = false;
                            });
                    });
            }
        });

        // Handle form reset
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            window.location.href = 'vehicles.php';
        });

        document.addEventListener('DOMContentLoaded', function() {
    const searchableSelects = document.querySelectorAll('.searchable-select');
    
    searchableSelects.forEach(select => {
        const input = select.querySelector('input');
        const arrow = select.querySelector('.dropdown-arrow');
        const optionsContainer = select.querySelector('.options-container');
        const options = select.querySelectorAll('.option');
        
        // Toggle dropdown
        input.addEventListener('focus', () => {
            optionsContainer.style.display = 'block';
            optionsContainer.classList.add('show');
        });
        
        arrow.addEventListener('click', () => {
            optionsContainer.style.display = optionsContainer.style.display === 'none' ? 'block' : 'none';
            optionsContainer.classList.toggle('show');
        });
        
        // Select option
        options.forEach(option => {
            option.addEventListener('click', () => {
                input.value = option.textContent;
                options.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                optionsContainer.style.display = 'none';
                optionsContainer.classList.remove('show');
            });
        });
        
        // Filter options while typing
        input.addEventListener('input', () => {
            const searchTerm = input.value.toLowerCase();
            options.forEach(option => {
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
        
        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!select.contains(e.target)) {
                optionsContainer.style.display = 'none';
                optionsContainer.classList.remove('show');
            }
        });
    });
});
    </script>
</body>
</html>