<?php
include 'db.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$action = $_POST['action'] ?? '';
$year = $_POST['year'] ?? '';
$month = $_POST['month'] ?? '';
$water_level = $_POST['water_level'] ?? null;
$casualties = $_POST['casualties'] ?? null;

$response = ['status' => 'error', 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'save':
            // Check if record exists
            $check = $conn->prepare("SELECT id FROM monthly_water_data WHERE year = ? AND month = ?");
            $check->bind_param("is", $year, $month);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            
            if ($exists) {
                // Update existing record
                $stmt = $conn->prepare("UPDATE monthly_water_data SET water_level = ?, casualties = ? WHERE id = ?");
                $stmt->bind_param("dii", $water_level, $casualties, $exists['id']);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO monthly_water_data (year, month, water_level, casualties) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isdi", $year, $month, $water_level, $casualties);
            }
            
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Data saved successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to save data'];
            }
            break;
            
        case 'load':
            $stmt = $conn->prepare("SELECT * FROM monthly_water_data ORDER BY year DESC, FIELD(month, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun')");
            $stmt->execute();
            $result = $stmt->get_result();
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $response = ['status' => 'success', 'data' => $data];
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            $stmt = $conn->prepare("DELETE FROM monthly_water_data WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Data deleted successfully'];
            } else {
                $response = ['status' => 'error', 'message' => 'Failed to delete data'];
            }
            break;
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
$conn->close();
?>