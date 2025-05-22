<?php
function check_login() {
    // Check if the user is logged in by verifying the session variable
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit(); // Stop script execution after redirect
    }
}
?>