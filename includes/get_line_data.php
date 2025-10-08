<?php
include 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$category = isset($_GET['category']) ? $_GET['category'] : '2023';

$value_column = ($category == '2024') ? 'casualties' : 'water_level';

$sql = "SELECT month AS label, $value_column AS line_value 
        FROM monthly_water_data 
        WHERE year = ? 
        ORDER BY FIELD(month, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun')";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $year);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>