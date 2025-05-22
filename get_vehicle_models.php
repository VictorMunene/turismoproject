<?php
include('includes/dbconnect.php');

$make = isset($_GET['make']) ? mysqli_real_escape_string($conn, $_GET['make']) : '';

$query = "SELECT DISTINCT model FROM vehicles WHERE make = '$make' ORDER BY model";
$result = mysqli_query($conn, $query);
$models = [];
while ($row = mysqli_fetch_assoc($result)) {
    $models[] = $row['model'];
}

header('Content-Type: application/json');
echo json_encode($models);
mysqli_close($conn);
?>