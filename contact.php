<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
check_login();

// Process form submission
// In your form processing section:
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['fullName'] ?? '';
        $email = $_POST['email'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $error = "Please fill in all required fields";
        } else {
            try {
                // Modified to match your actual table columns
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);
                
                $success = "Your message has been sent successfully!";
                $_POST = array(); // Clear form fields
            } catch (PDOException $e) {
                $error = "Error sending message: " . $e->getMessage();
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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
                    <li><a href="vehicles.php">Vehicles</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="contact.php" class="active">Contact</a></li>
                    <li class="phone"><a href="tel: +254708300400"><i class="fas fa-phone"></i> +254708300400</a></li>
                    <li><a class="login" href="login.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="contact-hero">
            <div class="container">
                <h1>Contact Us</h1>
                <p>We're here to help you find your perfect vehicle. Get in touch with our team.</p>
            </div>
        </section>

        <section class="contact-content">
            <div class="container">
            <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="contact-grid">
                    <div class="contact-info">
                        <h2>Contact Information</h2>
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Our Location</strong>
                                    <p>Ole dume road, off Ngong Road, Nairobi</p>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Phone Number</strong>
                                    <p>+254708300400</p>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email Address</strong>
                                    <p>turismo.motors.ltd@gmail.com</p>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Business Hours</strong>
                                    <p>Monday - Friday: 8:00 AM - 5:00 PM<br>
                                    Saturday: 9:00 AM - 4:00 PM<br>
                                    Sunday: Closed</p>
                                </div>
                            </li>
                        </ul>

                        <div class="map-section">
                            <h3><i class="fas fa-map-marked-alt"></i> Ngong Rd</h3>
                            <ul class="map-info">
                                <li>Nairobi</li>
                                <li><a href="https://maps.app.goo.gl/MLvQvukzZfmYzQMCA">View larger map</a></li>
                                <li>Past Prestage Plaza Shopping Mall</li>
                                <li>Ole Dume Road</li>
                            </ul>
                        </div>
                    </div>

                    <div class="contact-form">
                        <h2>Send Us a Message</h2>
                        <form id="messageForm" method="POST" action="contact.php">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" 
                                           value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" id="email" name="email" placeholder="Enter your email address" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <select id="subject" name="subject" required>
                                    <option value="" disabled selected>Select a subject</option>
                                    <option value="general" <?php echo (($_POST['subject'] ?? '') === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="vehicle" <?php echo (($_POST['subject'] ?? '') === 'vehicle') ? 'selected' : ''; ?>>Vehicle Inquiry</option>
                                    <option value="test-drive" <?php echo (($_POST['subject'] ?? '') === 'test-drive') ? 'selected' : ''; ?>>Test Drive Request</option>
                                    <option value="finance" <?php echo (($_POST['subject'] ?? '') === 'finance') ? 'selected' : ''; ?>>Financing Options</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" placeholder="Enter your message here" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn primary"><i class="fas fa-paper-plane"></i> Send Message</button>
                        </form>
                    </div>
                </div>

                <div class="specific-vehicle">
                    <h2>Looking for a Specific Vehicle?</h2>
                    <p>If you're searching for a particular make or model that's not in our current inventory, let us know. Our team can help source your dream car.</p>
                    <div class="vehicle-cta">
                        <a href="vehicles.php" class="btn primary">View Inventory</a>
                        <a href= "mailto:turismo.motors.ltd@gmail.com" class="btn secondary">Special Requests</a>
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