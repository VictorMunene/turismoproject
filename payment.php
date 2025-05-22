<?php
session_start();
include('includes/dbconnect.php');
include('includes/checklogin.php');
check_login();

// Get vehicle details
$vehicle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM vehicles WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $vehicle_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$vehicle = mysqli_fetch_assoc($result);

if (!$vehicle) {
    header("Location: vehicles.php");
    exit();
}

// Get payment methods
$payment_methods = mysqli_query($conn, "SELECT * FROM payment_methods WHERE is_active = 1");

// Process payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = (int)$_POST['payment_method'];
    $amount = $vehicle['price'];
    $user_id = $_SESSION['user_id'];
    
    // Validate payment method
    $valid_method = false;
    while ($row = mysqli_fetch_assoc($payment_methods)) {
        if ($row['id'] == $payment_method) {
            $valid_method = true;
            break;
        }
    }
    
    if (!$valid_method) {
        $error = "Invalid payment method selected";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Create transaction record
            $query = "INSERT INTO transactions (user_id, vehicle_id, amount, payment_method_id, status) 
                      VALUES (?, ?, ?, ?, 'pending')";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'iidi', $user_id, $vehicle_id, $amount, $payment_method);
            mysqli_stmt_execute($stmt);
            $transaction_id = mysqli_insert_id($conn);
            
            // Process payment based on method
            switch($payment_method) {
                case 1: // Credit Card
                    // Process credit card payment (in a real app, this would connect to Stripe/PayPal/etc.)
                    $transaction_code = 'CC-' . time();
                    $status = 'completed';
                    break;
                    
                case 2: // PayPal
                    // Process PayPal payment
                    $transaction_code = 'PP-' . time();
                    $status = 'completed';
                    break;
                    
                case 3: // M-Pesa
                    // Process M-Pesa payment
                    $transaction_code = 'MP-' . time();
                    $status = 'completed';
                    break;
                    
                case 4: // Bank Transfer
                    $transaction_code = 'BT-' . time();
                    $status = 'pending';
                    break;
            }
            
            // Update transaction with payment details
            $query = "UPDATE transactions SET transaction_id = ?, status = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssi', $transaction_code, $status, $transaction_id);
            mysqli_stmt_execute($stmt);
            
            // Mark vehicle as sold
            $query = "UPDATE vehicles SET status = 'sold' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $vehicle_id);
            mysqli_stmt_execute($stmt);
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Redirect to success page
            header("Location: payment_success.php?id=" . $transaction_id);
            exit();
            
        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            $error = "Payment failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Purchase - Turismo Motors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="payment.css">
    <!-- Include Stripe.js for credit card processing -->
    <script src="https://js.stripe.com/v3/"></script>
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
        <div class="payment-container">
            <div class="payment-summary">
                <h2>Complete Your Purchase</h2>
                <div class="vehicle-summary">
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars(($vehicle['brand'] ?? $vehicle['make']) . ' ' . $vehicle['model']); ?>">
                    <div class="vehicle-details">
                        <h3><?php echo htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']); ?></h3>
                        <p>Year: <?php echo htmlspecialchars($vehicle['year']); ?></p>
                        <p>Price: <strong>KES <?php echo number_format($vehicle['price'], 2); ?></strong></p>
                    </div>
                </div>
            </div>

            <div class="payment-options">
                <h3>Select Payment Method</h3>
                
                <?php if (isset($error)): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form id="paymentForm" method="POST">
                    <div class="payment-methods">
                        <?php while ($method = mysqli_fetch_assoc($payment_methods)): ?>
                            <div class="payment-method">
                                <input type="radio" id="method<?php echo $method['id']; ?>" 
                                       name="payment_method" value="<?php echo $method['id']; ?>" required>
                                <label for="method<?php echo $method['id']; ?>">
                                    <i class="fas fa-<?php echo getPaymentMethodIcon($method['name']); ?>"></i>
                                    <?php echo htmlspecialchars($method['name']); ?>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Credit Card Form (shown when credit card is selected) -->
                    <div id="creditCardForm" class="payment-details" style="display:none;">
                        <div class="form-group">
                            <label for="cardNumber">Card Number</label>
                            <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" class="card-input">
                        </div>
                        <div class="form-group">
                            <label for="cardExpiry">Expiry Date</label>
                            <input type="text" id="cardExpiry" placeholder="MM/YY" class="card-input">
                        </div>
                        <div class="form-group">
                            <label for="cardCvc">CVC</label>
                            <input type="text" id="cardCvc" placeholder="123" class="card-input">
                        </div>
                        <div class="form-group">
                            <label for="cardName">Name on Card</label>
                            <input type="text" id="cardName" placeholder="John Doe" class="card-input">
                        </div>
                    </div>

                    <!-- PayPal Button -->
                    <div id="paypal-button-container" style="display:none;"></div>

                    <button type="submit" class="btn-btn-primary">Complete Purchase</button>
                </form>
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

        // Show/hide payment method details
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('creditCardForm').style.display = 'none';
                document.getElementById('paypal-button-container').style.display = 'none';
                
                if (this.value === '1') { // Credit Card
                    document.getElementById('creditCardForm').style.display = 'block';
                } else if (this.value === '2') { // PayPal
                    document.getElementById('paypal-button-container').style.display = 'block';
                    loadPayPalButton();
                }
            });
        });

        // Load PayPal button
        function loadPayPalButton() {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?php echo $vehicle["price"]; ?>',
                                currency_code: 'KES'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Submit form with PayPal details
                        const form = document.getElementById('paymentForm');
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'paypal_order_id';
                        input.value = details.id;
                        form.appendChild(input);
                        form.submit();
                    });
                }
            }).render('#paypal-button-container');
        }

        // Initialize Stripe Elements for credit card processing
        const stripe = Stripe('your_stripe_publishable_key');
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#cardNumber');

        // Handle form submission
        document.getElementById('paymentForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            if (paymentMethod === '1') { // Credit Card
                const {error, paymentMethod} = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: document.getElementById('cardName').value
                    }
                });

                if (error) {
                    alert(error.message);
                } else {
                    // Add payment method ID to form and submit
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'stripe_payment_method';
                    input.value = paymentMethod.id;
                    this.appendChild(input);
                    this.submit();
                }
            } else {
                this.submit();
            }
        });
    </script>
</body>
</html>

<?php
function getPaymentMethodIcon($methodName) {
    switch(strtolower($methodName)) {
        case 'credit card': return 'credit-card';
        case 'paypal': return 'paypal';
        case 'm-pesa': return 'mobile-alt';
        case 'bank transfer': return 'university';
        default: return 'money-bill-wave';
    }
}
?>