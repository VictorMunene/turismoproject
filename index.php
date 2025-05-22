<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');

// Only check login if the current page is not the login page
if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    check_login();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turismo Motors</title>
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
                    <li><a href="#" class="active">Home</a></li>
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
        <section class="hero">
            <div class="container">
                <h1>Find Your Perfect Car in Kenya</h1>
                <p>Browse our extensive collection of premium vehicles at competitive prices. Quality and reliability guaranteed.</p>
                <div class="cta-buttons">
                <a href="add_vehicle.php" class="btn primary">Add New Vehicle</a>
                    <a href="vehicles.php" class="btn primary">Browse Inventory</a>
                    <a href="about.php" class="btn secondary">Learn More</a>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features">
            <div class="container">
                <div class="feature">
                    <h3>Wide Selection</h3>
                    <p>Browse through hundreds of quality vehicles of various makes and models.</p>
                </div>
                <div class="feature">
                    <h3>Quality Assured</h3>
                    <p>All our vehicles undergo rigorous inspection to ensure they meet the highest standards.</p>
                </div>
                <div class="feature">
                    <h3>Competitive Pricing</h3>
                    <p>We offer the best market rates in Kenya, with flexible financing options.</p>
                </div>
                <div class="feature">
                    <h3>Nationwide Delivery</h3>
                    <p>We can arrange delivery of your vehicle to any location within Kenya.</p>
                </div>
                <div class="feature">
                    <h3>Efficient Process</h3>
                    <p>Our streamlined buying process ensures a quick and hassle-free experience.</p>
                </div>
                <div class="feature">
                    <h3>Excellent Service</h3>
                    <p>Our dedicated team is committed to providing personalized service to every customer.</p>
                </div>
            </div>
        </section>
        
         <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="container">
                <div class="section-header">
                    <h2>What Our Customers Say</h2>
                    <p>Don't just take our word for it. Here's what customers who have purchased vehicles from Turismo Motors have to say.</p>
                </div>
                
                <div class="reviews-container" id="reviewsContainer">
                </div>
                
                <!-- Review Form -->
                <div class="review-form">
                    <h3 class="form-title">Share Your Experience</h3>
                    <form id="reviewForm">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Your Role</label>
                            <input type="text" id="role" name="role" placeholder="e.g. Business Owner" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="rating">Rating</label>
                            <div class="rating-input" id="ratingInput">
                                <i class="fas fa-star" data-value="1"></i>
                                <i class="fas fa-star" data-value="2"></i>
                                <i class="fas fa-star" data-value="3"></i>
                                <i class="fas fa-star" data-value="4"></i>
                                <i class="fas fa-star" data-value="5"></i>
                            </div>
                            <input type="hidden" id="rating" name="rating" value="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="review">Your Review</label>
                            <textarea id="review" name="review" required></textarea>
                        </div>
                        
                        <button type="submit" class="submit-btn">Submit Review</button>
                    </form>
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
                        <li><a href="#">Home</a></li>
                        <li><a href="vehicles.php">Vehicles</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
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
                // Sample Review Data
                const reviews = [
            {
                id: 1,
                name: "James Mwangi",
                role: "Business Owner",
                rating: 5,
                text: "I purchased a Toyota Land Cruiser from Turismo Motors and couldn't be happier with my experience. The staff was knowledgeable and helped me find exactly what I needed for my business.",
                date: "2025-04-15",
            },
            {
                id: 2,
                name: "Lexurs Musa",
                role: "Interior Designer",
                rating: 4,
                text: "Excellent service! Found my Toyota Hilux with their help. The financing options were clearly explained and the whole process was smooth.",
                date: "2025-03-28",
            },
            {
                id: 3,
                name: "Altaf Hussein",
                role: "Software Engineer",
                rating: 5,
                text: "As a first-time car buyer, I was nervous, but the team at Turismo Motors made it so easy. My Honda Civic is perfect for my commute!",
                date: "2025-04-05",
            }
        ];

        // Function to render stars based on rating
        function renderStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating 
                    ? '<i class="fas fa-star"></i>' 
                    : '<i class="far fa-star"></i>';
            }
            return stars;
        }

        // Function to render reviews
        function renderReviews() {
            const container = document.getElementById('reviewsContainer');
            container.innerHTML = '';
            
            reviews.forEach(review => {
                const reviewCard = document.createElement('div');
                reviewCard.className = 'review-card';
                reviewCard.innerHTML = `
                    <span class="review-date">${review.date}</span>
                    <div class="rating">
                        ${renderStars(review.rating)}
                    </div>
                    <p class="review-text">${review.text}</p>
                    <div class="client-info">
                        <div class="client-details">
                            <h4>${review.name}</h4>
                            <p>${review.role}</p>
                        </div>
                    </div>
                `;
                container.appendChild(reviewCard);
            });
        }

        // Rating input functionality
        function setupRatingInput() {
            const stars = document.querySelectorAll('#ratingInput i');
            const ratingInput = document.getElementById('rating');
            
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    ratingInput.value = value;
                    
                    stars.forEach((s, index) => {
                        if (index < value) {
                            s.classList.add('active');
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('active');
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });
        }

        // Form submission handler
        function setupForm() {
            const form = document.getElementById('reviewForm');
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const name = form.name.value.trim();
                const role = form.role.value.trim();
                const rating = parseInt(form.rating.value);
                const reviewText = form.review.value.trim();
                
                if (!name || !role || !rating || !reviewText) {
                    alert('Please fill all fields');
                    return;
                }
                
                // Generate random avatar (for demo purposes)
                const gender = Math.random() > 0.5 ? 'men' : 'women';
                const randomId = Math.floor(Math.random() * 100);
                
                // Create new review
                const newReview = {
                    id: reviews.length + 1,
                    name: name,
                    role: role,
                    rating: rating,
                    text: reviewText,
                    date: new Date().toISOString().split('T')[0],
                };
                
                // Add to beginning of array
                reviews.unshift(newReview);
                
                // Re-render reviews
                renderReviews();
                
                // Reset form
                form.reset();
                document.querySelectorAll('#ratingInput i').forEach(star => {
                    star.classList.remove('active', 'fas');
                    star.classList.add('far');
                });
                form.rating.value = 0;
                
                // Show success message
                alert('Thank you for your review!');
            });
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', () => {
            renderReviews();
            setupRatingInput();
            setupForm();
        });
        // Set the current year in the footer
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>