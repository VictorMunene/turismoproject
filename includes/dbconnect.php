<?php
$host = "localhost";
$username = "root";
$password = "root";
$database = "turismo_db";

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>

<?php

// includes/dbconnect.php
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=turismo_db',
        'root',
        'root',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    return $pdo; // Return the connection
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>