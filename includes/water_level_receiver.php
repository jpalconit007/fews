<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Get raw POST data
$content = file_get_contents("php://input");
parse_str($content, $data);

// Validate input - less strict validation since column is VARCHAR
if (!isset($data['water_level'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing water_level parameter"
    ]);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fews";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Trim and sanitize the input
    $water_level = substr(trim($data['water_level']), 0, 100); // Limit to reasonable length
    
    $stmt = $conn->prepare("INSERT INTO sensor_readings (water_level, reading_time) VALUES (:water_level, NOW())");
    $stmt->bindParam(':water_level', $water_level, PDO::PARAM_STR);
    $stmt->execute();

    echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully",
        "data" => [
            "water_level" => $water_level,
            "reading_time" => date('Y-m-d H:i:s')
        ]
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>