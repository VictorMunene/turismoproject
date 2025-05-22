
<?php
session_start();
include('includes/dbconnect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Turismo Motors</title>
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
                    <li><a href="about.php" class="active">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li class="phone"><a href="tel:+254708300400"><i class="fas fa-phone"></i> +254708300400</a></li>
                    <li><a class="login" href="login.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="about-hero">
            <div class="container">
                <h1>About Turismo Motors</h1>
                <p class="lead">Kenya's premier destination for quality pre-owned vehicles, offering an unmatched selection and exceptional service.</p>
            </div>
        </section>

        <section class="our-story">
            <div class="container">
                <h2>Our Story</h2>
                <p>Turismo Motors began with a simple mission: to provide Kenyans with access to high-quality vehicles at fair prices. What started as a small dealership with few cars has grown into one of the most trusted names in Kenya's automotive industry.</p>
                <p>Over the years, we've built a reputation for integrity, quality, and exceptional customer service. Our team of automotive experts personally inspects each vehicle to ensure it meets our rigorous standards before it reaches our showroom floor.</p>
                <p>Today, Turismo Motors offers a huge number of premium vehicles from top manufacturers around the world, along with financing solutions and after-sales support to ensure complete customer satisfaction.</p>
            </div>
        </section>

        <section class="core-values">
            <div class="container">
                <h2>Our Core Values</h2>
                <p class="subtitle">At Turismo Motors, we operate with a set of principles that guide everything we do.</p>
                
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Integrity</h3>
                        <p>We operate with complete transparency, providing accurate information about every vehicle we sell.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-star"></i>
                        <h3>Quality</h3>
                        <p>We maintain the highest standards for every vehicle in our inventory, ensuring reliability and value.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-users"></i>
                        <h3>Customer-First</h3>
                        <p>We prioritize our customers' needs and satisfaction above all else, providing personalized service.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-trophy"></i>
                        <h3>Excellence</h3>
                        <p>We strive for excellence in every aspect of our business, from vehicle selection to after-sales support.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="visit-showroom">
            <div class="container">
                <div class="showroom-grid">
                    <div class="showroom-info">
                        <h2>Visit Our Showroom</h2>
                        <p>We invite you to visit our state-of-the-art showroom in Nairobi to explore our extensive inventory of premium vehicles. Our friendly staff is ready to assist you in finding your perfect match and answer any questions you may have.</p>
                        
                        <ul class="showroom-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Address</strong>
                                    <p>Ole dume road, off Ngong Road, Nairobi</p>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Phone</strong>
                                    <p>+254708300400</p>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email</strong>
                                    <p>turismo.motors.ltd@gmail.com</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="showroom-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.854345425801!2d36.79221431575383!3d-1.2656355990788515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f173c0a1f9de7%3A0x1d5e6e9e9e9e9e9e!2sOle%20dume%20road%2C%20off%20Ngong%20Road%2C%20Nairobi!5e0!3m2!1sen!2ske!4v1620000000000!5m2!1sen!2ske" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
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
                        <li>SUVs<li>
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
    </script>html
</body>
</html>