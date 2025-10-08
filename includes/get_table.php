<?php
require_once 'db.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$category = isset($_GET['category']) ? $_GET['category'] : '2023';

try {
    $stmt = $conn->prepare("SELECT month, water_level, casualties FROM monthly_water_data WHERE year = ? ORDER BY FIELD(month, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec')");
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'month' => $row['month'],
            'water_level' => $row['water_level'],
            'casualties' => $row['casualties']
        ];
    }
    
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>