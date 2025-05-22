<?php
include('includes/dbconnect.php');

$make = isset($_GET['make']) ? mysqli_real_escape_string($conn, $_GET['make']) : '';

$query = "
    SELECT DISTINCT m.model
    FROM model_list m
    JOIN brand_list b ON m.brand_id = b.id
    WHERE b.name = '$make' AND b.status = 1 AND b.delete_flag = 0
    ORDER BY m.model
";
$result = mysqli_query($conn, $query);
$models = [];
while ($row = mysqli_fetch_assoc($result)) {
    $models[] = $row['model'];
}

header('Content-Type: application/json');
echo json_encode($models);
mysqli_close($conn);
?>