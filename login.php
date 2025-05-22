<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include('includes/dbconnect.php');

    // Handle Login
    if (isset($_POST['email']) && isset($_POST['password']) && !isset($_POST['name'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Use prepared statement to prevent SQL injection
        $query = "SELECT id, password FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        
        if (!$stmt) {
            $error = "Database error: " . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                
                // Verify password
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Handle Registration
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate passwords match
        if ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } else {
            // Use email as username for consistency
            $username = $email;
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database using prepared statement
            $query = "INSERT INTO users (name, username, email, phone, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sssss', $name, $username, $email, $phone, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                header("Location: index.php");
                exit();
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Turismo Motors</title>
    <!-- Google Fonts for Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Link to style.css -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <img src="assets/images/logo.png" alt="Turismo Motors" class="logo">
            <h1>Welcome Back</h1>
            <p>Sign in to access your account</p>
        </div>

        <div class="auth-tabs">
            <button class="tab-btn active" data-tab="login">Login</button>
            <button class="tab-btn" data-tab="register">Register</button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="auth-form active" data-form="login" method="POST">
            <div class="form-group">
                <label for="loginEmail">Email</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="loginEmail" name="email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="loginPassword" name="password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
            </div>
            
            <div class="form-options">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="forgot-password.html">Forgot password?</a>
            </div>
            
            <?php if (isset($error) && !isset($_POST['name'])) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
            <button type="submit" class="btn btn-primary">Sign In</button>
            
            <div class="auth-footer">
                Don't have an account? <a href="#" class="switch-tab" data-tab="register">Sign up</a>
            </div>
            <a href="admin_login.php" class="admin-login-link" style="
                display: inline-flex;
                align-items: center;
                padding: 8px 16px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-size: 14px;
                font-weight: 500;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                gap: 8px;
                ">
                <i class="fas fa-lock"></i>
                <span>Admin Access</span>
            </a>
        </form>

        <!-- Registration Form -->
        <form id="registerForm" class="auth-form" data-form="register" method="POST">
            <div class="form-group">
                <label for="regName">Full Name</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="regName" name="name" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="regEmail">Email (will be used as your username)</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="regEmail" name="email" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="regPhone">Phone Number</label>
                <div class="input-icon">
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="regPhone" name="phone" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="regPassword">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="regPassword" name="password" required>
                    <i class="fas fa-eye toggle-password"></i>
                </div>
                <div class="password-strength">
                    <span class="strength-bar"></span>
                    <span class="strength-bar"></span>
                    <span class="strength-bar"></span>
                    <span class="strength-text">Weak</span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="regConfirmPassword">Confirm Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="regConfirmPassword" name="confirm_password" required>
                </div>
            </div>
            
            <div class="form-group checkbox">
                <input type="checkbox" id="agreeTerms" name="terms" required>
                <label for="agreeTerms">I agree to the Terms of Service and Privacy Policy</label>
            </div>
            
            <?php if (isset($error) && isset($_POST['name'])) echo "<p style='color:red; font-size:14px;'>$error</p>"; ?>
            <button type="submit" class="btn btn-primary">Create Account</button>
            
            <div class="auth-footer">
                Already have an account? <a href="#" class="switch-tab" data-tab="login">Sign in</a>
            </div>

        </form>
    </div>
    <!-- Link to auth.js -->
    <script src="auth.js"></script>
</body>
</html>