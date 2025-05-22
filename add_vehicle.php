<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('includes/dbconnect.php');

    // Initialize all variables with default values using null coalescing operator
    $user_id = $_SESSION['user_id'];
    $make = $_POST['make'] ?? '';
    $model = $_POST['model'] ?? '';
    $year = isset($_POST['year']) ? (int)$_POST['year'] : 0;
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $description = $_POST['description'] ?? '';
    $mileage = isset($_POST['mileage']) && $_POST['mileage'] !== '' ? (int)$_POST['mileage'] : null;
    $variant = $_POST['variant'] ?? null;
    $engine_number = $_POST['engine_number'] ?? null;
    $fuel_type = $_POST['fuel_type'] ?? 'Petrol';
    $transmission = $_POST['transmission'] ?? 'Automatic';
    $car_type = $_POST['car_type'] ?? 'Sedan';

    // Server-side validation
    $errors = [];
    
    if (empty($make)) {
        $errors[] = "Make is required";
    }
    
    if (empty($model)) {
        $errors[] = "Model is required";
    }
    
    if ($year < 1900 || $year > date('Y') + 1) {
        $errors[] = "Please enter a valid year between 1900 and " . (date('Y') + 1);
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    
    if ($mileage !== null && $mileage < 0) {
        $errors[] = "Mileage cannot be negative";
    }

    if (empty($errors)) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Insert vehicle with all fields
            $query = "INSERT INTO vehicles (user_id, make, model, year, price, description, mileage, variant, engine_number, fuel_type, transmission, car_type) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'isssdsisssss', 
                $user_id, $make, $model, $year, $price, $description, 
                $mileage, $variant, $engine_number, $fuel_type, $transmission, $car_type);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to add vehicle: " . mysqli_error($conn));
            }
            
            $vehicle_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Handle photo uploads
            if (!empty($_FILES['photos']['name'][0])) {
                $upload_dir = 'uploads/vehicles/';
                // Create upload directory if it doesn't exist
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                foreach ($_FILES['photos']['name'] as $key => $name) {
                    if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['photos']['tmp_name'][$key];
                        $file_type = $_FILES['photos']['type'][$key];
                        $file_size = $_FILES['photos']['size'][$key];

                        // Validate file
                        if (!in_array($file_type, $allowed_types)) {
                            throw new Exception("Invalid file type for photo: $name. Only JPEG, PNG, and GIF are allowed.");
                        }
                        if ($file_size > $max_size) {
                            throw new Exception("File too large: $name. Maximum size is 5MB.");
                        }

                        // Generate unique filename
                        $file_ext = pathinfo($name, PATHINFO_EXTENSION);
                        $new_filename = uniqid() . '.' . $file_ext;
                        $destination = $upload_dir . $new_filename;

                        // Move uploaded file
                        if (!move_uploaded_file($tmp_name, $destination)) {
                            throw new Exception("Failed to upload photo: $name");
                        }

                        // Insert photo record
                        $photo_query = "INSERT INTO vehicle_photos (vehicle_id, photo_path) VALUES (?, ?)";
                        $photo_stmt = mysqli_prepare($conn, $photo_query);
                        mysqli_stmt_bind_param($photo_stmt, 'is', $vehicle_id, $destination);
                        
                        if (!mysqli_stmt_execute($photo_stmt)) {
                            throw new Exception("Failed to save photo record: " . mysqli_error($conn));
                        }
                        mysqli_stmt_close($photo_stmt);
                    }
                }
            }

            // Commit transaction
            mysqli_commit($conn);
            $_SESSION['success'] = "Vehicle and photos added successfully!";
            header("Location: vehicles.php");
            exit();
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - Turismo Motors</title>
    <!-- Google Fonts for Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Link to style.css -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="filters.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <img src="assets/images/logo.png" alt="Turismo Motors" class="logo">
            <h1>Add New Vehicle</h1>
            <p>Enter vehicle details below</p>
        </div>

        <form id="addVehicleForm" class="auth-form active" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="make">Make</label>
                <div class="input-icon">
                    <i class="fas fa-car"></i>
                    <input type="text" id="make" name="make" required>
                </div>
            </div>
            <div class="form-group">
                <label for="model">Model</label>
                <div class="input-icon">
                    <i class="fas fa-car-side"></i>
                    <input type="text" id="model" name="model" required>
                </div>
            </div>

            <div class="form-group">
                <label for="year">Year</label>
                <div class="input-icon">
                    <i class="fas fa-calendar"></i>
                    <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="price">Price (KES)</label>
                <div class="input-icon">
                    <i class="fa-solid fa-money-bill"></i>
                    <input type="number" id="price" name="price" step="1" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="mileage">Mileage (km, Optional)</label>
                <div class="input-icon">
                    <i class="fas fa-tachometer-alt"></i>
                    <input type="number" id="mileage" name="mileage" min="0" step="1">
                </div>
            </div>
<!-- 
            <div class="form-group">
                <label for="variant">Fuel Type</label>
                <div class="input-icon">
                    <i class="fas fa-tag"></i>
                    <input type="text" id="variant" name="variant">
                </div>
            </div> -->

            <div class="form-group">
                <label for="engine_number">Engine Capacity (Optional)</label>
                <div class="input-icon">
                    <i class="fas fa-cogs"></i>
                    <input type="text" id="engine_number" name="engine_number">
                </div>
            </div>

            <div class="form-group">
                <label for="fuel_type">Fuel Type</label>
                <div class="input-icon">
                    <i class="fas fa-gas-pump"></i>
                    <select id="fuel_type" name="fuel_type" required>
                        <option value="Petrol">Petrol</option>
                        <option value="Diesel">Diesel</option>
                        <option value="Hybrid">Hybrid</option>
                        <option value="Electric">Electric</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="transmission">Transmission</label>
                <div class="input-icon">
                    <i class="fas fa-cogs"></i>
                    <select id="transmission" name="transmission" required>
                        <option value="Automatic">Automatic</option>
                        <option value="Manual">Manual</option>
                        <option value="Semi-Automatic">Semi-Automatic</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="car_type">Vehicle Type</label>
                <div class="input-icon">
                    <i class="fas fa-car"></i>
                    <select id="car_type" name="car_type" required>
                        <option value="Motorcycle">Motorcycle</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Truck">Truck</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <div class="input-icon">
                    <i class="fas fa-comment"></i>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="photos">Vehicle Photos (Optional)</label>
                <div class="input-icon">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="photos" name="photos[]" accept="image/*" multiple>
                </div>
                <p class="form-hint">Accepted formats: JPEG, PNG, GIF. Max size: 5MB each. You can select multiple photos.</p>
            </div>

            <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
            <?php if (isset($success)) echo "<p class='success-message'>$success</p>"; ?>
            <button type="submit" class="btn btn-primary">Add Vehicle</button>

            <div class="auth-footer">
                <a href="index.php">Back to Dashboard</a>
            </div>
        </form>
    </div>
    <!-- Link to add_vehicle.js -->
    <script src="assets/add_vehicle.js"></script>
</body>
</html>